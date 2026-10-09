<?php
// Local HTTP integration tests. Only records created by this run are removed.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/prebuilts.php';
$pdo = db();
$tag = 'checkout_test_' . bin2hex(random_bytes(6));
$password = bin2hex(random_bytes(20));
$users = []; $parts = []; $cookies = [];
$base = getenv('PCFORGE_TEST_URL') ?: 'http://localhost/PCForge/';

function verify_checkout(bool $ok, string $message): void
{
    if (!$ok) throw new RuntimeException($message);
    echo 'PASS ' . $message . PHP_EOL;
}
function page(string $path, ?array $post = null, int $account = 0): array
{
    $curl = curl_init($GLOBALS['base'] . $path);
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 20, CURLOPT_COOKIEJAR => $GLOBALS['cookies'][$account], CURLOPT_COOKIEFILE => $GLOBALS['cookies'][$account], CURLOPT_FOLLOWLOCATION => false, CURLOPT_HEADER => true]);
    if ($post !== null) curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($post)]);
    $raw = curl_exec($curl);
    if ($raw === false) throw new RuntimeException(curl_error($curl));
    $size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $response = ['body' => substr($raw, $size), 'headers' => substr($raw, 0, $size), 'status' => (int) curl_getinfo($curl, CURLINFO_HTTP_CODE), 'location' => curl_getinfo($curl, CURLINFO_REDIRECT_URL)];
    curl_close($curl);
    if (preg_match('/Fatal error:|Warning:|Notice:/', $response['body'])) throw new RuntimeException($path . ' returned a PHP error');
    return $response;
}
function csrf(array $response): string
{
    if (!preg_match('/name="csrf_token" value="([a-f0-9]+)"/', $response['body'], $match)) throw new RuntimeException('Missing CSRF token.');
    return $match[1];
}
function cart_key(array $parts, string $action = 'add_build'): string
{
    ksort($parts);
    return hash('sha256', $action . json_encode($parts));
}

