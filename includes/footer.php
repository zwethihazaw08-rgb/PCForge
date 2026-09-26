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
<!-- Scoped styles keep this footer update in one file. -->
<style>
    .forge-footer {
        margin: auto 1rem 1rem;
        padding: clamp(1.5rem, 4vw, 3.5rem);
        border: 1px solid #dedede;
        border-radius: 1.5rem;
        background: #ffffff;
        color: #171717;
    }
    .forge-footer-top {
        display: grid;
        grid-template-columns: 1.5fr repeat(4, 1fr);
        gap: 2rem;
        padding-bottom: 2.5rem;
    }
    .forge-footer-brand { max-width: 340px; }
    .forge-footer-logo {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        color: #171717;
        font-size: 1.2rem;
        font-weight: 700;
        text-decoration: none;
        letter-spacing: -0.025em;
    }
    .forge-footer-mark {
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border-radius: 0.6rem;
        background: #171717;
        color: #ffffff;
        font-size: 0.85rem;
    }
    .forge-footer-logo-image { display: block; width: auto; height: 2.4rem; max-width: 180px; object-fit: contain; }
    .forge-footer-description { margin: 1rem 0 1.25rem; color: #606060; font-size: 0.875rem; }
    .forge-footer h2 {
        margin: 0 0 1rem;
        font-size: 0.75rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    .forge-footer-list { display: flex; flex-direction: column; gap: 0.75rem; list-style: none; padding: 0; margin: 0; }
    .forge-footer-list a,
    .forge-footer-top-link { color: #606060; font-size: 0.875rem; text-decoration: none; }
    .forge-footer-list a:hover,
    .forge-footer-top-link:hover { color: #171717; text-decoration: underline; text-underline-offset: 0.25em; }
    .forge-footer-bottom {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        border-top: 1px solid #dedede;
        padding-top: 1.5rem;
        color: #606060;
        font-size: 0.8rem;
    }
    .forge-footer-note { display: inline-block; padding: 0.3rem 0.65rem; border-radius: 999px; background: #f5f5f5; color: #505050; }
    @media (max-width: 1100px) {
        .forge-footer-top { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .forge-footer-brand { grid-column: 1 / -1; max-width: 480px; }
    }
    @media (max-width: 760px) {
        .forge-footer-top { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 460px) {
        .forge-footer { margin-right: 0.5rem; margin-left: 0.5rem; border-radius: 1rem; }
        .forge-footer-top { grid-template-columns: 1fr; }
    }
</style>
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
            <nav aria-label="<?= e('Footer: ' . $heading) ?>">
                <h2><?= e($heading) ?></h2>
                <ul class="forge-footer-list">
                    <?php foreach ($links as $label => $path): ?>
                        <li><a href="<?= e(url($path)) ?>"><?= e($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
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
</body>
</html>
