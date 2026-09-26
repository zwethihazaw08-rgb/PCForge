<?php
// Local sample statistics. Run with: php Database/seed_dashboard.php
// SAMPLE-DASH order numbers make fictional paid revenue easy to identify.
// Re-running skips existing records and never changes inventory or existing users.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require __DIR__ . '/../config/database.php';
$db = db();
$inserted = 0;
try {
    $db->beginTransaction();
    $settings = $db->query('SELECT currency, CURRENT_DATE AS today FROM store_settings WHERE id=1')->fetch();
    if (!$settings) throw new RuntimeException('Store settings are missing.');
    $today = new DateTimeImmutable($settings['today']);
    $products = [];
    foreach (['cpu','gpu','mb','memory','storage','psu','case_box','cooling','fans'] as $category) {
        foreach ($db->query("SELECT id,name,price FROM `$category` WHERE status='active' AND price > 0 ORDER BY id LIMIT 3")->fetchAll() as $product) {
            $products[] = $product + ['category'=>$category];
        }
    }
    if (!$products) throw new RuntimeException('Add products before running this seed.');
    $customers = [];
    for ($i=1; $i<=8; $i++) {
        $email = "dashboard.sample.$i@example.invalid";
        $find = $db->prepare('SELECT id,username FROM users WHERE email=?');
        $find->execute([$email]);
        $user = $find->fetch();
        if (!$user) {
            $name = "Sample Customer $i";
            $db->prepare("INSERT INTO users (username,email,password,role,status,created_at) VALUES (?,?,?,'customer','disabled',?)")
                ->execute([$name,$email,password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),$today->modify('first day of this month')->modify('-5 months')->format('Y-m-d 10:00:00')]);
            $user = ['id'=>$db->lastInsertId(),'username'=>$name];
        }
        $customers[] = $user + ['email'=>$email];
    }
    $findOrder = $db->prepare('SELECT id FROM orders WHERE order_number=?');
    $addOrder = $db->prepare('INSERT INTO orders (order_number,user_id,customer_name,customer_email,shipping_address,shipping_city,shipping_postal_code,subtotal,shipping_total,total,currency,payment_method,payment_status,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $addItem = $db->prepare('INSERT INTO order_items (order_id,category,product_id,product_name,quantity,unit_price,line_total) VALUES (?,?,?,?,?,?,?)');
    $sequence = 0;
    for ($offset=5; $offset>=0; $offset--) {
        $month = $today->modify('first day of this month')->modify("-$offset months");
        $count = 8 - $offset;
        if ($offset === 0) $count += 6;
        for ($i=0; $i<$count; $i++) {
            $number = 'SAMPLE-DASH-' . $month->format('Ym') . '-' . sprintf('%02d', $i+1);
            $sequence++;
            $findOrder->execute([$number]);
            if ($findOrder->fetchColumn()) continue;
            $customer = $customers[($sequence-1) % count($customers)];
            $items = [];
            $subtotal = 0;
            for ($j=0; $j<2 + ($i % 3); $j++) {
                $product = $products[($sequence*3+$j) % count($products)];
                $quantity = $j === 0 ? 1 + ($i % 2) : 1;
                $cents = (int)round((float)$product['price'] * 100);
                $subtotal += $cents * $quantity;
                $items[] = [$product,$quantity,$cents];
            }
            $status = 'completed';
            $payment = 'paid';
            if ($offset === 0 && $i>=8) {
                $status = ['pending','processing','cancelled'][($i-8)%3];
                $payment = 'unpaid';
            }
            $maxDay = $offset === 0 ? (int)$today->format('j') : (int)$month->format('t');
            $date = $month->modify('+' . (int)floor($i * $maxDay / $count) . ' days')->format('Y-m-d 00:00:00');
            $money = fn(int $cents): string => number_format($cents/100,2,'.','');
            $addOrder->execute([$number,$customer['id'],$customer['username'],$customer['email'],'Sample address (fictional)','Sample City','00000',$money($subtotal),'0.00',$money($subtotal),$settings['currency'],'sample_seed',$payment,$status,$date,$date]);
            $orderId = $db->lastInsertId();
            foreach ($items as [$product,$quantity,$cents]) {
                $addItem->execute([$orderId,$product['category'],$product['id'],$product['name'],$quantity,$money($cents),$money($cents*$quantity)]);
            }
            $inserted++;
        }
    }
    $db->commit();
    echo "Inserted $inserted sample orders. Sample customers are disabled; inventory is unchanged.\n";
} catch (Throwable $error) {
    if ($db->inTransaction()) $db->rollBack();
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}
