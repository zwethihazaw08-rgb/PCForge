<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/compatibility.php';

$categories = [
    'cpu' => 'Processor', 'mb' => 'Motherboard', 'memory' => 'Memory',
    'gpu' => 'Graphics Card', 'storage' => 'Storage', 'cooling' => 'Cooling',
    'psu' => 'Power Supply', 'case_box' => 'Case',
];
$selectedIds = is_array($_SESSION['build'] ?? null) ? $_SESSION['build'] : [];
$parts = [];
$unavailable = [];
$databaseError = false;
$relationships = ['case_form_factors' => [], 'cooling_sockets' => []];

try {
    foreach ($categories as $category => $label) {
        if (!isset($selectedIds[$category])) continue;
        $id = filter_var($selectedIds[$category], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            $unavailable[] = $label;
            continue;
        }
        // Only the fixed category list above supplies SQL table names.
        $statement = db()->prepare("SELECT * FROM `$category` WHERE id = :id AND status = 'active'");
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();
        if ($product) {
            $parts[$category] = $product;
        } else {
            $unavailable[] = $label;
        }
    }
    if (isset($parts['case_box'])) {
        $statement = db()->prepare('SELECT form_factor FROM case_motherboard_support WHERE case_id = :id');
        $statement->execute(['id' => $parts['case_box']['id']]);
        $relationships['case_form_factors'] = $statement->fetchAll(PDO::FETCH_COLUMN);
    }
    if (isset($parts['cooling'])) {
        $statement = db()->prepare('SELECT socket FROM cooling_socket_support WHERE cooling_id = :id');
        $statement->execute(['id' => $parts['cooling']['id']]);
        $relationships['cooling_sockets'] = $statement->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (PDOException $exception) {
    error_log('PCForge build review failed: ' . $exception->getMessage());
    http_response_code(503);
    $databaseError = true;
}

// Sum database DECIMAL prices as integer cents to avoid rounding during addition.
$totalCents = 0;
$missingPrices = [];
foreach ($parts as $category => $product) {
    $price = (string) ($product['price'] ?? '');
    if (!preg_match('/^(\d+)\.(\d{2})$/', $price, $amount)) {
        $missingPrices[] = $categories[$category];
        continue;
    }
    $totalCents += (int) $amount[1] * 100 + (int) $amount[2];
}
$total = intdiv($totalCents, 100) . '.' . str_pad((string) ($totalCents % 100), 2, '0', STR_PAD_LEFT);
$missingParts = array_diff_key($categories, $parts);

// These fallback values are estimates for one selected item/kit in each category.
$powerDefaults = ['cpu' => 65, 'gpu' => 150, 'mb' => 50, 'memory' => 10, 'storage' => 5, 'cooling' => 5];
$powerRows = [];
$estimatedWatts = 0;
foreach ($powerDefaults as $category => $fallback) {
    if (!isset($parts[$category])) continue;
    $field = in_array($category, ['cpu', 'gpu'], true) ? 'tdp' : 'power_watts';
    $watts = filter_var($parts[$category][$field] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $isEstimate = $watts === false;
    $watts = $isEstimate ? $fallback : $watts;
    $powerRows[$category] = ['watts' => $watts, 'fallback' => $isEstimate];
    $estimatedWatts += $watts;
}
$recommendedWatts = $estimatedWatts > 0 ? (int) ceil($estimatedWatts * 1.35 / 50) * 50 : 0;
$gpuRecommendation = (int) ($parts['gpu']['recommended_psu_watts'] ?? 0);
$recommendedWatts = max($recommendedWatts, $gpuRecommendation);

// The checker uses descriptive names for motherboard and case records.
$checkBuild = $parts;
$checkBuild['motherboard'] = $parts['mb'] ?? [];
$checkBuild['case'] = $parts['case_box'] ?? [];
$checkBuild['estimated_watts'] = $estimatedWatts;
$checkBuild['recommended_psu_watts'] = $recommendedWatts;
$checks = checkBuildCompatibility($checkBuild, $relationships);
$expectedChecks = ['CPU / Motherboard', 'Memory / Motherboard', 'GPU / Case', 'Motherboard / Case', 'Cooling / CPU', 'Cooling / Case', 'Storage / Motherboard', 'PSU Capacity'];
foreach ($expectedChecks as $name) {
    if (!isset($checks[$name])) {
        $checks[$name] = compatibilityResult('unknown', 'Choose the required parts to run this check.');
    }
}
if (array_diff_key($powerDefaults, $parts)) {
    $checks['PSU Capacity'] = compatibilityResult('unknown', 'Choose all power-consuming parts before judging PSU capacity.');
}
$issues = count(array_filter($checks, fn ($check) => $check['status'] === 'incompatible'));
$needsReview = count(array_filter($checks, fn ($check) => in_array($check['status'], ['unknown', 'warning'], true)));
// Show each check beside both components it involves.
$componentChecks = [
    'cpu' => ['CPU / Motherboard', 'Cooling / CPU'],
    'mb' => ['CPU / Motherboard', 'Memory / Motherboard', 'Motherboard / Case', 'Storage / Motherboard'],
    'memory' => ['Memory / Motherboard'],
    'gpu' => ['GPU / Case'],
    'storage' => ['Storage / Motherboard'],
    'cooling' => ['Cooling / CPU', 'Cooling / Case'],
    'psu' => ['PSU Capacity'],
    'case_box' => ['GPU / Case', 'Motherboard / Case', 'Cooling / Case'],
];
$statusLabels = ['compatible' => '✓ Compatible', 'incompatible' => '✕ Incompatible', 'unknown' => '? Needs check', 'warning' => '! Warning'];
$pageTitle = 'Review Your Build';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content" tabindex="-1">
    <style>
        .review-status { position: relative; }
        .review-status .compatibility-status { font-size: 0.7rem; padding: 0.2rem 0.45rem; cursor: help; }
        .review-status-tip {
            display: none; position: absolute; top: 100%; left: 0; z-index: 10;
            width: min(280px, 75vw); padding: 0.75rem; border: 1px solid #dedede;
            border-radius: 0.6rem; background: #ffffff; color: #202020;
            box-shadow: 0 6px 20px #00000015; font-size: 0.8rem;
        }
        .review-status:hover .review-status-tip,
        .review-status:focus-within .review-status-tip { display: block; }
    </style>
    <div class="container section-padding">
        <p class="small fw-semibold text-uppercase text-secondary">Your PC, in detail</p>
        <h1>Review your build</h1>
        <p class="lead text-secondary">Check your parts, budget, and compatibility before moving on.</p>
        <a class="forge-save-btn mb-4" href="<?= e(url('saved-builds.php')) ?>">
            <span class="forge-save-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 4h12a1 1 0 0 1 1 1v15l-7-4-7 4V5a1 1 0 0 1 1-1z"/></svg>Save build</span>
        </a>
        <a class="ms-3" href="<?= e(url('saved-builds.php')) ?>">View saved builds</a>
        <?php if ($databaseError): ?>
            <div class="alert alert-secondary" role="alert">Your build could not be loaded. Please try again later.</div>
        <?php elseif (!$parts): ?>
            <div class="border rounded-4 p-5 text-center">
                <h2 class="h4">Your build is waiting for its first part.</h2>
                <p class="text-secondary"><?= $unavailable ? 'Previously selected parts are no longer available. Choose replacements in the builder.' : 'Start with a processor, then choose the components around it.' ?></p>
                <a class="btn btn-primary" href="<?= e(url('builder.php')) ?>">Start building &rarr;</a>
            </div>
        <?php else: ?>
            <?php if ($unavailable): ?>
                <p class="alert alert-warning">Unavailable selections: <?= e(implode(', ', $unavailable)) ?>. Choose replacements below.</p>
            <?php endif; ?>
            <div class="d-flex flex-wrap gap-2 mb-4">
                <span class="badge bg-dark"><?= count($parts) ?> / <?= count($categories) ?> parts selected</span>
                <span class="compatibility-status <?= $issues ? 'incompatible' : ($needsReview ? 'unknown' : 'compatible') ?>">
                    <?= $issues ? $issues . ' compatibility issue(s)' : ($needsReview ? 'Checks need attention' : 'Basic checks passed') ?>
                </span>
            </div>
            <div class="row g-4 align-items-start">
                <section class="col-lg-8" aria-labelledby="review-parts-heading">
                    <h2 id="review-parts-heading" class="h4 mb-3">Your components</h2>
                    <div class="border rounded-4">
                        <?php foreach ($categories as $category => $label): ?>
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-2 border-bottom">
                                <div class="flex-grow-1">
                                    <p class="small text-secondary mb-1"><?= e($label) ?></p>
                                    <p class="fw-semibold mb-0"><?= e($parts[$category]['name'] ?? 'Not selected') ?></p>
                                    <?php if (isset($parts[$category])): ?>
                                        <small class="text-secondary"><?= $parts[$category]['stock'] === null ? 'Availability unconfirmed' : ((int) $parts[$category]['stock'] > 0 ? 'In stock' : 'Out of stock') ?></small>
                                    <?php endif; ?>
                                </div>
                                <span><?= isset($parts[$category]) ? e(money($parts[$category]['price'])) : '—' ?></span>
                                <a class="btn btn-sm btn-outline-dark" href="<?= e(url('builder.php?category=' . $category)) ?>" aria-label="<?= e('Change ' . $label) ?>"><?= isset($parts[$category]) ? 'Change' : 'Choose' ?></a>
                                <?php if (isset($parts[$category])): ?>
                                    <?php
                                    $rowStatus = 'compatible';
                                    $severity = ['compatible' => 0, 'unknown' => 1, 'warning' => 2, 'incompatible' => 3];
                                    $rowMessages = [];
                                    foreach ($componentChecks[$category] as $checkName) {
                                        $check = $checks[$checkName];
                                        if ($severity[$check['status']] > $severity[$rowStatus]) $rowStatus = $check['status'];
                                        if ($check['status'] !== 'compatible') $rowMessages[] = $check['message'];
                                    }
                                    ?>
                                    <div class="review-status">
                                        <button type="button" class="compatibility-status <?= e($rowStatus) ?>" aria-describedby="review-tip-<?= e($category) ?>" aria-label="<?= e($label . ': ' . $statusLabels[$rowStatus]) ?>"><?= e($statusLabels[$rowStatus]) ?></button>
                                        <div class="review-status-tip" id="review-tip-<?= e($category) ?>" role="tooltip">
                                            <?php foreach ($rowMessages ?: ['Basic checks passed for the selected parts.'] as $rowMessage): ?>
                                                <p class="mb-1"><?= e($rowMessage) ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="small text-secondary">Not checked</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <details class="mt-3">
                        <summary class="fw-semibold">View all compatibility checks</summary>
                        <?php foreach ($checks as $name => $check): ?>
                            <div class="py-3 border-bottom">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                    <h3 class="h6 mb-0"><?= e($name) ?></h3>
                                    <span class="compatibility-status <?= e($check['status']) ?>"><?= e(ucfirst($check['status'])) ?></span>
                                </div>
                                <p class="small text-secondary mb-0"><?= e($check['message']) ?></p>
                            </div>
                        <?php endforeach; ?>
                        <p class="small text-secondary mt-3">Basic checks do not cover BIOS versions, PSU connectors, or PCIe lane sharing. Confirm exact manufacturer specifications before purchasing.</p>
                    </details>
                </section>
                <aside class="col-lg-4" aria-labelledby="review-total-heading">
                    <div class="build-summary">
                        <h2 id="review-total-heading" class="h4">Build totals</h2>
                        <p class="small text-secondary mb-1"><?= $missingPrices ? 'Known-price subtotal' : 'Selected parts total' ?></p>
                        <p class="fs-2 fw-bold mb-3"><?= e(money($total)) ?></p>
                        <?php if ($missingPrices): ?><p class="small">Prices unavailable for <?= e(implode(', ', $missingPrices)) ?>. The total is incomplete.</p><?php endif; ?>
                        <?php if ($missingParts): ?><p class="small text-secondary">Choose <?= e(implode(', ', $missingParts)) ?> to complete these builder steps.</p><?php endif; ?>
                        <h3 class="h6 mt-4">Estimated system power</h3>
                        <dl class="small mb-3">
                            <?php foreach ($powerRows as $category => $power): ?>
                                <div class="d-flex justify-content-between gap-2 py-1">
                                    <dt class="fw-normal"><?= e($categories[$category]) ?><?= $power['fallback'] ? ' (estimate)' : '' ?></dt>
                                    <dd class="mb-0"><?= $power['watts'] ?> W</dd>
                                </div>
                            <?php endforeach; ?>
                        </dl>
                        <div class="d-flex justify-content-between border-top pt-2"><span>Estimated load</span><strong><?= $estimatedWatts ?> W</strong></div>
                        <div class="d-flex justify-content-between gap-2 mt-2"><span>Recommended PSU</span><strong><?= $recommendedWatts ? $recommendedWatts . ' W' : 'Pending' ?></strong></div>
                        <p class="small text-secondary mt-3">Adds 35% headroom, rounds up to a 50 W step, and respects the GPU's listed PSU recommendation. Based on selected parts only; this is not a wall-power measurement.</p>
                        <a class="btn btn-primary w-100" href="<?= e(url('builder.php')) ?>">Continue editing</a>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/includes/ai-assistant.php'; ?>
<script src="<?= e(url('assets/js/ai-assistant.js?v=' . filemtime(__DIR__ . '/assets/js/ai-assistant.js'))) ?>" defer></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
