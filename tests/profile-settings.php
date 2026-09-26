<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Isolated handler checks: SQLite storage and mail are test doubles; no real accounts or mail are used.
$scenario = $argv[1] ?? '';
if ($scenario === '') {
    foreach (['render', 'username', 'duplicate', 'shipping', 'invalid_shipping', 'password', 'wrong_password', 'email_request', 'email_verify', 'email_wrong', 'email_expired'] as $case) {
        passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . escapeshellarg($case), $code);
        if ($code) exit($code);
    }
    exit;
}
class ProfileTestPDO extends PDO {
    public function prepare(string $query, array $options = []): PDOStatement|false {
        $query = str_replace('ON DUPLICATE KEY UPDATE', 'ON CONFLICT(user_id) DO UPDATE SET', $query);
        $query = preg_replace('/VALUES\((\w+)\)/', 'excluded.$1', $query);
        return parent::prepare($query, $options);
    }
}
$database = new ProfileTestPDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$database->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT UNIQUE, email TEXT UNIQUE, password TEXT)');
$database->exec('CREATE TABLE user_shipping_details (user_id INTEGER PRIMARY KEY, name TEXT, phone TEXT, address TEXT, address_line2 TEXT, city TEXT, region TEXT, postal_code TEXT, country TEXT)');
$insert = $database->prepare('INSERT INTO users VALUES (?, ?, ?, ?)');
$insert->execute([1, 'tester', 'old@example.com', password_hash('old-password', PASSWORD_DEFAULT)]);
$insert->execute([2, 'taken', 'taken@example.com', password_hash('other-password', PASSWORD_DEFAULT)]);
$database->exec("INSERT INTO user_shipping_details VALUES (2, 'Other user', '123456789', 'Other street', '', 'Other city', '', '', 'Other country')");
function db(): PDO { return $GLOBALS['database']; }
function auth_user(): array { return ['id' => 1, 'username' => 'tester', 'email' => 'old@example.com']; }
function require_login(string $target): void {}
function csrf_verify(): void { if (($_POST['csrf_token'] ?? '') !== 'test-token') exit(9); }
function csrf_field(): string { return '<input name="csrf_token" value="test-token">'; }
function e(?string $s): string { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function url(string $s): string { return '/PCForge/' . $s; }
function redirect(string $s): void { exit; }
function pcforge_mail_settings(): array { return ['otp_ttl_seconds' => 600, 'otp_max_attempts' => 5]; }
function send_registration_otp(string $email, string $otp, bool $change = false): bool { $GLOBALS['sentMail'] = [$email, $otp, $change]; return true; }
session_start();
$_SESSION = [];
$_POST = ['csrf_token' => 'test-token'];
$_SERVER['REQUEST_METHOD'] = $scenario === 'render' ? 'GET' : 'POST';
if (in_array($scenario, ['username', 'duplicate', 'email_request'])) {
    $_POST += ['action' => 'account', 'username' => $scenario === 'duplicate' ? 'taken' : 'renamed', 'email' => $scenario === 'email_request' ? 'new@example.com' : 'old@example.com', 'current_password' => 'old-password'];
}
if (in_array($scenario, ['shipping', 'invalid_shipping'])) {
    $_POST += ['action' => 'shipping', 'user_id' => '2', 'name' => 'Test Person', 'phone' => '+95 91234567', 'address' => $scenario === 'invalid_shipping' ? '' : '12 Main Road', 'address_line2' => 'Unit 3', 'city' => 'Yangon', 'region' => 'Yangon', 'postal_code' => '11181', 'country' => 'Myanmar'];
}
if (in_array($scenario, ['password', 'wrong_password'])) {
    $_POST += ['action' => 'password', 'password_current' => $scenario === 'wrong_password' ? 'bad' : 'old-password', 'new_password' => 'new-password', 'password_confirmation' => 'new-password'];
}
if (in_array($scenario, ['email_verify', 'email_wrong', 'email_expired'])) {
    $_SESSION['profile_email_change'] = ['user_id' => 1, 'username' => 'renamed', 'email' => 'new@example.com', 'original_email' => 'old@example.com', 'hash' => password_hash('123456', PASSWORD_DEFAULT), 'attempts' => 0, 'sent_at' => time(), 'expires_at' => $scenario === 'email_expired' ? time() - 1 : time() + 600];
    $_POST += ['action' => 'verify_email', 'otp' => $scenario === 'email_wrong' ? '000000' : '123456'];
}
$source = file_get_contents(__DIR__ . '/../profile.php');
$source = preg_replace('/^require_once .*;\R/m', '', $source);
$source = str_replace("<?php require_once __DIR__ . '/includes/footer.php'; ?>", '', $source);
$fixture = tempnam(sys_get_temp_dir(), 'profile-check-');
file_put_contents($fixture, $source);
ob_start();
register_shutdown_function(function () use ($scenario, $fixture) {
    $html = ob_get_clean();
    unlink($fixture);
    $user = db()->query('SELECT * FROM users WHERE id = 1')->fetch();
    $shipping = db()->query('SELECT * FROM user_shipping_details WHERE user_id = 1')->fetch();
    $errors = $GLOBALS['errors'] ?? [];
    $ok = match ($scenario) {
        'render' => str_contains($html, 'Shipping details') && !str_contains($html, 'Your saved builds'),
        'username' => $user['username'] === 'renamed' && $user['email'] === 'old@example.com',
        'duplicate' => $user['username'] === 'tester' && count($errors) > 0,
        'shipping' => $shipping['address'] === '12 Main Road' && $shipping['phone'] === '+95 91234567' && db()->query('SELECT name FROM user_shipping_details WHERE user_id = 2')->fetchColumn() === 'Other user',
        'invalid_shipping' => !$shipping && count($errors) > 0,
        'password' => password_verify('new-password', $user['password']),
        'wrong_password' => password_verify('old-password', $user['password']) && count($errors) > 0,
        'email_request' => $user['email'] === 'old@example.com' && ($_SESSION['profile_email_change']['email'] ?? '') === 'new@example.com' && ($GLOBALS['sentMail'][2] ?? false),
        'email_verify' => $user['email'] === 'new@example.com' && !isset($_SESSION['profile_email_change']),
        'email_wrong' => $user['email'] === 'old@example.com' && $_SESSION['profile_email_change']['attempts'] === 1 && count($errors) > 0,
        'email_expired' => $user['email'] === 'old@example.com' && !isset($_SESSION['profile_email_change']) && count($errors) > 0,
        default => false,
    };
    echo ($ok ? 'PASS ' : 'FAIL ') . $scenario . PHP_EOL;
    if (!$ok) exit(1);
});
require $fixture;
