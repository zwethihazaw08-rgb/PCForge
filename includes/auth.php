<?php

require_once __DIR__ . '/functions.php';

function is_admin(): bool
{
    $user = auth_user();
    return $user !== null && $user['role'] === 'admin';
}

function require_admin(): void
{
    // Validate the account against the database on every request, including POSTs.
    // Never trust a role supplied by a form or retained in the session.
    $target = 'admin/dashboard.php';
    $script = basename($_SERVER['SCRIPT_NAME'] ?? 'dashboard.php');
    if (preg_match('/^[a-z-]+\.php$/D', $script)) {
        $target = 'admin/' . $script;
    }
    require_login($target);
    if (!is_admin()) {
        http_response_code(403);
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Access denied | PCForge</title><main><h1>Administrator access required</h1><p>Your account does not have permission to open this page.</p><a href="' . e(url('index.php')) . '">Return to PCForge</a></main></html>';
        exit;
    }
    header('Cache-Control: no-store, private');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
}

function flash_set(string $message, string $type = 'success'): void
{
    $_SESSION['admin_flash'] = ['message' => $message, 'type' => in_array($type, ['success', 'danger', 'warning', 'info'], true) ? $type : 'info'];
}

function flash_get(): ?array
{
    $flash = $_SESSION['admin_flash'] ?? null;
    unset($_SESSION['admin_flash']);
    return is_array($flash) ? $flash : null;
}
