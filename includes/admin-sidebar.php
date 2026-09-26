<?php
// Shared navigation for every admin page.
$adminNavigation = [
    'Overview' => [['dashboard.php', 'Dashboard', 'grid', true]],
    'Management' => [
        ['products.php', 'Products', 'cpu', true],
        ['categories.php', 'Categories', 'collection', true],
        ['inventory.php', 'Inventory', 'boxes', true],
        ['orders.php', 'Orders', 'receipt', true],
        ['users.php', 'Customers', 'people', true],
        ['builds.php', 'PC Builds', 'pc-display', true],
    ],
    'Analytics' => [['reports.php', 'Reports', 'graph-up', true]],
    'System' => [['settings.php', 'Settings', 'gear', true]],
];
$activePage = basename($_SERVER['SCRIPT_NAME'] ?? 'dashboard.php');
if (str_starts_with($activePage,'product-')) $activePage = 'products.php';
if ($activePage === 'user-view.php') $activePage = 'users.php';
if ($activePage === 'order-view.php') $activePage = 'orders.php';
if ($activePage === 'build-view.php') $activePage = 'builds.php';
?>
<aside class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="admin-sidebar" aria-labelledby="sidebar-title">
    <div class="offcanvas-header">
        <span class="fw-semibold" id="sidebar-title">PCForge administration</span>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#admin-sidebar" aria-label="Close navigation"></button>
    </div>
    <div class="offcanvas-body">
        <a class="admin-brand" href="<?= e(url('admin/dashboard.php')) ?>"><img class="admin-brand-logo" src="<?= e(url('assets/images/logo_nobg.png')) ?>" alt="" width="32" height="32"> PCFORGE <span class="admin-brand-label">ADMIN</span></a>
        <nav class="admin-navigation" aria-label="Administration">
        <?php foreach ($adminNavigation as $group => $links): ?>
            <p class="nav-group-label"><?= e($group) ?></p>
            <?php foreach ($links as [$path, $label, $icon, $available]): ?>
                <?php if ($available): ?>
                <a class="admin-nav-link <?= $activePage === $path ? 'active' : '' ?>" href="<?= e(url('admin/' . $path)) ?>" <?= $activePage === $path ? 'aria-current="page"' : '' ?>><i class="bi bi-<?= e($icon) ?>" aria-hidden="true"></i><?= e($label) ?></a>
                <?php else: ?>
                <span class="admin-nav-link nav-unavailable"><i class="bi bi-<?= e($icon) ?>" aria-hidden="true"></i><?= e($label) ?><span class="nav-soon">Planned</span></span>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </nav>
        <div class="sidebar-account">
            <span class="account-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($adminUser['username'], 0, 1))) ?></span>
            <div class="account-name"><strong><?= e($adminUser['username']) ?></strong><span>Administrator</span></div>
            <form method="post" action="<?= e(url('logout.php')) ?>">
                <?= csrf_field() ?><button class="icon-button" type="submit" aria-label="Sign out" title="Sign out"><i class="bi bi-box-arrow-right" aria-hidden="true"></i></button>
            </form>
        </div>
    </div>
</aside>
