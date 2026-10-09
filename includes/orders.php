<?php
require_once __DIR__ . '/catalog.php';

function customer_order_receipt(string $number, int $userId): ?array
{
    if (!preg_match('/^DEMO-[A-F0-9]{16}$/D', $number)) return null;
    // An order number identifies a receipt; it never grants access to it.
    $query = db()->prepare('SELECT * FROM orders WHERE order_number = ? AND user_id = ?');
    $query->execute([$number, $userId]);
    $order = $query->fetch();
    if (!$order) return null;
    $query = db()->prepare('SELECT category, product_name, quantity, unit_price, line_total FROM order_items WHERE order_id = ? ORDER BY id');
    $query->execute([$order['id']]);
    $order['items'] = $query->fetchAll();
    return $order;
}

function receipt_money(string $amount, string $currency): string
{
    return $currency . ' ' . number_format((float) $amount, 2);
}

function order_status_update(int $id, string $next): void
{
    $allowed = ['pending'=>['processing','cancelled'],'processing'=>['completed','cancelled'],'completed'=>[],'cancelled'=>[]];
    $pdo = db(); $pdo->beginTransaction();
    try {
        $query = $pdo->prepare('SELECT status, stock_deducted_at FROM orders WHERE id = ? FOR UPDATE');
        $query->execute([$id]); $order = $query->fetch();
        if (!$order) throw new InvalidArgumentException('Order not found.');
        if ($order['status'] === $next) { $pdo->commit(); return; }
        if (!in_array($next,$allowed[$order['status']] ?? [],true)) throw new InvalidArgumentException('That status change is not allowed. Completed and cancelled orders are final.');
        if ($next === 'completed') {
            if ($order['stock_deducted_at'] !== null) throw new InvalidArgumentException('Stock has already been deducted for this order.');
            $query = $pdo->prepare('SELECT category, product_id, SUM(quantity) AS quantity FROM order_items WHERE order_id = ? GROUP BY category, product_id ORDER BY category, product_id');
            $query->execute([$id]); $items = $query->fetchAll();
            if (!$items) throw new InvalidArgumentException('An empty order cannot be completed.');
            foreach ($items as $item) {
                $type = component_type($item['category']);
                $quantity = (int)$item['quantity'];
                // Conditional updates lock stock and prevent concurrent overselling.
                $update = $pdo->prepare("UPDATE `$type` SET stock = stock - ? WHERE id = ? AND stock >= ?");
                $update->execute([$quantity,$item['product_id'],$quantity]);
                if ($update->rowCount() !== 1) throw new InvalidArgumentException('Insufficient or unknown stock for ' . component_categories()[$type] . ' #' . $item['product_id'] . '. Update inventory first.');
            }
        }
        $query = $pdo->prepare("UPDATE orders SET status = ?, stock_deducted_at = CASE WHEN ? = 'completed' THEN CURRENT_TIMESTAMP ELSE stock_deducted_at END WHERE id = ?");
        $query->execute([$next,$next,$id]);
        $pdo->commit();
    } catch (Throwable $error) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $error; }
}

function create_demo_order(array $cart, array $customer, int $userId): array
{
    if (!$cart) throw new InvalidArgumentException('Your cart is empty.');
    $pdo = db(); $pdo->beginTransaction();
    try {
        $quantities = [];
        foreach ($cart as $item) {
            $quantity = filter_var($item['quantity'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>99]]);
            if ($quantity === false || !is_array($item['parts'] ?? null) || !$item['parts']) throw new InvalidArgumentException('Invalid cart item.');
            foreach ($item['parts'] as $type=>$id) {
                $type = component_type($type);
                $id = filter_var($id,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
                if ($id === false) throw new InvalidArgumentException('Invalid component in cart.');
                $key = $type . ':' . $id;
                $quantities[$key] = ['type'=>$type,'id'=>$id,'quantity'=>($quantities[$key]['quantity'] ?? 0) + $quantity];
            }
        }
        ksort($quantities); $items = []; $total = 0;
        foreach ($quantities as $item) {
            $type = $item['type'];
            $query = $pdo->prepare("SELECT name, price, stock, status FROM `$type` WHERE id = ? FOR UPDATE");
            $query->execute([$item['id']]); $product = $query->fetch();
            if (!$product || $product['status'] !== 'active' || $product['price'] === null) throw new InvalidArgumentException('A cart component is no longer available.');
            if ($product['stock'] === null || (int)$product['stock'] < $item['quantity']) throw new InvalidArgumentException('Insufficient or unknown stock for ' . $product['name'] . '.');
            $price = price_cents($product['price']);
            $total += $price * $item['quantity'];
            $items[] = $item + ['name'=>$product['name'],'price'=>$price];
        }
        if ($total > 999999999999) throw new InvalidArgumentException('Order total is too large.');
        $number = 'DEMO-' . strtoupper(bin2hex(random_bytes(8)));
        $currency = store_settings()['currency'];
        $query = $pdo->prepare('INSERT INTO orders (order_number,user_id,customer_name,customer_email,shipping_address,shipping_city,shipping_postal_code,shipping_phone,shipping_region,shipping_country,subtotal,total,currency) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $query->execute([$number,$userId,$customer['name'],$customer['email'],$customer['address'],$customer['city'],$customer['postal_code'],$customer['phone'] ?? '',$customer['region'] ?? '',$customer['country'] ?? '',cents_decimal($total),cents_decimal($total),$currency]);
        $orderId = (int)$pdo->lastInsertId();
        $query = $pdo->prepare('INSERT INTO order_items (order_id,category,product_id,product_name,quantity,unit_price,line_total) VALUES (?,?,?,?,?,?,?)');
        foreach ($items as $item) $query->execute([$orderId,$item['type'],$item['id'],$item['name'],$item['quantity'],cents_decimal($item['price']),cents_decimal($item['price']*$item['quantity'])]);
        $pdo->commit();
        return ['number'=>$number,'name'=>$customer['name'],'email'=>$customer['email'],'total'=>$currency . ' ' . number_format($total / 100,2),'item_count'=>array_sum(array_column($items,'quantity')),'created_at'=>date('Y-m-d H:i:s')];
    } catch (Throwable $error) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $error; }
}