try {
    for ($account = 0; $account < 3; $account++) $cookies[] = tempnam(sys_get_temp_dir(), 'pcforge-checkout-');
    for ($account = 0; $account < 2; $account++) {
        $query = $pdo->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,'customer')");
        $query->execute([$tag . $account, $tag . $account . '@example.invalid', password_hash($password, PASSWORD_DEFAULT)]);
        $users[] = (int) $pdo->lastInsertId();
        $response = page('login.php', null, $account);
        $response = page('login.php', ['csrf_token' => csrf($response), 'login' => $tag . $account, 'password' => $password], $account);
        verify_checkout($response['status'] === 303, 'test customer signed in');
    }
    $categories = ['cpu', 'mb', 'memory', 'gpu', 'storage', 'cooling', 'psu', 'case_box', 'monitor'];
    foreach ($categories as $category) {
        $query = $pdo->prepare("INSERT INTO `$category` (name,price,stock,status) VALUES (?,10.25,10,'active')");
        $query->execute([$tag . ' ' . $category]);
        $parts[$category] = (int) $pdo->lastInsertId();
    }
    $build = $parts; unset($build['monitor']);
    $query = $pdo->prepare('INSERT INTO saved_builds (user_id,build_name,build_data,share_token) VALUES (?,?,?,?)');
    $query->execute([$users[0], 'Receipt test build', json_encode($build), bin2hex(random_bytes(32))]);
    $savedId = (int) $pdo->lastInsertId();
    $query->execute([$users[0], 'Partial build', json_encode(['cpu' => $parts['cpu']]), bin2hex(random_bytes(32))]);
    $partialId = (int) $pdo->lastInsertId();
    $token = csrf(page('saved-builds.php'));
    $response = page('cart.php', ['action' => 'add_saved_build', 'id' => $savedId]);
    verify_checkout($response['status'] === 403, 'adding a build requires CSRF');
    $response = page('cart.php', ['csrf_token' => csrf(page('cart.php', null, 1)), 'action' => 'add_saved_build', 'id' => $savedId], 1);
    verify_checkout(str_contains($response['body'], 'That saved build is no longer available.') && str_contains($response['body'], 'Your cart is empty'), 'another customer cannot add a private saved build');
    $response = page('cart.php', ['csrf_token' => $token, 'action' => 'add_saved_build', 'id' => $partialId]);
    verify_checkout(str_contains($response['body'], 'Complete all eight builder steps') && str_contains($response['body'], 'Your cart is empty'), 'incomplete build is rejected atomically');
    $response = page('cart.php', ['csrf_token' => $token, 'action' => 'add_prebuilt', 'build' => 'nonexistent']);
    verify_checkout(str_contains($response['body'], 'Choose an available prebuilt'), 'unrecognized prebuilt is rejected');

    // Use real gallery templates only for read-only catalogue/cart checks.
    foreach (prebuilt_templates() as $key => $template) {
        $response = page('cart.php', ['csrf_token' => $token, 'action' => 'add_prebuilt', 'build' => $key]);
        verify_checkout($response['status'] === 303, $template['name'] . ' adds directly to cart');
        $cart = page('cart.php');
        verify_checkout(str_contains($cart['body'], $template['name']) && str_contains($cart['body'], 'View the 8 included parts'), 'prebuilt keeps its name and all eight components');
        page('cart.php', ['csrf_token' => $token, 'action' => 'remove', 'item' => cart_key($template['ids'])]);
    }
    foreach ($build as $category => $id) page('builder.php?category=' . $category . '&id=' . $id);
    $response = page('build-summary.php');
    verify_checkout(str_contains($response['body'], 'value="add_build"'), 'build summary offers Add to cart');
    $response = page('cart.php', ['csrf_token' => $token, 'action' => 'add_saved_build', 'id' => $savedId]);
    verify_checkout($response['status'] === 303, 'saved build adds directly to cart');
    page('cart.php', ['csrf_token' => $token, 'action' => 'add_build']);
    $response = page('cart.php');
    verify_checkout(str_contains($response['body'], 'value="2"') && str_contains($response['body'], 'View the 8 included parts'), 'identical saved/current builds combine with quantity two');
    page('cart.php', ['csrf_token' => $token, 'action' => 'add_product', 'category' => 'monitor', 'id' => $parts['monitor']]);
    $response = page('checkout.php');
    verify_checkout(str_contains($response['body'], 'Place demo order') && str_contains($response['body'], $tag . ' monitor'), 'checkout accepts a build together with a monitor');
    $customer = ['csrf_token' => $token, 'name' => 'Demo Customer', 'email' => 'receipt@example.invalid', 'address' => '12 Example Street', 'city' => 'Yangon', 'postal_code' => '11181', 'country' => 'Myanmar', 'payment_method' => 'demo'];
    $response = page('checkout.php', $customer);
    verify_checkout($response['status'] === 303, 'demo checkout saves the complete order');
    $query = $pdo->prepare('SELECT * FROM orders WHERE user_id = ?'); $query->execute([$users[0]]); $orders = $query->fetchAll();
    verify_checkout(count($orders) === 1 && $orders[0]['total'] === '174.25' && $orders[0]['payment_status'] === 'demo', 'server calculates 17 component prices with no real payment');
    $order = $orders[0];
    $response = page('checkout.php');
    verify_checkout(str_contains($response['body'], 'data-receipt-qr') && str_contains($response['body'], 'View / print receipt'), 'confirmation has a local QR code and receipt link');
    $confirmationHtml = $response['body'];
    page('checkout.php', $customer);
    $query->execute([$users[0]]);
    verify_checkout(count($query->fetchAll()) === 1, 'repeated confirmation submission does not duplicate the order');
    verify_checkout(str_contains(page('cart.php')['body'], 'Your cart is empty'), 'successful checkout clears the cart');
    $receiptPath = 'receipt.php?order=' . $order['order_number'];
    // Change only our fixture's catalogue values to prove historical receipt snapshots.
    $pdo->prepare('UPDATE cpu SET price=99.99, name=? WHERE id=?')->execute(['Changed fixture', $parts['cpu']]);
    $response = page($receiptPath);
    verify_checkout($response['status'] === 200 && str_contains($response['body'], $tag . ' cpu') && !str_contains($response['body'], 'Changed fixture') && str_contains($response['body'], '174.25'), 'receipt retains purchased names and prices');
    verify_checkout(str_contains($response['body'], 'Amount charged') && str_contains($response['body'], '0.00') && str_contains($response['headers'], 'no-store'), 'receipt shows zero charged and is not cached');
    $receiptHtml = $response['body'];
    $response = page($receiptPath, null, 1);
    verify_checkout($response['status'] === 404 && !str_contains($response['body'], '12 Example Street'), 'another customer cannot read the receipt');
    $response = page($receiptPath, null, 2);
    verify_checkout($response['status'] === 303 && str_contains($response['location'], rawurlencode($receiptPath)), 'phone scan requires login and preserves the receipt destination');
    verify_checkout(page('receipt.php?order[]=bad')['status'] === 404, 'malformed receipt identifier is handled');
    // A saved-build add must leave the user's separate builder selection alone.
    page('builder.php?category=cpu&id=24');
    page('cart.php', ['csrf_token' => $token, 'action' => 'add_saved_build', 'id' => $savedId]);
    $response = page('build-summary.php');
    verify_checkout(!str_contains($response['body'], '<p class="fw-semibold mb-0">Changed fixture</p>'), 'adding a saved build preserves the current builder selection');
    // Check expired/unavailable catalogue entries cannot be added as complete builds.
    $pdo->prepare("UPDATE cpu SET status='inactive' WHERE id=?")->execute([$parts['cpu']]);
    $response = page('cart.php', ['csrf_token' => $token, 'action' => 'add_saved_build', 'id' => $savedId]);
    verify_checkout(str_contains($response['body'], 'A selected part is unavailable'), 'unavailable saved-build component is rejected');
    if (in_array('--preview', $argv, true)) {
        $previewDir = __DIR__ . '/checkout-preview';
        if (!is_dir($previewDir)) mkdir($previewDir, 0777, true);
        file_put_contents($previewDir . '/confirmation.html', $confirmationHtml);
        file_put_contents($previewDir . '/receipt.html', $receiptHtml);
    }
} finally {
    foreach ($users as $id) {
        $pdo->prepare('DELETE i FROM order_items i JOIN orders o ON o.id=i.order_id WHERE o.user_id=?')->execute([$id]);
        $pdo->prepare('DELETE FROM orders WHERE user_id=?')->execute([$id]);
        $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
    }
    foreach ($parts as $category => $id) $pdo->prepare("DELETE FROM `$category` WHERE id=?")->execute([$id]);
    foreach ($cookies as $cookie) if (is_file($cookie)) unlink($cookie);
}
echo "All checkout/build integration checks passed.\n";
