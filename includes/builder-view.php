<?php

// The builder view deliberately uses native links, forms, and disclosure
// controls. The catalog remains usable when a browser blocks optional CDN JS.
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/navbar.php';

$currentStepIndex = array_search($currentCategory, $stepCategories, true);
$previousCategory = $currentStepIndex > 0 ? $stepCategories[$currentStepIndex - 1] : null;
$nextCategory = $currentStepIndex < count($stepCategories) - 1 ? $stepCategories[$currentStepIndex + 1] : null;
$selectedCurrent = $selectedProducts[$currentCategory] ?? null;
$selectedCurrentImage = $selectedCurrent ? $builderImageUrl($selectedCurrent) : null;

?>

<main id="main-content" tabindex="-1">
    <style>
        .builder-page { width: min(100% - 2rem, 1680px); margin-inline: auto; padding: 2rem 0 4rem; }
        .builder-page :is(a, button, input, select, summary) { touch-action: manipulation; }
        .builder-shell { display: grid; grid-template-columns: 190px minmax(0, 1fr) minmax(300px, 360px); align-items: start; border: 1px solid var(--forge-border); border-radius: 1rem; background: var(--forge-surface-raised); overflow: clip; }
        .builder-rail { position: sticky; top: 1rem; min-height: min(720px, calc(100dvh - 2rem)); padding: 1.2rem .9rem; border-right: 1px solid var(--forge-border); background: var(--forge-surface); }
        .builder-rail-title { margin: 0 0 1rem; padding: 0 .55rem .8rem; border-bottom: 1px solid var(--forge-border); font-size: .78rem; font-weight: 750; letter-spacing: .04em; text-transform: uppercase; }
        .builder-rail-list { display: grid; gap: .3rem; margin: 0; padding: 0; list-style: none; }
        .builder-rail-link { display: block; padding: .7rem .65rem; border-radius: .45rem; color: var(--forge-muted); font-size: .9rem; font-weight: 650; text-decoration: none; }
        .builder-rail-link:hover, .builder-rail-link:focus-visible { color: var(--bs-body-color); background: var(--forge-surface-raised); }
        .builder-rail-link[aria-current="step"] { color: var(--bs-body-color); background: var(--forge-surface-raised); box-shadow: inset 3px 0 var(--bs-body-color); }
        .builder-workspace { min-width: 0; padding: 1.4rem 1.5rem 1.5rem; }
        .builder-heading { display: flex; flex-wrap: wrap; align-items: baseline; justify-content: space-between; gap: .75rem 1rem; margin-bottom: 1rem; }
        .builder-heading h1 { margin: 0; font-size: clamp(1.5rem, 2.5vw, 2rem); }
        .builder-heading p { margin: .25rem 0 0; color: var(--forge-muted); }
        .builder-filterbar { display: flex; flex-wrap: wrap; align-items: center; gap: .55rem; margin-bottom: 1rem; padding: .8rem; border: 1px solid var(--forge-border); border-radius: .75rem; background: var(--forge-surface); }
        .builder-filter-label { margin-right: .15rem; font-size: .9rem; font-weight: 700; }
        .builder-filterbar select { min-width: 9rem; max-width: 12rem; min-height: 42px; padding: .55rem .75rem; border: 1px solid var(--forge-border); border-radius: .5rem; background: var(--forge-surface-raised); color: var(--bs-body-color); font: inherit; }
        .builder-filterbar .builder-sort { margin-left: auto; }
        .builder-filter-submit { min-height: 42px; padding: .55rem .85rem; border: 1px solid var(--bs-body-color); border-radius: .5rem; background: var(--bs-body-color); color: var(--bs-body-bg); font-weight: 700; }
        .builder-clear { padding: .5rem; color: var(--bs-link-color); text-underline-offset: .2em; white-space: nowrap; }
        .builder-catalog { display: grid; gap: .7rem; }
        .builder-product-card { display: grid; grid-template-columns: 92px minmax(0, 1fr) auto; align-items: center; gap: 1rem; min-width: 0; padding: .8rem 1rem; border: 1px solid var(--forge-border); border-radius: .7rem; background: var(--forge-surface-raised); }
        .builder-product-card:hover, .builder-product-card.is-selected { border-color: var(--bs-body-color); box-shadow: 0 8px 24px -20px #0009; }
        .builder-product-media { position: relative; display: grid; place-items: center; width: 92px; height: 92px; overflow: hidden; border-radius: .55rem; background: var(--forge-surface); color: var(--forge-muted); }
        .builder-product-media img { width: 100%; height: 100%; object-fit: contain; padding: .35rem; }
        .builder-product-placeholder { display: grid; place-items: center; gap: .2rem; font-size: .65rem; text-align: center; }
        .builder-product-copy { min-width: 0; }
        .builder-product-brand { margin: 0 0 .15rem; color: var(--forge-muted); font-size: .8rem; }
        .builder-product-name { margin: 0 0 .25rem; overflow-wrap: anywhere; font-size: 1rem; line-height: 1.3; }
        .builder-product-specs { margin: 0 0 .35rem; color: var(--forge-muted); font-size: .78rem; overflow-wrap: anywhere; }
        .builder-status { display: inline-block; padding: .18rem .45rem; border: 1px solid currentColor; border-radius: .35rem; font-size: .68rem; font-weight: 700; }
        .builder-status.compatible { color: #285b3b; background: #eff6f1; }
        .builder-status.incompatible { color: #8c2929; background: #fff2f2; }
        .builder-status.warning { color: #705015; background: #fff8e8; }
        .builder-status.unknown { color: var(--forge-muted); background: var(--forge-surface); }
        .builder-issue { margin-top: .35rem; color: #8c2929; font-size: .75rem; }
        .builder-issue summary, .builder-specs summary { cursor: pointer; color: var(--bs-link-color); text-decoration: underline; text-underline-offset: .2em; }
        .builder-specs { margin-top: .45rem; font-size: .78rem; }
        .builder-specs dl { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: .25rem .75rem; margin: .55rem 0 0; padding: .65rem; border-radius: .5rem; background: var(--forge-surface); }
        .builder-specs dt { color: var(--forge-muted); font-weight: 400; }
        .builder-specs dd { margin: 0; font-weight: 700; text-align: right; }
        .builder-product-action { display: grid; justify-items: end; gap: .55rem; min-width: 7rem; }
        .builder-product-price { margin: 0; font-size: 1.15rem; font-weight: 800; white-space: nowrap; }
        .builder-product-action .btn { min-width: 7.2rem; }
        .builder-empty { padding: 2rem; border: 1px dashed var(--forge-border); border-radius: .7rem; color: var(--forge-muted); text-align: center; }
        .builder-pagination { display: flex; justify-content: space-between; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--forge-border); }
        .builder-total-bar { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 1rem; padding: .9rem 1rem; border: 1px solid var(--forge-border); border-radius: .7rem; background: var(--forge-surface); }
        .builder-total-bar strong { display: block; font-size: 1.1rem; }
        .builder-total-bar span { color: var(--forge-muted); font-size: .8rem; }
        .builder-detail { position: sticky; top: 1rem; min-height: min(720px, calc(100dvh - 2rem)); padding: 1rem; border-left: 1px solid var(--forge-border); background: var(--forge-surface); }
        .builder-detail-card { min-height: 100%; padding: 1rem; border: 1px solid var(--forge-border); border-radius: .75rem; background: var(--forge-surface-raised); }
        .builder-detail h2 { margin: 0; font-size: 1.25rem; line-height: 1.25; }
        .builder-detail-kicker { margin: 0 0 .4rem; color: var(--forge-muted); font-size: .8rem; }
        .builder-detail-image { display: block; width: 100%; height: 250px; margin: 1rem 0; object-fit: contain; border-radius: .55rem; background: var(--forge-surface); }
        .builder-detail-placeholder { display: grid; place-items: center; height: 250px; margin: 1rem 0; border-radius: .55rem; background: var(--forge-surface); color: var(--forge-muted); }
        .builder-detail-price { margin: .8rem 0 1rem; font-size: 1.4rem; font-weight: 800; }
        .builder-detail dl { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: .55rem .75rem; margin: 0; padding-top: .8rem; border-top: 1px solid var(--forge-border); font-size: .82rem; }
        .builder-detail dt { color: var(--forge-muted); }
        .builder-detail dd { margin: 0; max-width: 13rem; font-weight: 700; text-align: right; overflow-wrap: anywhere; }
        .builder-summary-list { display: grid; gap: .35rem; margin: 1rem 0; padding: 0; list-style: none; font-size: .82rem; }
        .builder-summary-list li { display: flex; justify-content: space-between; gap: .75rem; padding-bottom: .35rem; border-bottom: 1px solid var(--forge-border); }
        .builder-summary-list span:first-child { color: var(--forge-muted); }
        @media (max-width: 1199.98px) {
            .builder-shell { grid-template-columns: 160px minmax(0, 1fr); }
            .builder-detail { grid-column: 1 / -1; position: static; min-height: 0; border-top: 1px solid var(--forge-border); border-left: 0; }
            .builder-detail-card { min-height: 0; }
        }
        @media (max-width: 767.98px) {
            .builder-page { width: calc(100% - 1rem); padding-top: 1rem; }
            .builder-shell { display: block; overflow: visible; border: 0; background: transparent; }
            .builder-rail { position: static; min-height: 0; padding: .75rem; border: 1px solid var(--forge-border); border-radius: .75rem; }
            .builder-rail-title { margin-bottom: .7rem; }
            .builder-rail-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .builder-rail-link { padding: .65rem .55rem; font-size: .8rem; }
            .builder-workspace { padding: 1rem 0; }
            .builder-filterbar { align-items: stretch; }
            .builder-filterbar select, .builder-filter-submit { flex: 1 1 calc(50% - .55rem); max-width: none; }
            .builder-filterbar .builder-sort { margin-left: 0; }
            .builder-clear { flex-basis: 100%; text-align: left; }
            .builder-product-card { grid-template-columns: 68px minmax(0, 1fr); align-items: start; padding: .75rem; }
            .builder-product-media { width: 68px; height: 68px; }
            .builder-product-action { grid-column: 1 / -1; display: flex; align-items: center; justify-content: space-between; width: 100%; min-width: 0; }
            .builder-product-price { font-size: 1rem; }
            .builder-detail { padding: 0; border: 0; background: transparent; }
            .builder-detail-card { padding: 1rem; }
            .builder-detail-image, .builder-detail-placeholder { height: 190px; }
            .builder-total-bar, .builder-pagination { align-items: stretch; flex-direction: column; }
            .builder-pagination .btn { width: 100%; }
        }
    </style>

    <div class="builder-page">
        <?php if ($message): ?><div class="alert alert-secondary" role="status"><?= e($message) ?></div><?php endif; ?>
        <?php if ($databaseError): ?><div class="alert alert-secondary" role="status">The database is unavailable. Start MySQL and try again.</div><?php endif; ?>

        <div class="builder-shell">
            <aside class="builder-rail" aria-label="Build components">
                <p class="builder-rail-title">Core components</p>
                <ol class="builder-rail-list">
                    <?php foreach ($steps as $stepCategory => $stepLabel): ?>
                        <li><a class="builder-rail-link" href="<?= e(url('builder.php?category=' . $stepCategory)) ?>" <?= $currentCategory === $stepCategory ? 'aria-current="step"' : '' ?>><?= e($stepLabel) ?></a></li>
                    <?php endforeach; ?>
                </ol>
            </aside>

            <section class="builder-workspace" aria-labelledby="parts-heading">
                <div class="builder-heading">
                    <div><p class="small fw-semibold text-uppercase text-secondary mb-1">PC builder</p><h1 id="parts-heading"><?= e($steps[$currentCategory]) ?> <span class="fw-normal">Selected:</span></h1><p><?= e($stepGuides[$currentCategory]) ?></p></div>
                    <span class="small text-secondary"><?= count($availableProducts) ?> shown</span>
                </div>

                <form class="builder-filterbar" method="get" action="<?= e(url('builder.php')) ?>">
                    <input type="hidden" name="category" value="<?= e($currentCategory) ?>">
                    <span class="builder-filter-label">Filter by</span>
                    <?php foreach ($currentFilterDefinitions as $field => $label): ?>
                        <label class="visually-hidden" for="builder-filter-<?= e($field) ?>"><?= e($label) ?></label>
                        <select id="builder-filter-<?= e($field) ?>" name="filter_<?= e($field) ?>">
                            <option value="">All <?= e($label) ?></option>
                            <?php foreach ($filterOptions[$field] as $option): ?><option value="<?= e($option) ?>" <?= $activeFilters[$field] === $option ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?>
                        </select>
                    <?php endforeach; ?>
                    <label class="visually-hidden" for="builder-sort">Sort products</label>
                    <select class="builder-sort" id="builder-sort" name="sort">
                        <?php foreach ($sortOptions as $sortValue => $sortLabel): ?><option value="<?= e($sortValue) ?>" <?= $sort === $sortValue ? 'selected' : '' ?>><?= e($sortLabel) ?></option><?php endforeach; ?>
                    </select>
                    <button class="builder-filter-submit" type="submit">Apply</button>
                    <a class="builder-clear" href="<?= e(url('builder.php?category=' . $currentCategory)) ?>">Clear filters</a>
                </form>

                <?php if (!$availableProducts): ?>
                    <p class="builder-empty">No components match these filters. Clear the filters to see all available parts.</p>
                <?php else: ?>
                    <div class="builder-catalog" aria-live="polite">
                        <?php foreach ($availableProducts as $product): ?>
                            <?php
                            $isSelected = (int) ($_SESSION['build'][$currentCategory] ?? 0) === (int) $product['id'];
                            $specifications = builderSpecifications($currentCategory, $product);
                            $imageUrl = $builderImageUrl($product);
                            $cardSpecifications = $specifications;
                            unset($cardSpecifications['Brand']);
                            $cardSpecifications = array_slice($cardSpecifications, 0, 3, true);
                            $review = $candidateReviews[$product['id']] ?? ['status' => 'unknown', 'messages' => []];
                            ?>
                            <article class="builder-product-card <?= $isSelected ? 'is-selected' : '' ?>">
                                <div class="builder-product-media">
                                    <?php if ($imageUrl): ?><img src="<?= e($imageUrl) ?>" alt="<?= e($product['name']) ?>" loading="lazy" width="320" height="240">
                                    <?php else: ?><div class="builder-product-placeholder"><span aria-hidden="true">▧</span><span>No image</span></div><?php endif; ?>
                                </div>
                                <div class="builder-product-copy">
                                    <p class="builder-product-brand"><?= e($product['brand'] ?? 'Brand not listed') ?></p>
                                    <h3 class="builder-product-name"><?= e($product['name']) ?></h3>
                                    <p class="builder-product-specs"><?= e($cardSpecifications ? implode(' · ', $cardSpecifications) : 'Specifications unavailable') ?></p>
                                    <span class="builder-status <?= e($review['status']) ?>"><?= e($badgeLabels[$review['status']] ?? $badgeLabels['unknown']) ?></span>
                                    <?php if ($review['messages']): ?><details class="builder-issue"><summary>Why this needs attention</summary><p class="mb-0 mt-1"><?= e(implode(' ', $review['messages'])) ?></p></details><?php endif; ?>
                                    <details class="builder-specs"><summary>View specifications</summary><dl><?php foreach ($specifications as $label => $value): ?><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd><?php endforeach; ?></dl></details>
                                </div>
                                <div class="builder-product-action">
                                    <p class="builder-product-price"><?= e(money($product['price'] ?? null)) ?></p>
                                    <form method="post" action="<?= e(url('builder.php')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="<?= $isSelected ? 'clear' : 'select' ?>">
                                        <input type="hidden" name="category" value="<?= e($currentCategory) ?>">
                                        <input type="hidden" name="product_id" value="<?= e((string) $product['id']) ?>">
                                        <button class="btn <?= $isSelected ? 'btn-dark' : 'btn-outline-dark' ?>" type="submit" aria-pressed="<?= $isSelected ? 'true' : 'false' ?>"><?= $isSelected ? '− Remove' : '+ Choose' ?></button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="builder-total-bar">
                    <div><strong>Total price: <?= e(money((string) $totalPrice)) ?></strong><span><?= count($selectedProducts) ?> of <?= count($steps) ?> components selected</span></div>
                    <a class="btn btn-primary" href="<?= e($nextCategory !== null ? url('builder.php?category=' . $nextCategory) : url('build-summary.php')) ?>"><?= e($nextCategory !== null ? 'Next: ' . $steps[$nextCategory] : 'Review build') ?> <span aria-hidden="true">&rarr;</span></a>
                </div>
            </section>

            <aside class="builder-detail" aria-labelledby="selected-heading">
                <div class="builder-detail-card">
                    <p class="builder-detail-kicker"><?= e($steps[$currentCategory]) ?> selected:</p>
                    <?php if ($selectedCurrent): ?>
                        <h2 id="selected-heading"><?= e($selectedCurrent['name']) ?></h2>
                        <?php if ($selectedCurrentImage): ?><img class="builder-detail-image" src="<?= e($selectedCurrentImage) ?>" alt="<?= e($selectedCurrent['name']) ?>" width="600" height="420">
                        <?php else: ?><div class="builder-detail-placeholder">Image unavailable</div><?php endif; ?>
                        <p class="builder-detail-price"><?= e(money($selectedCurrent['price'] ?? null)) ?></p>
                        <dl><?php foreach (builderSpecifications($currentCategory, $selectedCurrent) as $label => $value): ?><dt><?= e($label) ?></dt><dd><?= e($value) ?></dd><?php endforeach; ?></dl>
                    <?php else: ?>
                        <h2 id="selected-heading">Choose a component</h2>
                        <div class="builder-detail-placeholder">Your selected <?= e(strtolower($steps[$currentCategory])) ?> will appear here.</div>
                        <p class="text-secondary mb-0">Select a part from the list to see its details and add it to your build.</p>
                    <?php endif; ?>
                    <h3 class="h6 mt-4">Build summary</h3>
                    <ul class="builder-summary-list">
                        <?php foreach ($steps as $summaryCategory => $summaryLabel): ?><li><span><?= e($summaryLabel) ?></span><strong><?= e($selectedProducts[$summaryCategory]['name'] ?? 'Not selected') ?></strong></li><?php endforeach; ?>
                    </ul>
                    <p class="d-flex justify-content-between mb-2"><span>Estimated power</span><strong><?= e((string) $estimatedWatts) ?> W</strong></p>
                    <p class="d-flex justify-content-between mb-3"><span>Recommended PSU</span><strong><?= $recommendedWatts > 0 ? e((string) $recommendedWatts) . ' W' : 'Pending' ?></strong></p>
                    <a class="btn btn-primary w-100" href="<?= e(url('build-summary.php')) ?>">Review complete build</a>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>
