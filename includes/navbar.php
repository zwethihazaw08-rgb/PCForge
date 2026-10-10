<?php

// Include this after header.php, which loads e() and url().
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
$navPage = match ($currentPage) {
    'product.php' => 'products.php',
    'build-summary.php' => 'builder.php',
    default => $currentPage,
};
$navLinks = [
    'index.php' => 'Home',
    'builder.php' => 'PC Builder',
    'products.php' => 'Components',
    'prebuilts.php' => 'Prebuilt PCs',
    'compare.php' => 'Compare',
];
$currentUser = auth_user();

?>
<nav class="lg-navbar navbar navbar-expand-xl" aria-label="Main navigation">
    <a class="lg-brand" href="<?= e(url('index.php')) ?>">
        <img class="lg-logo" src="<?= e(url('assets/images/logo_nobg.png')) ?>" alt="">
        <span>PCForge</span>
    </a>

    <div class="lg-mobile-controls">
        <button class="lg-control lg-theme-toggle" type="button" data-theme-toggle-mobile aria-label="Switch to dark mode" aria-pressed="false">&#9790;</button>
        <button class="lg-control lg-menu-toggle" type="button" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <div class="collapse navbar-collapse lg-menu" id="mainNavbar">
        <div class="lg-tabs" data-glass-tabs>
            <span class="lg-sheen" aria-hidden="true"></span>
            <span class="lg-pill" aria-hidden="true"></span>
            <?php foreach ($navLinks as $path => $label): ?>
                <a class="lg-item<?= $navPage === $path ? ' active' : '' ?>" href="<?= e(url($path)) ?>" <?= $navPage === $path ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="lg-actions">
            <button class="lg-control lg-theme-toggle lg-desktop-theme" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false">&#9790;</button>
            <a class="lg-action" href="<?= e(url('cart.php')) ?>" <?= $currentPage === 'cart.php' ? 'aria-current="page"' : '' ?>>Cart</a>
            <?php if ($currentUser): ?>
                <div class="dropdown lg-account">
                    <button class="lg-action lg-account-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>Hi, <?= e($currentUser['username']) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-header">Your account</span></li>
                        <?php if ($currentUser['role'] === 'admin'): ?>
                            <li><a class="dropdown-item" href="<?= e(url('admin/dashboard.php')) ?>">Admin dashboard</a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item" href="<?= e(url('profile.php')) ?>">Your profile</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('build-summary.php')) ?>">Review current build</a></li>
                        <li><a class="dropdown-item" href="<?= e(url('saved-builds.php')) ?>">Saved builds</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="<?= e(url('logout.php')) ?>" class="m-0">
                                <?= csrf_field() ?><button class="dropdown-item" type="submit">Sign out</button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php else: ?>
                <a class="lg-action" href="<?= e(url('login.php')) ?>" <?= $currentPage === 'login.php' ? 'aria-current="page"' : '' ?>>Sign in</a>
                <a class="lg-action lg-register" href="<?= e(url('register.php')) ?>" <?= $currentPage === 'register.php' ? 'aria-current="page"' : '' ?>>Create account</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<script defer src="<?= e(url('assets/js/navbar.js?v=' . (string) filemtime(__DIR__ . '/../assets/js/navbar.js'))) ?>"></script>
