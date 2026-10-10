<?php
// The admin workspace reuses this footer without the storefront navigation.
if (!empty($adminLayout)) {
    ?>
    <footer class="admin-footer"><span>&copy; <?= e(date('Y')) ?> PCForge</span><span>Store administration</span></footer>
    </div>
    </body>
    </html>
    <?php
    return;
}
// Include after the page's closing </main> tag. header.php loads the helpers.
$publicStoreSettings = store_settings();
$footerGroups = [
    'Explore' => [
        'Home' => 'index.php',
        'All Components' => 'products.php',
        'Prebuilt PCs' => 'prebuilts.php',
        'Compare Parts' => 'compare.php',
    ],
    'Core Components' => [
        'Processors' => 'products.php?category=cpu',
        'Graphics Cards' => 'products.php?category=gpu',
        'Motherboards' => 'products.php?category=mb',
        'Memory' => 'products.php?category=memory',
    ],
    'Complete Your PC' => [
        'Storage' => 'products.php?category=storage',
        'Power Supplies' => 'products.php?category=psu',
        'Cases' => 'products.php?category=case_box',
        'CPU Cooling' => 'products.php?category=cooling',
        'Case Fans' => 'products.php?category=fans',
        'Monitors' => 'products.php?category=monitor',
    ],
    'Plan Your Build' => [
        'PC Builder' => 'builder.php',
        'Shopping Cart' => 'cart.php',
        'Build Planning Guide' => 'index.php#planning-heading',
        'Browse Categories' => 'index.php#categories-heading',
    ],
];
?>
<link rel="stylesheet" href="<?= e(url('assets/css/footer.css?v=' . filemtime(__DIR__ . '/../assets/css/footer.css'))) ?>">
<footer class="forge-footer">
    <div class="forge-footer-top">
        <div class="forge-footer-brand">
            <a class="forge-footer-logo" href="<?= e(url('index.php')) ?>">
                <img class="forge-footer-logo-image" src="<?= e(url('assets/images/logo_nobg.png')) ?>" alt="PCForge">
                <span><?= e($publicStoreSettings['store_name']) ?></span>
            </a>
            <p class="forge-footer-description">
                Every great PC starts with a plan. Explore components and choose the parts
                that fit your work, games, and budget.
            </p>
            <?php if ($publicStoreSettings['store_email'] !== ''): ?><p><a href="mailto:<?= e($publicStoreSettings['store_email']) ?>"><?= e($publicStoreSettings['store_email']) ?></a></p><?php endif; ?>
            <a class="btn btn-primary" href="<?= e(url('builder.php')) ?>">Build Your PC <span aria-hidden="true">&rarr;</span></a>
        </div>
        <?php foreach ($footerGroups as $heading => $links): ?>
            <details class="forge-footer-section" data-footer-section aria-label="<?= e('Footer: ' . $heading) ?>" open>
                <summary><span class="forge-footer-heading" role="heading" aria-level="2"><?= e($heading) ?></span><span class="forge-footer-section-icon" aria-hidden="true">+</span></summary>
                <ul class="forge-footer-list">
                    <?php foreach ($links as $label => $path): ?>
                        <li><a href="<?= e(url($path)) ?>"><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </details>
        <?php endforeach; ?>
    </div>
    <div class="forge-footer-bottom">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <span>&copy; <?= e(date('Y')) ?> PCForge</span>
            <span class="forge-footer-note">School project</span>
        </div>
        <p class="mb-0">Demo checkout only. No real payments are processed.</p>
        <a class="forge-footer-top-link" href="#main-content">Back to content <span aria-hidden="true">&uarr;</span></a>
    </div>
</footer>
<script src="<?= e(url('assets/js/footer.js?v=' . filemtime(__DIR__ . '/../assets/js/footer.js'))) ?>"></script>
<?php require_once __DIR__ . '/ai-assistant.php'; ?>
<script src="<?= e(url('assets/js/ai-assistant.js?v=' . filemtime(__DIR__ . '/../assets/js/ai-assistant.js'))) ?>" defer></script>
</body>
</html>
