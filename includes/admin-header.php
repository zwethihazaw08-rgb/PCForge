<?php
require_once __DIR__ . '/auth.php';
require_admin();
$pageTitle = $pageTitle ?? 'Dashboard';
$adminUser = auth_user();
$adminLayout = true;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | PCForge Admin</title>
    <style>html{background:#fff;color:#111;color-scheme:light}html[data-theme=dark]{background:#0b0b0b;color:#f5f5f5;color-scheme:dark}@media(prefers-color-scheme:dark){html:not([data-theme]){background:#0b0b0b;color:#f5f5f5;color-scheme:dark}}body{background:inherit!important;color:inherit}</style>
    <script>
    (() => {
        let theme = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        try { const saved = localStorage.getItem('pcforge-theme'); if (['light', 'dark'].includes(saved)) theme = saved; } catch (_) {}
        document.documentElement.dataset.theme = theme;
        document.documentElement.dataset.bsTheme = theme;
    })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
    <noscript><style>@media(max-width:991.98px){.admin-sidebar{position:static!important;transform:none!important;visibility:visible!important;width:100%!important;height:auto!important}.admin-sidebar .offcanvas-body{display:block!important}.admin-sidebar .offcanvas-header,.admin-navbar button{display:none!important}}</style></noscript>
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script defer src="<?= e(url('assets/js/admin.js')) ?>"></script>
</head>
<body class="admin-body">
<a class="visually-hidden-focusable skip-link" href="#main-content">Skip to main content</a>
<?php require __DIR__ . '/admin-sidebar.php'; ?>
<div class="admin-workspace">
<?php require __DIR__ . '/admin-navbar.php'; ?>
<main id="main-content" class="admin-main" tabindex="-1">
<?php $flash = flash_get(); if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
