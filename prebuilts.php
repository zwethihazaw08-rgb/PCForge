<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/compatibility.php';

$partLabels = [
    'cpu' => 'Processor', 'mb' => 'Motherboard', 'memory' => 'Memory', 'gpu' => 'Graphics card',
    'storage' => 'Storage', 'cooling' => 'Cooling', 'psu' => 'Power supply', 'case_box' => 'Case',
];

// Edit these component IDs to curate builds. Names, prices and specifications
// always come from the catalogue; these are never copied from a submitted form.
$templates = [
    'everyday' => [
        'name' => 'Forge Everyday', 'use' => 'Everyday', 'number' => '01',
        'description' => 'A compact starting point for your desk, daily tasks, and downtime.',
        'ids' => ['cpu' => 8, 'mb' => 3, 'memory' => 2, 'gpu' => 3, 'storage' => 1, 'cooling' => 1, 'psu' => 1, 'case_box' => 2],
    ],
    'play' => [
        'name' => 'Forge Play', 'use' => 'Gaming', 'number' => '02',
        'description' => 'Build around your games with dedicated graphics and room to make it yours.',
        'ids' => ['cpu' => 1, 'mb' => 1, 'memory' => 1, 'gpu' => 5, 'storage' => 1, 'cooling' => 1, 'psu' => 1, 'case_box' => 1],
    ],
    'studio' => [
        'name' => 'Forge Studio', 'use' => 'Creating', 'number' => '03',
        'description' => 'More memory and storage for the projects you want to bring to life.',
        'ids' => ['cpu' => 5, 'mb' => 5, 'memory' => 4, 'gpu' => 6, 'storage' => 4, 'cooling' => 2, 'psu' => 5, 'case_box' => 3],
    ],
];

function prebuiltCents($price): ?int
{
    if (!preg_match('/^(\d+)\.(\d{2})$/', (string) $price, $matches)) return null;
    return (int) $matches[1] * 100 + (int) $matches[2];
}

function prebuiltMoney(int $cents): string
{
    return money(intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT));
}

function prebuiltImage(?array $part): ?string
{
    $stored = (string) ($part['image_url'] ?? '');
    if (filter_var($stored, FILTER_VALIDATE_URL) && strtolower(parse_url($stored, PHP_URL_SCHEME) ?? '') === 'https') return $stored;
    if ($stored !== '' && is_file(__DIR__ . '/assets/images/' . basename($stored))) return url('assets/images/' . rawurlencode(basename($stored)));
    return null;
}

function prebuiltIllustration(): void
{
    // A neutral concept illustration, not a photograph of the selected case.
    ?>
    <svg class="prebuilt-tower" viewBox="0 0 360 260" fill="none" aria-hidden="true">
        <ellipse cx="183" cy="238" rx="112" ry="10" fill="currentColor" opacity=".07"/>
        <path d="M88 46 223 23 283 57 149 82Z" fill="var(--forge-surface-raised)" stroke="currentColor" stroke-width="2"/>
        <path d="M88 46 223 23V212L88 234Z" fill="var(--forge-surface-raised)" stroke="currentColor" stroke-width="2"/>
        <path d="M223 23 283 57V224L223 212Z" fill="var(--forge-surface)" stroke="currentColor" stroke-width="2"/>
        <path d="M101 59 209 41V182L101 200Z" fill="var(--forge-surface)" stroke="currentColor" opacity=".7"/>
        <path d="M110 72 190 59V162L110 175Z" stroke="currentColor" opacity=".25"/>
        <path d="M172 71v46m10-48v46m10-48v46" stroke="currentColor" stroke-width="5" opacity=".5"/>
        <ellipse cx="139" cy="104" rx="21" ry="26" stroke="currentColor" stroke-width="2"/>
        <path d="m139 81 6 16-6 7-13-7m32 5-14 9-5-7-1-18m-14 33 11-12 4-3 7 17" stroke="currentColor" stroke-width="4" opacity=".45"/>
        <circle cx="139" cy="104" r="4" fill="currentColor"/>
        <path d="m108 149 95-16v26l-95 16Z" fill="var(--forge-surface-raised)" stroke="currentColor"/>
        <ellipse cx="135" cy="157" rx="10" ry="11" stroke="currentColor"/>
        <ellipse cx="177" cy="149" rx="10" ry="11" stroke="currentColor"/>
        <path d="m102 209 106-17v13l-106 17Z" fill="currentColor" opacity=".25"/>
        <?php foreach ([91, 139, 187] as $y): ?>
            <ellipse cx="251" cy="<?= $y ?>" rx="18" ry="23" stroke="currentColor" stroke-width="2" opacity=".6"/>
            <ellipse cx="251" cy="<?= $y ?>" rx="5" ry="7" fill="currentColor" opacity=".6"/>
        <?php endforeach; ?>
        <circle cx="251" cy="60" r="3" fill="currentColor"/>
    </svg>
    <?php
}

