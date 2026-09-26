<?php
// Run with PHP CLI against the local development database.
// Temporary accounts are always rolled back, including when the guard exits.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$scenario = $argv[1] ?? '';
if ($scenario === '') {
    foreach (['customer', 'disabled', 'admin'] as $case) {
        passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . escapeshellarg($case), $code);
        if ($code !== 0) exit($code);
    }
    exit;
}
if (!in_array($scenario, ['customer', 'disabled', 'admin'], true)) exit(1);
require_once __DIR__ . '/../includes/auth.php';
db()->beginTransaction();
$name = 'admin_test_' . bin2hex(random_bytes(6));
$query = db()->prepare('INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)');
$query->execute([$name, $name . '@example.invalid', password_hash(bin2hex(random_bytes(20)), PASSWORD_DEFAULT), $scenario === 'customer' ? 'customer' : 'admin', $scenario === 'disabled' ? 'disabled' : 'active']);
$_SESSION['user_id'] = (int) db()->lastInsertId();
$_SERVER['SCRIPT_NAME'] = '/PCForge/admin/dashboard.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
ob_start();
register_shutdown_function(function () use ($scenario) {
    $html = ob_get_clean();
    if (db()->inTransaction()) db()->rollBack();
    $ok = match ($scenario) {
        'customer' => http_response_code() === 403 && str_contains($html, 'Administrator access required') && !str_contains($html, 'kpi-grid'),
        'disabled' => http_response_code() === 303 && !isset($_SESSION['user_id']) && !str_contains($html, 'kpi-grid'),
        'admin' => str_contains($html, 'kpi-grid') && str_contains($html, 'csrf_token') && str_contains($html, 'admin-sidebar') && !str_contains($html, 'forge-footer-top'),
    };
    $_SESSION = [];
    session_destroy();
    echo ($ok ? 'PASS ' : 'FAIL ') . $scenario . PHP_EOL;
    if (!$ok) exit(1);
});
require __DIR__ . '/../admin/dashboard.php';
