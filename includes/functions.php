<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/catalog.php';

// Include this file before sending any HTML to the browser.
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'use_strict_mode' => true,
        'use_only_cookies' => true,
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'cookie_samesite' => 'Lax',
    ]);
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    // Change this base path if the project folder is renamed.
    return '/PCForge/' . ltrim($path, '/');
}

function public_url(string $path = ''): string
{
    // QR codes may be scanned by another device, so allow the owner to set a
    // hostname that is reachable from phones (for example a LAN address).
    $configured = trim((string) getenv('PCFORGE_PUBLIC_URL'));
    if ($configured !== '') {
        $parts = parse_url($configured);
        if (is_array($parts) && in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true) && !empty($parts['host'])) {
            return rtrim($configured, '/') . '/' . ltrim($path, '/');
        }
    }

    return url($path);
}

function redirect(string $path): void
{
    // Pass a project-relative path, such as 'cart.php'.
    header('Location: ' . url($path), true, 303);
    exit;
}

function auth_user(): ?array
{
    static $loaded = false;
    static $user = null;

    if ($loaded) {
        return $user;
    }

    $loaded = true;
    $userId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
    if (!$userId) {
        return null;
    }

    $statement = db()->prepare('SELECT id, username, email, role FROM users WHERE id = :id AND status = \'active\' LIMIT 1');
    $statement->execute(['id' => $userId]);
    $user = $statement->fetch() ?: null;

    if (!$user) {
        unset($_SESSION['user_id']);
    }

    return $user;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
}

function logout_user(): void
{
    unset($_SESSION['user_id']);
    session_regenerate_id(true);
}

function auth_redirect_target(string $fallback = 'index.php'): string
{
    $target = $_POST['redirect'] ?? $_GET['redirect'] ?? $fallback;
    if (!is_string($target) || $target === '' || str_starts_with($target, '/') || str_contains($target, '://')) {
        return $fallback;
    }

    return ltrim($target, '/');
}

function require_login(string $target = 'index.php'): void
{
    if (!auth_user()) {
        redirect('login.php?redirect=' . rawurlencode($target));
    }
}

function login_destination(array $user, string $target): string
{
    // Use the admin workspace for a normal sign-in, while preserving return
    // destinations such as checkout or a specific protected admin page.
    return ($user['role'] ?? '') === 'admin' && $target === 'index.php'
        ? 'admin/dashboard.php'
        : $target;
}

function money(?string $amount): string
{
    if ($amount === null) {
        return 'Price unavailable';
    }

    // Formatting only. Store prices as DECIMAL; calculate totals separately.
    $currency = store_settings()['currency'];
    return ($currency === 'USD' ? '$' : $currency . ' ') . number_format((float) $amount, 2);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $submittedToken = $_POST['csrf_token'] ?? null;
    $sessionToken = $_SESSION['csrf_token'] ?? null;

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST'
        || !is_string($submittedToken)
        || !is_string($sessionToken)
        || $sessionToken === ''
        || !hash_equals($sessionToken, $submittedToken)) {
        http_response_code(403);
        exit('Invalid form token. Please reload the page and try again.');
    }
}

// Keep login and admin recovery available while the customer store is closed.
// This runs before public page handlers, so checkout writes are blocked too.
if (PHP_SAPI !== 'cli') {
    $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $maintenanceExempt = str_contains($scriptPath, '/admin/')
        || str_contains($scriptPath, '/auth/')
        || in_array(basename($scriptPath), ['login.php','logout.php'], true);
    if (!$maintenanceExempt && (int)store_settings()['maintenance_mode'] === 1 && (auth_user()['role'] ?? '') !== 'admin') {
        http_response_code(503);
        header('Retry-After: 3600');
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Store maintenance</title><main><h1>' . e(store_settings()['store_name']) . ' is temporarily closed</h1><p>We are updating the store. Please try again later.</p><a href="' . e(url('login.php?redirect=admin/dashboard.php')) . '">Administrator sign in</a></main></html>';
        exit;
    }
}
