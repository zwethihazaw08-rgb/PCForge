<link rel="stylesheet" href="<?= e(url('assets/css/home-builds.css?v=' . filemtime(__DIR__ . '/../assets/css/home-builds.css'))) ?>">
<section class="home-builds" aria-labelledby="gaming-deals-heading" data-build-carousel>
    <header class="home-builds-header">
        <div>
            <p class="home-builds-eyebrow">Your next gaming setup</p>
            <h2 id="gaming-deals-heading">Best Deals Gaming PCs</h2>
            <p class="home-builds-intro">New builds. Powerful parts. Find your next upgrade.</p>
        </div>
        <div class="home-builds-controls" hidden>
            <button type="button" class="home-builds-arrow" data-build-prev aria-label="Previous gaming PCs" aria-controls="gaming-builds-track" disabled>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m14 6-6 6 6 6"/></svg>
            </button>
            <button type="button" class="home-builds-arrow" data-build-next aria-label="Next gaming PCs" aria-controls="gaming-builds-track">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m10 6 6 6-6 6"/></svg>
            </button>
        </div>
    </header>
    <?php if ($buildsError || !$gamingBuilds): ?>
        <p class="home-builds-empty">Gaming builds are temporarily unavailable. <a href="<?= e(url('prebuilts.php')) ?>">Explore the PC shop</a> or check back shortly.</p>
    <?php else: ?>
        <div class="home-builds-track" id="gaming-builds-track" tabindex="0" role="region" aria-label="Gaming PC builds. Scroll to explore all configurations.">
            <?php foreach ($gamingBuilds as $key => $build): ?>
                <?php
                    $caseImage = prebuiltImage($build['parts']['case_box']);
                    $detailsUrl = url('prebuilts.php?build=' . rawurlencode($key) . '#pc-' . rawurlencode($key));
                    $memory = $build['parts']['memory'];
                    $memorySummary = $memory && !empty($memory['capacity']) && !empty($memory['speed'])
                        ? $memory['capacity'] . ' ' . $memory['speed'] . ' RAM' : ($memory['name'] ?? 'Memory unavailable');
                ?>
                <article class="gaming-deal-card" aria-labelledby="gaming-build-<?= e($key) ?>">
                    <div class="gaming-deal-badges">
                        <span class="gaming-deal-type">Prebuilt PC</span>
                        <span class="gaming-deal-tag"><?= !empty($build['is_new']) ? 'New build' : 'Gaming pick' ?></span>
                    </div>
                    <div class="gaming-deal-art">
                        <?php prebuiltIllustration(); ?>
                        <?php if ($caseImage): ?>
                            <img src="<?= e($caseImage) ?>" alt="<?= e($build['parts']['case_box']['name'] . ' case preview') ?>" loading="lazy" width="360" height="260" onerror="this.hidden = true;">
                        <?php endif; ?>
                        <a class="gaming-deal-details" href="<?= e($detailsUrl) ?>" aria-label="<?= e('View ' . $build['name'] . ' build details') ?>">View build <span aria-hidden="true">↗</span></a>
                        <span class="gaming-deal-image-note">Case preview</span>
                    </div>
                    <div class="gaming-deal-body">
                        <h3 id="gaming-build-<?= e($key) ?>"><a href="<?= e($detailsUrl) ?>"><?= e($build['name']) ?></a></h3>
                        <ul class="gaming-deal-specs">
                            <li><?= e($build['parts']['cpu']['name'] ?? 'Processor unavailable') ?></li>
                            <li><?= e($build['parts']['gpu']['name'] ?? 'Graphics card unavailable') ?></li>
                            <li><?= e($memorySummary) ?></li>
                            <li><?= e($build['parts']['storage']['name'] ?? 'Storage unavailable') ?></li>
                        </ul>
                        <p class="gaming-deal-stock"><span class="gaming-deal-dot <?= $build['can_add'] ? '' : 'is-unavailable' ?>" aria-hidden="true"></span><?= e($build['missing'] ? 'Some parts unavailable' : $build['stock']) ?></p>
                        <div class="gaming-deal-price">
                            <strong><?= e($build['missing'] || $build['missing_prices'] ? 'Price unavailable' : prebuiltMoney($build['cents'])) ?></strong>
                            <span>8 components · Customize it your way</span>
                        </div>
                        <form method="post" action="<?= e(url('cart.php')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="add_prebuilt">
                            <input type="hidden" name="build" value="<?= e($key) ?>">
                            <button class="gaming-deal-cart" type="submit" <?= !$build['can_add'] ? 'disabled' : '' ?> aria-label="<?= e('Add ' . $build['name'] . ' to cart') ?>"><?= $build['can_add'] ? 'Add to cart' : 'Currently unavailable' ?><span aria-hidden="true">⟶</span></button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <footer class="home-builds-footer">
            <p>Component totals only. Assembly, OS, delivery, and taxes are not included.</p>
            <a href="<?= e(url('prebuilts.php')) ?>">Shop all PC builds <span aria-hidden="true">&rarr;</span></a>
        </footer>
    <?php endif; ?>
</section>
<script defer src="<?= e(url('assets/js/home-builds.js?v=' . filemtime(__DIR__ . '/../assets/js/home-builds.js'))) ?>"></script>
