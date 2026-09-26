<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
$pdo = db();
$pdo->beginTransaction();
try {
    $name = 'db_test_' . bin2hex(random_bytes(6));
    $stmt = $pdo->prepare('INSERT INTO users (username,email,password) VALUES (?,?,?)');
    $stmt->execute([$name, $name . '@example.invalid', password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT)]);
    $userId = $pdo->lastInsertId();
    $stmt = $pdo->prepare('INSERT INTO user_shipping_details (user_id,name,phone,address,city,postal_code,country) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$userId, 'Test customer', '123456', 'Test address', 'Yangon', '11181', 'Myanmar']);
    $stmt = $pdo->prepare('INSERT INTO orders (order_number,user_id,customer_name,customer_email,shipping_address,shipping_city,shipping_postal_code,subtotal,total) VALUES (?,?,?,?,?,?,?,?,?)');
    $stmt->execute([$name, $userId, 'Test customer', $name . '@example.invalid', 'Test address', 'Yangon', '11181', '20.00', '20.00']);
    $orderId = $pdo->lastInsertId();
    $stmt = $pdo->prepare('INSERT INTO order_items (order_id,category,product_id,product_name,quantity,unit_price,line_total) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute([$orderId, 'cpu', 1, 'Historical test snapshot', 2, '10.00', '20.00']);
    $rejected = false;
    try { $stmt->execute([$orderId, 'cpu', 1, 'Invalid quantity', 0, '10.00', '0.00']); }
    catch (PDOException $error) { $rejected = $error->getCode() === '23000'; }
    if (!$rejected) throw new RuntimeException('Zero quantity was not rejected.');
    $query = $pdo->prepare('SELECT SUM(line_total) FROM order_items WHERE order_id = ?');
    $query->execute([$orderId]);
    if ($query->fetchColumn() !== '20.00') throw new RuntimeException('Historical item total mismatch.');
    $pdo->rollBack();
    echo "PASS shipping record, order relationship, historical prices, quantity constraint; test rows rolled back.\n";
} catch (Throwable $error) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $error;
}