$builds = [];
$error = '';
$loadFailed = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') csrf_verify();

try {
    $connection = db();
    $catalog = [];
    foreach ($partLabels as $category => $label) {
        $ids = array_unique(array_map(fn($template) => $template['ids'][$category], $templates));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        // The table name comes only from the fixed list above.
        $query = $connection->prepare("SELECT * FROM `$category` WHERE id IN ($placeholders) AND status = 'active'");
        $query->execute(array_values($ids));
        $catalog[$category] = array_column($query->fetchAll(), null, 'id');
    }
    $caseSupport = [];
    foreach ($connection->query('SELECT case_id, form_factor FROM case_motherboard_support') as $row) $caseSupport[$row['case_id']][] = $row['form_factor'];
    $coolerSupport = [];
    foreach ($connection->query('SELECT cooling_id, socket FROM cooling_socket_support') as $row) $coolerSupport[$row['cooling_id']][] = $row['socket'];

    foreach ($templates as $key => $template) {
        $build = $template + ['parts' => [], 'cents' => 0, 'missing' => [], 'missing_prices' => false, 'stock' => 'Parts in stock', 'watts' => 0];
        $fallbackWatts = ['cpu' => 65, 'mb' => 50, 'memory' => 10, 'gpu' => 150, 'storage' => 5, 'cooling' => 5];
        foreach ($partLabels as $category => $label) {
            $part = $catalog[$category][$template['ids'][$category]] ?? null;
            $build['parts'][$category] = $part;
            if (!$part) {
                $build['missing'][] = $label;
                continue;
            }
            $cents = prebuiltCents($part['price']);
            $build['missing_prices'] = $build['missing_prices'] || $cents === null;
            $build['cents'] += $cents ?? 0;
            if ($part['stock'] !== null && (int) $part['stock'] === 0) $build['stock'] = 'Some parts out of stock';
            elseif ($part['stock'] === null && $build['stock'] === 'Parts in stock') $build['stock'] = 'Stock needs checking';
            if (isset($fallbackWatts[$category])) {
                $field = in_array($category, ['cpu', 'gpu'], true) ? 'tdp' : 'power_watts';
                $build['watts'] += (int) ($part[$field] ?? $fallbackWatts[$category]);
            }
        }
        $recommended = max((int) ceil($build['watts'] * 1.35 / 50) * 50, (int) ($build['parts']['gpu']['recommended_psu_watts'] ?? 0));
        $build['checks'] = checkBuildCompatibility(array_merge($build['parts'], [
            'motherboard' => $build['parts']['mb'] ?? [], 'case' => $build['parts']['case_box'] ?? [],
            'estimated_watts' => $build['watts'], 'recommended_psu_watts' => $recommended,
        ]), [
            'case_form_factors' => $caseSupport[$template['ids']['case_box']] ?? [],
            'cooling_sockets' => $coolerSupport[$template['ids']['cooling']] ?? [],
        ]);
        $statuses = array_column($build['checks'], 'status');
        $build['fit'] = in_array('incompatible', $statuses, true) ? 'incompatible'
            : (($build['missing'] || count($statuses) < 8 || array_diff($statuses, ['compatible'])) ? 'unknown' : 'compatible');
        $build['fit_label'] = ['compatible' => 'Basic checks passed', 'unknown' => 'Review compatibility', 'incompatible' => 'Fit issue found'][$build['fit']];
        $builds[$key] = $build;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $key = is_string($_POST['build'] ?? null) ? $_POST['build'] : '';
        if (($_POST['action'] ?? null) !== 'customize' || !isset($builds[$key])) {
            http_response_code(400);
            $error = 'Choose one of the builds below to customize.';
        } elseif ($builds[$key]['missing']) {
            http_response_code(409);
            $error = 'Some parts in this build are no longer available. Your current build has been kept.';
        } else {
            // Replace all eight slots together; never mix a template with an older build.
            $_SESSION['build'] = $templates[$key]['ids'];
            redirect('builder.php?category=cpu');
        }
    }
} catch (PDOException $exception) {
    error_log('PCForge prebuilt load failed: ' . $exception->getMessage());
    http_response_code(503);
    $loadFailed = true;
    $error = 'Builds are temporarily unavailable. Please try again shortly.';
}

$hasCurrentBuild = !empty($_SESSION['build']);
$pageTitle = 'Custom PC Builds';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main id="main-content" tabindex="-1">
    <style>
        .prebuilt-page { width: calc(100% - 2rem); max-width: 1400px; margin-inline: auto; }
        .prebuilt-intro { display: flex; align-items: end; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 2rem; }
        .prebuilt-intro h1 { max-width: 760px; font-size: clamp(2.2rem, 4.8vw, 4rem); letter-spacing: -.05em; }
        .prebuilt-intro .lead { max-width: 670px; margin-bottom: 0; }
        .prebuilt-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin: 2rem 0 1.5rem; }
        .prebuilt-filters { display: flex; gap: .5rem; flex-wrap: wrap; }
        .prebuilt-filter { border: 1px solid var(--forge-border); border-radius: 999px; background: var(--forge-surface-raised); color: var(--bs-body-color); padding: .5rem 1rem; font-size: .85rem; }
        .prebuilt-filter[aria-pressed="true"] { background: var(--bs-body-color); color: var(--bs-body-bg); border-color: var(--bs-body-color); }
        .prebuilt-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem; }
        .prebuilt-card { min-width: 0; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--forge-border); border-radius: 1.25rem; background: var(--forge-surface-raised); }
        .prebuilt-card[hidden] { display: none; }
        .prebuilt-art { position: relative; display: grid; place-items: center; height: 265px; padding: 1.25rem; background-color: var(--forge-surface); background-image: radial-gradient(var(--forge-border) 1px, transparent 1px); background-size: 18px 18px; color: var(--bs-body-color); }
        .prebuilt-art svg { width: 100%; height: 100%; max-width: 360px; }
        .prebuilt-art .prebuilt-case-photo { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; padding: 2rem; background: var(--forge-surface); }
        .prebuilt-art-label { position: absolute; left: 1rem; bottom: .7rem; font-size: .65rem; color: var(--forge-muted); background: var(--forge-surface); padding: .15rem .4rem; border-radius: .3rem; }
        .prebuilt-card-body { display: flex; flex-direction: column; flex: 1; padding: 1.5rem; }
        .prebuilt-eyebrow { display: flex; justify-content: space-between; gap: 1rem; font-size: .7rem; text-transform: uppercase; letter-spacing: .12em; color: var(--forge-muted); }
        .prebuilt-card h2 { font-size: 1.55rem; margin: .75rem 0; }
        .prebuilt-description { color: var(--forge-muted); font-size: .9rem; min-height: 3.2em; }
        .prebuilt-specs { margin: 0 0 1.25rem; font-size: .85rem; }
        .prebuilt-specs > div { display: grid; grid-template-columns: 65px minmax(0, 1fr); gap: .75rem; padding: .65rem 0; border-bottom: 1px solid var(--forge-border); }
        .prebuilt-specs dt { color: var(--forge-muted); font-weight: 400; }
        .prebuilt-specs dd { margin: 0; overflow-wrap: anywhere; }
        .prebuilt-fit { font-size: .72rem; padding: .25rem .5rem; align-self: start; }
        .prebuilt-price { margin-top: auto; padding-top: 1.25rem; }
        .prebuilt-price strong { display: block; font-size: 1.7rem; letter-spacing: -.04em; }
        .prebuilt-price span { font-size: .75rem; color: var(--forge-muted); }
        .prebuilt-stock { font-size: .75rem; color: var(--forge-muted); margin: .75rem 0 1rem; }
        .prebuilt-note { color: var(--forge-muted); font-size: .8rem; margin-top: 1.25rem; }
        .prebuilt-dialog { width: min(1000px, calc(100% - 2rem)); max-height: calc(100dvh - 2rem); margin: auto; padding: 0; border: 1px solid var(--forge-border); border-radius: 1.25rem; background: var(--forge-surface-raised); color: var(--bs-body-color); }
        .prebuilt-dialog[open] { display: grid; grid-template-rows: auto minmax(0, 1fr) auto; }
        .prebuilt-dialog::backdrop { background: #0009; }
        body:has(.prebuilt-dialog[open]) { overflow: hidden; }
        .prebuilt-dialog-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--forge-border); }
        .prebuilt-dialog-header h2 { margin: 0; font-size: 1.5rem; }
        .prebuilt-close { width: 40px; height: 40px; border: 1px solid var(--forge-border); border-radius: 50%; background: var(--forge-surface); color: var(--bs-body-color); font-size: 1.5rem; }
        .prebuilt-dialog-body { overflow-y: auto; padding: 1.5rem; }
        .prebuilt-detail-grid { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 1.5rem; }
        .prebuilt-detail-grid > * { min-width: 0; }
        .prebuilt-detail-grid .prebuilt-art { height: 220px; border-radius: 1rem; }
        .prebuilt-part-list { list-style: none; padding: 0; margin: 0; }
        .prebuilt-part { display: grid; grid-template-columns: 48px minmax(0, 1fr) auto; align-items: center; gap: .75rem; border-bottom: 1px solid var(--forge-border); padding: .75rem 0; }
        .prebuilt-part-image { position: relative; display: grid; place-items: center; width: 48px; height: 48px; border-radius: .5rem; background: var(--forge-surface); color: var(--forge-muted); font-size: .65rem; overflow: hidden; }
        .prebuilt-part-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; padding: .25rem; background: var(--forge-surface); }
        .prebuilt-part small { display: block; color: var(--forge-muted); font-size: .7rem; }
        .prebuilt-part a, .prebuilt-part-name { color: var(--bs-body-color); font-size: .85rem; overflow-wrap: anywhere; text-underline-offset: 3px; }
        .prebuilt-part-price { font-size: .8rem; text-align: right; }
        .prebuilt-checks { margin-top: 1.25rem; padding: 1rem; border: 1px solid var(--forge-border); border-radius: .8rem; font-size: .8rem; }
        .prebuilt-checks summary { cursor: pointer; font-weight: 600; }
        .prebuilt-checks ul { padding-left: 1.25rem; margin: 1rem 0 0; }
        .prebuilt-checks li + li { margin-top: .65rem; }
        .prebuilt-dialog-footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; border-top: 1px solid var(--forge-border); padding: 1rem 1.5rem; }
        .prebuilt-dialog-footer p { flex: 1 1 230px; margin: 0; font-size: .75rem; color: var(--forge-muted); }
        @media (max-width: 1100px) { .prebuilt-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 700px) {
            .prebuilt-grid, .prebuilt-detail-grid { grid-template-columns: minmax(0, 1fr); }
            .prebuilt-dialog-body, .prebuilt-dialog-header, .prebuilt-dialog-footer { padding: 1rem; }
            .prebuilt-part { grid-template-columns: 40px minmax(0, 1fr); }
            .prebuilt-part-image { width: 40px; height: 40px; }
            .prebuilt-part-price { grid-column: 2; text-align: left; }
            .prebuilt-dialog-footer form, .prebuilt-dialog-footer .btn { width: 100%; }
            .prebuilt-description { min-height: 0; }
        }
    </style>
    <div class="prebuilt-page section-padding">
        <header class="prebuilt-intro">
            <div>
                <p class="small text-secondary text-uppercase fw-semibold">PCForge / Custom builds</p>
                <h1>A head start.<br>A build of your own.</h1>
                <p class="lead text-secondary">Start with a complete configuration. Explore every part, then change what you want in the builder.</p>
            </div>
            <a class="btn btn-outline-dark" href="<?= e(url('builder.php')) ?>">Build from scratch &rarr;</a>
        </header>
        <?php if ($error): ?><p class="alert alert-warning" role="alert"><?= e($error) ?></p><?php endif; ?>
        <?php if ($hasCurrentBuild): ?>
            <p class="prebuilt-note">Already working on a build? Customizing a configuration replaces your current selections. <a href="<?= e(url('saved-builds.php')) ?>">Save your current build</a> to keep it.</p>
        <?php endif; ?>
        <?php if (!$loadFailed): ?>
            <div class="prebuilt-toolbar">
                <div class="prebuilt-filters" role="group" aria-label="Filter builds by use" hidden>
                    <?php foreach (['All', 'Everyday', 'Gaming', 'Creating'] as $use): ?>
                        <button class="prebuilt-filter" type="button" data-build-filter="<?= e($use) ?>" aria-pressed="<?= $use === 'All' ? 'true' : 'false' ?>"><?= e($use) ?></button>
                    <?php endforeach; ?>
                </div>
                <span class="small text-secondary" id="prebuilt-count" role="status"><?= count($builds) ?> builds to make your own</span>
            </div>
            <div class="prebuilt-grid">
                <?php foreach ($builds as $key => $build): ?>
                    <article class="prebuilt-card" data-build-use="<?= e($build['use']) ?>">
                        <div class="prebuilt-art">
                            <?php prebuiltIllustration(); $caseImage = prebuiltImage($build['parts']['case_box']); ?>
                            <?php if ($caseImage): ?><img class="prebuilt-case-photo" src="<?= e($caseImage) ?>" alt="<?= e($build['parts']['case_box']['name']) ?>" onerror="this.hidden = true; this.nextElementSibling.textContent = 'Build illustration';"><?php endif; ?>
                            <span class="prebuilt-art-label"><?= $caseImage ? 'Case preview' : 'Build illustration' ?></span>
                        </div>
                        <div class="prebuilt-card-body">
                            <div class="prebuilt-eyebrow"><span><?= e($build['use']) ?></span><span>Build <?= e($build['number']) ?></span></div>
                            <h2><?= e($build['name']) ?></h2>
                            <p class="prebuilt-description"><?= e($build['description']) ?></p>
                            <dl class="prebuilt-specs">
                                <?php foreach (['cpu' => 'CPU', 'gpu' => 'GPU', 'memory' => 'RAM', 'storage' => 'Storage'] as $category => $label): ?>
                                    <div><dt><?= e($label) ?></dt><dd><?= e($build['parts'][$category]['name'] ?? 'Part unavailable') ?></dd></div>
                                <?php endforeach; ?>
                            </dl>
                            <span class="compatibility-status prebuilt-fit <?= e($build['fit']) ?>"><?= e($build['fit_label']) ?></span>
                            <div class="prebuilt-price"><strong><?= e(prebuiltMoney($build['cents'])) ?></strong><span><?= $build['missing'] || $build['missing_prices'] ? 'Known-price subtotal · incomplete' : 'Current component total' ?></span></div>
                            <p class="prebuilt-stock"><?= e($build['missing'] ? 'Some parts unavailable' : $build['stock']) ?></p>
                            <button class="btn btn-primary" type="button" data-open-build="build-<?= e($key) ?>" aria-haspopup="dialog" aria-controls="build-<?= e($key) ?>">View build &rarr;</button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <p class="prebuilt-note">Demo configurations using the current catalogue. Totals cover the listed components only; assembly, delivery, and applicable taxes are not included.</p>
            <noscript><p>Enable JavaScript to open the full build viewer and customize a configuration, or <a href="<?= e(url('builder.php')) ?>">use the PC builder</a>.</p></noscript>

            <?php foreach ($builds as $key => $build): ?>
                <dialog class="prebuilt-dialog" id="build-<?= e($key) ?>" aria-labelledby="build-title-<?= e($key) ?>">
                    <header class="prebuilt-dialog-header">
                        <h2 id="build-title-<?= e($key) ?>"><?= e($build['name']) ?></h2>
                        <form method="dialog"><button class="prebuilt-close" type="submit" aria-label="Close build details">&times;</button></form>
                    </header>
                    <div class="prebuilt-dialog-body">
                        <div class="prebuilt-detail-grid">
                            <div>
                                <div class="prebuilt-art"><?php prebuiltIllustration(); ?><span class="prebuilt-art-label">Build illustration</span></div>
                                <p class="small text-secondary mt-3"><?= e($build['description']) ?></p>
                                <div class="prebuilt-price"><strong><?= e(prebuiltMoney($build['cents'])) ?></strong><span><?= $build['missing'] || $build['missing_prices'] ? 'Known-price subtotal · incomplete' : 'Current component total' ?></span></div>
                                <p class="prebuilt-stock"><?= e($build['missing'] ? 'Some parts unavailable' : $build['stock']) ?></p>
                                <span class="compatibility-status prebuilt-fit <?= e($build['fit']) ?>"><?= e($build['fit_label']) ?></span>
                                <p class="prebuilt-note">Change any component in the builder. You can then review the build, save it to your account, or add it to your cart.</p>
                            </div>
                            <div>
                                <h3 class="h5">Inside this build</h3>
                                <p class="small text-secondary">One of each listed component. Select a name to see its specifications.</p>
                                <ul class="prebuilt-part-list">
                                    <?php foreach ($partLabels as $category => $label): ?>
                                        <?php $part = $build['parts'][$category]; $image = prebuiltImage($part); ?>
                                        <li class="prebuilt-part">
                                            <span class="prebuilt-part-image" aria-hidden="true"><?= e(strtoupper(substr($label, 0, 2))) ?><?php if ($image): ?><img src="<?= e($image) ?>" alt="" loading="lazy" onerror="this.hidden = true;"><?php endif; ?></span>
                                            <div><small><?= e($label) ?></small><?php if ($part): ?><a href="<?= e(url('product.php?' . http_build_query(['category' => $category, 'id' => $part['id']]))) ?>"><?= e($part['name']) ?></a><?php else: ?><span class="prebuilt-part-name">Part unavailable</span><?php endif; ?></div>
                                            <span class="prebuilt-part-price"><?= $part && prebuiltCents($part['price']) !== null ? e(money($part['price'])) : 'Price unavailable' ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <details class="prebuilt-checks">
                                    <summary>Compatibility details</summary>
                                    <p class="mt-3 mb-0">Estimated component load: <?= (int) $build['watts'] ?> W. These are basic fit checks; confirm manufacturer requirements before buying.</p>
                                    <?php if ($build['missing']): ?><p class="mt-2 mb-0">Unavailable: <?= e(implode(', ', $build['missing'])) ?>.</p><?php endif; ?>
                                    <ul><?php foreach ($build['checks'] as $label => $check): ?><li><strong><?= e($label) ?></strong><br><?= e($check['message']) ?></li><?php endforeach; ?></ul>
                                </details>
                            </div>
                        </div>
                    </div>
                    <footer class="prebuilt-dialog-footer">
                        <p><?= $hasCurrentBuild ? 'This replaces the parts in your current builder. Save your current build first if you want to keep it.' : 'Load these parts into the builder, then swap any component.' ?></p>
                        <form method="post" action="<?= e(url('prebuilts.php')) ?>">
                            <?= csrf_field() ?><input type="hidden" name="action" value="customize"><input type="hidden" name="build" value="<?= e($key) ?>">
                            <button class="btn btn-primary" type="submit" <?= $build['missing'] ? 'disabled' : '' ?>><?= $build['missing'] ? 'Build unavailable' : 'Customize this build →' ?></button>
                        </form>
                    </footer>
                </dialog>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <script>
        (() => {
            const filters = [...document.querySelectorAll('[data-build-filter]')];
            const cards = [...document.querySelectorAll('[data-build-use]')];
            const group = document.querySelector('.prebuilt-filters');
            if (group) group.hidden = false;
            filters.forEach(button => button.addEventListener('click', () => {
                filters.forEach(filter => filter.setAttribute('aria-pressed', String(filter === button)));
                cards.forEach(card => { card.hidden = button.dataset.buildFilter !== 'All' && card.dataset.buildUse !== button.dataset.buildFilter; });
                const count = cards.filter(card => !card.hidden).length;
                document.querySelector('#prebuilt-count').textContent = `${count} ${count === 1 ? 'build' : 'builds'} to make your own`;
            }));
            document.querySelectorAll('[data-open-build]').forEach(button => button.addEventListener('click', () => {
                document.getElementById(button.dataset.openBuild).showModal();
            }));
        })();
    </script>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
