<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/compatibility.php';

$steps = [
    'cpu' => 'Processor',
    'mb' => 'Motherboard',
    'memory' => 'Memory',
    'gpu' => 'Graphics Card',
    'storage' => 'Storage',
    'cooling' => 'Cooling',
    'psu' => 'Power Supply',
    'case_box' => 'Case',
];
$stepCategories = array_keys($steps);

if (!isset($_SESSION['build']) || !is_array($_SESSION['build'])) {
    $_SESSION['build'] = [];
}

$selectedIds = $_SESSION['build'];
$requestedCategory = (string) ($_GET['category'] ?? $_POST['category'] ?? '');
$currentCategory = array_key_exists($requestedCategory, $steps) ? $requestedCategory : 'cpu';
$databaseError = false;
$message = null;

function builderProduct(string $category, int $id): ?array
{
    $statement = db()->prepare("SELECT * FROM `$category` WHERE id = :id AND status = 'active' LIMIT 1");
    $statement->execute(['id' => $id]);
    $product = $statement->fetch();

    return $product ?: null;
}

function builderSpecifications(string $category, array $product): array
{
    $fields = [
        'cpu' => ['brand' => 'Brand', 'series' => 'Series', 'socket' => 'Socket', 'cores' => 'Cores', 'threads' => 'Threads', 'base_clock' => 'Base clock', 'memory_type' => 'Memory support', 'tdp' => 'TDP'],
        'mb' => ['brand' => 'Brand', 'socket' => 'Socket', 'memory_type' => 'Memory type', 'chipset' => 'Chipset', 'size' => 'Form factor', 'ram_slots' => 'RAM slots', 'sata_ports' => 'SATA ports', 'nvme_slots' => 'M.2 slots', 'wifi' => 'Wi-Fi'],
        'memory' => ['brand' => 'Brand', 'type' => 'Type', 'capacity' => 'Capacity', 'speed' => 'Speed', 'latency' => 'Latency', 'modules' => 'Modules'],
        'gpu' => ['brand' => 'Brand', 'vram' => 'VRAM', 'length_mm' => 'Length', 'boost_clock' => 'Boost clock', 'tdp' => 'Power draw', 'recommended_psu_watts' => 'Recommended PSU', 'ports' => 'Ports'],
        'storage' => ['brand' => 'Brand', 'type' => 'Type', 'interface' => 'Interface', 'capacity' => 'Capacity', 'read_speed' => 'Read speed', 'write_speed' => 'Write speed'],
        'cooling' => ['brand' => 'Brand', 'type' => 'Type', 'size' => 'Size', 'radiator_size_mm' => 'Radiator', 'height_mm' => 'Height'],
        'psu' => ['brand' => 'Brand', 'wattage_watts' => 'Wattage', 'rating' => 'Rating', 'modularity' => 'Modularity'],
        'case_box' => ['brand' => 'Brand', 'size' => 'Size', 'color' => 'Color', 'max_gpu_length' => 'GPU clearance', 'max_radiator_size' => 'Radiator support', 'max_cooler_height_mm' => 'Cooler clearance'],
    ][$category] ?? [];

    $specifications = [];
    foreach ($fields as $field => $label) {
        $value = $product[$field] ?? null;
        if ($value !== null && $value !== '') {
            $suffix = in_array($field, ['length_mm', 'max_gpu_length', 'max_radiator_size', 'radiator_size_mm', 'height_mm'], true) ? ' mm' : '';
            if (in_array($field, ['tdp', 'wattage_watts'], true)) {
                $suffix = ' W';
            }
            $specifications[$label] = (string) $value . $suffix;
        }
    }

    return $specifications;
}

// The product details page links here with a category and ID. Validate it before
// storing anything in the session, then redirect to a clean builder URL.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && array_key_exists('id', $_GET) && array_key_exists($requestedCategory, $steps)) {
    $requestedId = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($requestedId !== false) {
        try {
            if (builderProduct($requestedCategory, $requestedId)) {
                $_SESSION['build'][$requestedCategory] = $requestedId;
            }
        } catch (PDOException $exception) {
            error_log('PCForge builder selection failed: ' . $exception->getMessage());
            $databaseError = true;
        }
    }
    redirect('builder.php?category=' . rawurlencode($requestedCategory));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string) ($_POST['action'] ?? 'select');
    $postedCategory = (string) ($_POST['category'] ?? '');
    $postedId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($action === 'clear' && array_key_exists($postedCategory, $steps)) {
        unset($_SESSION['build'][$postedCategory]);
        redirect('builder.php?category=' . rawurlencode($postedCategory));
    }

    if (!array_key_exists($postedCategory, $steps) || $postedId === false) {
        $message = 'Please choose a valid component.';
    } else {
        try {
            if (builderProduct($postedCategory, $postedId)) {
                $_SESSION['build'][$postedCategory] = $postedId;
                redirect('builder.php?category=' . rawurlencode($postedCategory));
            }
            $message = 'That component is no longer available.';
        } catch (PDOException $exception) {
            error_log('PCForge builder update failed: ' . $exception->getMessage());
            $databaseError = true;
            $message = 'The component could not be selected right now.';
        }
    }
}

$selectedProducts = [];
$availableProducts = [];
$relationships = ['case_form_factors' => [], 'cooling_sockets' => []];
$candidateSupport = [];

try {
    $connection = db();

    foreach ($steps as $category => $label) {
        if (!empty($_SESSION['build'][$category])) {
            $product = builderProduct($category, (int) $_SESSION['build'][$category]);
            if ($product) {
                $selectedProducts[$category] = $product;
            } else {
                unset($_SESSION['build'][$category]);
            }
        }
    }

    $availableStatement = $connection->query("SELECT * FROM `$currentCategory` WHERE status = 'active' ORDER BY name LIMIT 60");
    $availableProducts = $availableStatement->fetchAll();
    // Load support lists in one query for the current catalog, not per product.
    if ($availableProducts && in_array($currentCategory, ['case_box', 'cooling'], true)) {
        $supportTable = $currentCategory === 'case_box' ? 'case_motherboard_support' : 'cooling_socket_support';
        $idField = $currentCategory === 'case_box' ? 'case_id' : 'cooling_id';
        $valueField = $currentCategory === 'case_box' ? 'form_factor' : 'socket';
        $ids = array_column($availableProducts, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $support = $connection->prepare("SELECT `$idField`, `$valueField` FROM `$supportTable` WHERE `$idField` IN ($placeholders)");
        $support->execute($ids);
        foreach ($support->fetchAll() as $row) $candidateSupport[$row[$idField]][] = $row[$valueField];
    }

    if (!empty($selectedProducts['case_box'])) {
        $support = $connection->prepare('SELECT form_factor FROM case_motherboard_support WHERE case_id = :case_id');
        $support->execute(['case_id' => $selectedProducts['case_box']['id']]);
        $relationships['case_form_factors'] = $support->fetchAll(PDO::FETCH_COLUMN);
    }
    if (!empty($selectedProducts['cooling'])) {
        $support = $connection->prepare('SELECT socket FROM cooling_socket_support WHERE cooling_id = :cooling_id');
        $support->execute(['cooling_id' => $selectedProducts['cooling']['id']]);
        $relationships['cooling_sockets'] = $support->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (PDOException $exception) {
    error_log('PCForge builder query failed: ' . $exception->getMessage());
    $databaseError = true;
}

$totalPrice = 0.0;
$estimatedWatts = 0;
foreach ($selectedProducts as $category => $product) {
    $totalPrice += (float) ($product['price'] ?? 0);
    $fallbackWatts = ['cpu' => 65, 'gpu' => 150, 'mb' => 50, 'memory' => 10, 'storage' => 5, 'cooling' => 5, 'psu' => 0, 'case_box' => 0];
    $powerField = match ($category) {
        'cpu' => 'tdp',
        'gpu' => 'tdp',
        'mb' => 'power_watts',
        'memory' => 'power_watts',
        'storage' => 'power_watts',
        'cooling' => 'power_watts',
        default => null,
    };
    $estimatedWatts += $powerField !== null && $product[$powerField] !== null
        ? (int) $product[$powerField]
        : ($fallbackWatts[$category] ?? 0);
}

$recommendedWatts = $estimatedWatts > 0 ? (int) ceil(($estimatedWatts * 1.35) / 50) * 50 : 0;
if (!empty($selectedProducts['gpu']['recommended_psu_watts'])) {
    $recommendedWatts = max($recommendedWatts, (int) $selectedProducts['gpu']['recommended_psu_watts']);
}
$buildChecks = checkBuildCompatibility(array_merge($selectedProducts, [
    'motherboard' => $selectedProducts['mb'] ?? [],
    'case' => $selectedProducts['case_box'] ?? [],
    'estimated_watts' => $estimatedWatts,
    'recommended_psu_watts' => $recommendedWatts,
]), $relationships);

$stepGuides = [
    'cpu' => 'Start with a CPU socket. Your motherboard and cooler must support it.',
    'mb' => 'Match the CPU socket first, then check memory type and case size.',
    'memory' => 'Match the motherboard memory generation. DDR4 and DDR5 are not interchangeable.',
    'gpu' => 'Check GPU length against case clearance. Revisit PSU capacity after changing graphics cards.',
    'storage' => 'NVMe drives need NVMe-capable M.2 slots; SATA drives need SATA ports.',
    'cooling' => 'Check CPU socket support, then air-cooler height or radiator clearance.',
    'psu' => 'Choose other power-consuming parts first, then allow headroom above the estimated load.',
    'case_box' => 'Check motherboard form factor, GPU length, and cooler clearance together.',
];
$checkNames = [
    'cpu' => ['CPU / Motherboard', 'Cooling / CPU'],
    'mb' => ['CPU / Motherboard', 'Memory / Motherboard', 'Motherboard / Case', 'Storage / Motherboard'],
    'memory' => ['Memory / Motherboard'], 'gpu' => ['GPU / Case'],
    'storage' => ['Storage / Motherboard'], 'cooling' => ['Cooling / CPU', 'Cooling / Case'],
    'psu' => ['PSU Capacity'], 'case_box' => ['GPU / Case', 'Motherboard / Case', 'Cooling / Case'],
];
$candidateReviews = [];
foreach ($availableProducts as $candidate) {
    $trial = $selectedProducts;
    $trial[$currentCategory] = $candidate;
    $trial['motherboard'] = $trial['mb'] ?? [];
    $trial['case'] = $trial['case_box'] ?? [];
    $trialRelationships = $relationships;
    if ($currentCategory === 'case_box') $trialRelationships['case_form_factors'] = $candidateSupport[$candidate['id']] ?? [];
    if ($currentCategory === 'cooling') $trialRelationships['cooling_sockets'] = $candidateSupport[$candidate['id']] ?? [];
    $trial['estimated_watts'] = $estimatedWatts;
    $trial['recommended_psu_watts'] = $recommendedWatts;
    $trialChecks = checkBuildCompatibility($trial, $trialRelationships);
    if ($currentCategory === 'psu' && array_diff(['cpu', 'mb', 'memory', 'gpu', 'storage', 'cooling'], array_keys($selectedProducts))) {
        $trialChecks['PSU Capacity'] = compatibilityResult('unknown', 'Choose CPU, motherboard, memory, GPU, storage and cooling before judging PSU capacity.');
    }
    $review = ['status' => 'compatible', 'messages' => []];
    $severity = ['compatible' => 0, 'unknown' => 1, 'warning' => 2, 'incompatible' => 3];
    foreach ($checkNames[$currentCategory] as $name) {
        $check = $trialChecks[$name] ?? compatibilityResult('unknown', 'Select both components to check this match.');
        if ($severity[$check['status']] > $severity[$review['status']]) $review['status'] = $check['status'];
        if ($check['status'] === 'incompatible') {
            $review['messages'][] = $name . ': ' . $check['message'];
        }
    }
    $candidateReviews[$candidate['id']] = $review;
}
$badgeLabels = ['compatible' => '✓ Basic checks passed', 'unknown' => '? Needs check', 'warning' => '! Warning', 'incompatible' => '✕ Incompatible'];

$filterDefinitions = [
    'cpu' => ['brand' => 'Manufacturer', 'series' => 'Series', 'socket' => 'Socket'],
    'mb' => ['brand' => 'Manufacturer', 'chipset' => 'Chipset', 'size' => 'Form factor'],
    'memory' => ['brand' => 'Manufacturer', 'type' => 'Memory type', 'capacity' => 'Capacity'],
    'gpu' => ['brand' => 'Manufacturer', 'vram' => 'VRAM', 'ports' => 'Ports'],
    'storage' => ['brand' => 'Manufacturer', 'type' => 'Drive type', 'interface' => 'Interface'],
    'cooling' => ['brand' => 'Manufacturer', 'type' => 'Cooling type', 'size' => 'Size'],
    'psu' => ['brand' => 'Manufacturer', 'rating' => 'Rating', 'modularity' => 'Modularity'],
    'case_box' => ['brand' => 'Manufacturer', 'size' => 'Size', 'color' => 'Color'],
];
$currentFilterDefinitions = $filterDefinitions[$currentCategory] ?? ['brand' => 'Manufacturer'];
$filterOptions = [];
$activeFilters = [];
foreach ($currentFilterDefinitions as $field => $label) {
    $values = [];
    foreach ($availableProducts as $product) {
        $value = trim((string) ($product[$field] ?? ''));
        if ($value !== '') $values[$value] = $value;
    }
    natcasesort($values);
    $filterOptions[$field] = array_values($values);
    $requestedValue = trim((string) ($_GET['filter_' . $field] ?? ''));
    $activeFilters[$field] = in_array($requestedValue, $filterOptions[$field], true) ? $requestedValue : '';
}
$sortOptions = ['name' => 'Best match', 'price_low' => 'Price: low to high', 'price_high' => 'Price: high to low'];
$sort = array_key_exists((string) ($_GET['sort'] ?? ''), $sortOptions) ? (string) $_GET['sort'] : 'name';
$availableProducts = array_values(array_filter($availableProducts, static function (array $product) use ($activeFilters): bool {
    foreach ($activeFilters as $field => $value) {
        if ($value !== '' && (string) ($product[$field] ?? '') !== $value) return false;
    }
    return true;
}));
usort($availableProducts, static function (array $left, array $right) use ($sort): int {
    if ($sort === 'price_low' || $sort === 'price_high') {
        $comparison = (float) ($left['price'] ?? 0) <=> (float) ($right['price'] ?? 0);
        return $sort === 'price_high' ? -$comparison : $comparison;
    }
    return strnatcasecmp((string) ($left['name'] ?? ''), (string) ($right['name'] ?? ''));
});
$builderImageUrl = static function (array $product): ?string {
    $storedImage = (string) ($product['image_url'] ?? '');
    if (filter_var($storedImage, FILTER_VALIDATE_URL) && strtolower(parse_url($storedImage, PHP_URL_SCHEME) ?? '') === 'https') {
        return $storedImage;
    }
    if ($storedImage !== '' && is_file(__DIR__ . '/assets/images/' . basename($storedImage))) {
        return url('assets/images/' . rawurlencode(basename($storedImage)));
    }
    return null;
};

$pageTitle = 'Build Your PC';
require __DIR__ . '/includes/builder-view.php';
exit;
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content" tabindex="-1">
    <style>
    .builder-page {
        width: calc(100% - 2rem);
        max-width: 1600px;
        margin-inline: auto;
    }

    .builder-page :is(a, button, input, select, summary) {
        touch-action: manipulation;
    }

    .builder-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        align-items: start;
        gap: 1rem;
    }

    .builder-layout>* {
        min-width: 0;
    }

    .builder-sidebar {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.5rem;
        margin: 0;
        padding: 0.75rem;
        list-style: none;
        border: 1px solid var(--forge-border);
        border-radius: 1rem;
        background: var(--forge-surface-raised);
    }

    .builder-sidebar .btn {
        display: block;
        width: 100%;
        padding: 0.75rem;
        text-align: left;
        touch-action: manipulation;
    }

    .builder-catalog {
        height: clamp(360px, 65vh, 720px);
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-gutter: stable;
        padding: 0;
        border: 1px solid #dedede;
        border-radius: 1rem;
        background: #ffffff;
    }

    .builder-product-list>div+div {
        border-top: 1px solid #dedede;
    }

    .builder-product {
        display: flex;
        align-items: center;
        gap: 1rem;
        min-width: 0;
        padding: 1rem;
        background: #ffffff;
    }

    .builder-product:hover {
        background: #fafafa;
    }

    .builder-product.is-selected {
        background: #f5f5f5;
        box-shadow: inset 3px 0 #171717;
    }

    .builder-product-media {
        position: relative;
        flex: 0 0 72px;
        height: 72px;
        display: grid;
        place-items: center;
        border-radius: 0.65rem;
        background: #eeeeee;
    }

    .builder-product-media img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 0.4rem;
        background: #eeeeee;
        border-radius: inherit;
    }

    .builder-image-placeholder {
        display: grid;
        justify-items: center;
        gap: 0.1rem;
        font-size: 0.6rem;
        color: #707070;
    }

    .builder-image-placeholder svg {
        width: 26px;
        height: 26px;
    }

    .builder-product-body {
        flex: 1;
        min-width: 0;
    }

    .builder-product-body h3 {
        margin: 0 0 0.35rem;
        overflow-wrap: anywhere;
    }

    .builder-quick-specs {
        color: #606060;
        font-size: 0.8rem;
        margin-bottom: 0.35rem;
        overflow-wrap: anywhere;
    }

    .builder-details-button {
        border: 0;
        padding: 0.2rem 0;
        background: transparent;
        color: #505050;
        font-size: 0.8rem;
        text-decoration: underline;
        text-underline-offset: 3px;
        touch-action: manipulation;
    }

    .builder-product-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.6rem;
        flex: 0 0 auto;
    }

    .builder-product-actions .product-price {
        font-size: 1rem;
        margin: 0;
    }

    .builder-product-actions .btn {
        padding: 0.4rem 0.8rem;
        min-width: 100px;
        font-size: 0.8rem;
        touch-action: manipulation;
    }

    .builder-hint {
        padding: 0.75rem 1rem;
        border: 1px solid #dedede;
        border-radius: 0.75rem;
        background: #f5f5f5;
        font-size: 0.85rem;
    }

    .builder-check .compatibility-status {
        font-size: 0.7rem;
        padding: 0.2rem 0.45rem;
    }

    .builder-check button {
        cursor: help;
    }

    .builder-issue-tooltip {
        --bs-tooltip-max-width: 280px;
        --bs-tooltip-bg: #ffffff;
        --bs-tooltip-color: #202020;
        --bs-tooltip-opacity: 1;
    }

    .builder-issue-tooltip .tooltip-inner {
        border: 1px solid #dedede;
        padding: 0.65rem 0.8rem;
        border-radius: 0.6rem;
        box-shadow: 0 4px 16px #00000018;
        text-align: left;
        white-space: pre-line;
        font-size: 0.75rem;
    }

    .builder-summary-panel .build-summary {
        position: static;
        max-height: 60vh;
        overflow-y: auto;
    }

    .builder-summary-drawer {
        --bs-offcanvas-width: min(90vw, 390px);
    }

    .builder-native-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1040;
        background: #0008;
    }

    .builder-native-modal {
        display: block !important;
        position: fixed;
        inset: 0;
        z-index: 1055;
        overflow-y: auto;
        padding: 1rem;
        background: #0008;
    }

    .builder-native-modal .modal-dialog {
        min-height: calc(100% - 2rem);
        display: flex;
        align-items: center;
    }

    .builder-native-drawer {
        position: fixed !important;
        inset: 0 auto 0 0;
        z-index: 1045;
        width: min(90vw, 390px);
        max-width: 100%;
        overflow: hidden;
        background: var(--forge-surface-raised);
        transform: translateX(-100%);
        visibility: hidden;
        transition: transform 180ms ease, visibility 180ms ease;
    }

    .builder-native-drawer.is-open {
        transform: translateX(0);
        visibility: visible;
    }

    .builder-summary-drawer:not(.show):not(.is-open),
    .modal:not(.show):not(.builder-native-modal) {
        pointer-events: none;
    }

    .builder-summary-toggle {
        position: fixed;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        z-index: 1030;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.2rem;
        width: 44px;
        min-height: 64px;
        padding: 0.55rem 0.2rem;
        border: 1px solid #171717;
        border-radius: 0 0.65rem 0.65rem 0;
        background: #171717;
        color: #ffffff;
        box-shadow: 2px 3px 12px #00000020;
        font-size: 0.65rem;
        touch-action: manipulation;
    }

    @media (max-width: 991.98px) {
        .builder-summary-drawer .offcanvas-body {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }

        .builder-summary-panel .build-summary {
            flex: 1;
            min-height: 0;
            max-height: none;
        }

        .builder-summary-drawer .summary-review-link {
            flex-shrink: 0;
        }
    }

    @media (min-width: 992px) {
        .builder-layout {
            grid-template-columns: 180px minmax(0, 1fr) 280px;
        }

        .builder-sidebar {
            position: sticky;
            top: 6rem;
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
            max-height: calc(100dvh - 8rem);
            overflow-y: auto;
        }

        .builder-summary-panel {
            position: sticky;
            top: 6rem;
        }

        .builder-summary-panel .build-summary {
            max-height: calc(100dvh - 12rem);
        }

        .builder-summary-drawer .offcanvas-body {
            display: block;
            padding: 0;
        }
    }

    @media (max-width: 575.98px) {
        .builder-product {
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .builder-product-actions {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }
    }
    </style>
    <div class="builder-page section-padding">
        <p class="small fw-semibold text-uppercase text-secondary mb-2">PC builder</p>
        <h1>Build your PC</h1>
        <p class="lead text-secondary">Choose each part, then review fit, price, and estimated power.</p>

        <?php if ($message): ?><div class="alert alert-secondary" role="status"><?= e($message) ?></div><?php endif; ?>
        <?php if ($databaseError): ?><div class="alert alert-secondary" role="status">The database is unavailable. Start
            MySQL and try again.</div><?php endif; ?>

        <button class="builder-summary-toggle d-lg-none" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#builder-summary-drawer" aria-controls="builder-summary-drawer"
            aria-label="View build summary, <?= count($selectedProducts) ?> parts selected">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="4" y="3" width="16" height="18" rx="2" />
                <path d="M8 8h8M8 12h8M8 16h5" />
            </svg>
            <span>Build</span>
        </button>
        <div class="builder-layout">
            <ol class="builder-steps builder-sidebar" aria-label="Build steps">
                <?php foreach ($steps as $stepCategory => $stepLabel): ?>
                <li><a class="btn btn-sm <?= $currentCategory === $stepCategory ? 'btn-dark' : 'btn-outline-dark' ?>"
                        href="<?= e(url('builder.php?category=' . $stepCategory)) ?>"
                        <?= $currentCategory === $stepCategory ? 'aria-current="step"' : '' ?>><?= e($stepLabel) ?></a>
                </li>
                <?php endforeach; ?>
            </ol>
            <section class="builder-content" aria-labelledby="parts-heading">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 id="parts-heading" class="h4 mb-0" tabindex="-1">Choose a <?= e($steps[$currentCategory]) ?>
                    </h2>
                    <span class="small text-secondary"><?= count($availableProducts) ?> shown</span>
                </div>
                <div class="builder-hint mb-3">
                    <strong>Selection guide:</strong> <?= e($stepGuides[$currentCategory]) ?>
                    <p class="small text-secondary mb-0 mt-1">Badges compare each option with your current parts. Hover
                        or tap an incompatible badge to see the issue. Incompatible parts remain selectable.</p>
                </div>
                <div class="builder-catalog" id="builder-catalog" role="region" aria-labelledby="parts-heading"
                    tabindex="0">
                    <?php if (!$availableProducts): ?>
                    <div class="border rounded-4 p-5 text-center">
                        <p class="text-secondary mb-0">No active products are available for this step.</p>
                    </div>
                    <?php else: ?>
                    <div class="builder-product-list">
                        <?php foreach ($availableProducts as $product): ?>
                        <?php $isSelected = (int) ($_SESSION['build'][$currentCategory] ?? 0) === (int) $product['id']; ?>
                        <?php $specifications = builderSpecifications($currentCategory, $product); ?>
                        <?php
                            $storedImage = $product['image_url'] ?? '';
                            $imageUrl = null;
                            if (filter_var($storedImage, FILTER_VALIDATE_URL) && strtolower(parse_url($storedImage, PHP_URL_SCHEME) ?? '') === 'https') {
                                $imageUrl = $storedImage;
                            } elseif ($storedImage !== '' && is_file(__DIR__ . '/assets/images/' . basename($storedImage))) {
                                $imageUrl = url('assets/images/' . rawurlencode(basename($storedImage)));
                            }
                            $cardSpecifications = $specifications;
                            unset($cardSpecifications['Brand']);
                            $cardSpecifications = array_slice($cardSpecifications, 0, 3, true);
                            ?>
                        <div>
                            <article class="builder-product <?= $isSelected ? 'is-selected' : '' ?>">
                                <div class="builder-product-media">
                                    <div class="builder-image-placeholder">
                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.2" aria-hidden="true">
                                            <rect x="3" y="3" width="18" height="18" rx="3" />
                                            <circle cx="8" cy="8" r="1.5" />
                                            <path d="m4 18 5-5 4 3 3-5 5 7" />
                                        </svg>
                                        <span>No image</span>
                                    </div>
                                    <?php if ($imageUrl !== null): ?>
                                    <img src="<?= e($imageUrl) ?>" alt="<?= e($product['name']) ?>" loading="lazy"
                                        width="320" height="160" onerror="this.hidden = true;">
                                    <?php endif; ?>
                                </div>
                                <div class="builder-product-body">
                                    <p class="small text-secondary mb-1">
                                        <?= e($product['brand'] ?? 'Brand not listed') ?></p>
                                    <h3 class="h6"><?= e($product['name']) ?></h3>
                                    <p class="builder-quick-specs">
                                        <?= e($cardSpecifications ? implode(' · ', $cardSpecifications) : 'Specifications unavailable') ?>
                                    </p>
                                    <?php $review = $candidateReviews[$product['id']]; ?>
                                    <div class="builder-check">
                                        <?php if ($review['messages']): ?>
                                        <button type="button" class="compatibility-status incompatible"
                                            data-builder-issue
                                            title="<?= e(implode("\n\n", $review['messages'])) ?>"><?= e($badgeLabels['incompatible']) ?></button>
                                        <?php else: ?>
                                        <span
                                            class="compatibility-status <?= e($review['status']) ?>"><?= e($badgeLabels[$review['status']]) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="builder-details-button" type="button" data-bs-toggle="modal"
                                        data-bs-target="#specifications-<?= e($currentCategory . '-' . $product['id']) ?>"
                                        aria-label="<?= e('View specifications for ' . $product['name']) ?>">
                                        View specifications
                                    </button>
                                </div>
                                <div class="builder-product-actions">
                                    <p class="product-price"><?= e(money($product['price'] ?? null)) ?></p>
                                    <form method="post" action="<?= e(url('builder.php')) ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action"
                                            value="<?= $isSelected ? 'clear' : 'select' ?>">
                                        <input type="hidden" name="category" value="<?= e($currentCategory) ?>">
                                        <input type="hidden" name="product_id"
                                            value="<?= e((string) $product['id']) ?>">
                                        <button class="btn <?= $isSelected ? 'btn-dark' : 'btn-outline-dark' ?>"
                                            type="submit" aria-pressed="<?= $isSelected ? 'true' : 'false' ?>"
                                            aria-label="<?= e(($isSelected ? 'Remove ' : 'Choose ') . $product['name']) ?>"><?= $isSelected ? '− Remove' : '+ Choose' ?></button>
                                    </form>
                                </div>
                            </article>
                            <div class="modal fade"
                                id="specifications-<?= e($currentCategory . '-' . $product['id']) ?>" tabindex="-1"
                                aria-labelledby="specifications-title-<?= e($currentCategory . '-' . $product['id']) ?>"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div>
                                                <p class="small text-secondary mb-1"><?= e($steps[$currentCategory]) ?>
                                                </p>
                                                <h2 class="modal-title h5"
                                                    id="specifications-title-<?= e($currentCategory . '-' . $product['id']) ?>">
                                                    <?= e($product['name']) ?></h2>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <dl class="mb-0">
                                                <?php foreach ($specifications as $label => $value): ?>
                                                <div
                                                    class="d-flex justify-content-between align-items-center gap-3 py-3 border-bottom">
                                                    <dt class="mb-0 text-secondary fw-normal"><?= e($label) ?></dt>
                                                    <dd class="mb-0 text-end fw-semibold"><?= e($value) ?></dd>
                                                </div>
                                                <?php endforeach; ?>
                                            </dl>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-dark"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php
                $currentStepIndex = array_search($currentCategory, $stepCategories, true);
                $previousCategory = $currentStepIndex > 0 ? $stepCategories[$currentStepIndex - 1] : null;
                $nextCategory = $currentStepIndex < count($stepCategories) - 1 ? $stepCategories[$currentStepIndex + 1] : null;
                ?>
                <div class="d-flex justify-content-between gap-3 mt-4 pt-4 border-top">
                    <?php if ($previousCategory !== null): ?>
                    <a class="btn btn-outline-dark"
                        href="<?= e(url('builder.php?category=' . $previousCategory)) ?>">&larr; Previous</a>
                    <?php else: ?>
                    <span></span>
                    <?php endif; ?>
                    <?php if ($nextCategory !== null): ?>
                    <a class="btn btn-primary" href="<?= e(url('builder.php?category=' . $nextCategory)) ?>">Next:
                        <?= e($steps[$nextCategory]) ?> &rarr;</a>
                    <?php else: ?>
                    <a class="btn btn-primary" href="<?= e(url('build-summary.php')) ?>">Review Build &rarr;</a>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="builder-summary-panel" aria-labelledby="summary-heading">
                <div class="offcanvas-lg offcanvas-start builder-summary-drawer" tabindex="-1"
                    id="builder-summary-drawer" aria-labelledby="summary-drawer-title">
                    <div class="offcanvas-header">
                        <h2 class="offcanvas-title h5" id="summary-drawer-title">Build summary</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                            data-bs-target="#builder-summary-drawer" aria-label="Close build summary"></button>
                    </div>
                    <div class="offcanvas-body">
                        <div class="build-summary">
                            <h2 id="summary-heading" class="h4 d-none d-lg-block">Build summary</h2>
                            <?php if (!$selectedProducts): ?><p class="text-secondary">Your selected parts will appear
                                here.</p><?php endif; ?>
                            <ul class="list-unstyled mb-4">
                                <?php foreach ($steps as $summaryCategory => $summaryLabel): ?>
                                <li class="d-flex justify-content-between gap-3 py-2 border-bottom">
                                    <span class="text-secondary"><?= e($summaryLabel) ?></span>
                                    <span
                                        class="text-end"><?= e($selectedProducts[$summaryCategory]['name'] ?? 'Not selected') ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="d-flex justify-content-between"><span>Total
                                    price</span><strong><?= e(money((string) $totalPrice)) ?></strong></div>
                            <div class="d-flex justify-content-between"><span>Estimated system
                                    power</span><strong><?= e((string) $estimatedWatts) ?> W</strong></div>
                            <div class="d-flex justify-content-between"><span>Recommended
                                    PSU</span><strong><?= $recommendedWatts > 0 ? e((string) $recommendedWatts) . ' W' : 'Pending' ?></strong>
                            </div>

                            <?php if ($buildChecks): ?>
                            <h3 class="h6 mt-4">Compatibility</h3>
                            <?php foreach ($buildChecks as $check): ?>
                            <p class="small mb-2"><span
                                    class="compatibility-status <?= e($check['status']) ?>"><?= e(ucfirst($check['status'])) ?></span><br><?= e($check['message']) ?>
                            </p>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <a class="btn btn-primary w-100 mt-3 summary-review-link"
                            href="<?= e(url('build-summary.php')) ?>">Review complete build</a>
                        <?php if (count($selectedProducts) === count($steps)): ?>
                        <form method="post" action="<?= e(url('cart.php')) ?>" class="mt-2">
                            <?= csrf_field() ?><input type="hidden" name="action" value="add_build">
                            <button class="btn btn-outline-dark w-100" type="submit">Add to cart</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</main>
<script>
(() => {
    const bindBuilderNavigation = () => {
        const main = document.querySelector('#main-content');
        if (!main || main.dataset.ajaxReady === 'true') return;
        main.dataset.ajaxReady = 'true';

        // Keep the builder's touch controls usable if Bootstrap's optional
        // CDN JavaScript is unavailable. Desktop browsers with Bootstrap
        // continue to use its native modal/offcanvas components.
        if (!window.bootstrap) {
            let backdrop = null;
            const closeNative = () => {
                main.querySelectorAll('.builder-native-modal').forEach((modal) => {
                    modal.classList.remove('builder-native-modal');
                    modal.setAttribute('aria-hidden', 'true');
                });
                main.querySelector('#builder-summary-drawer')?.classList.remove('builder-native-drawer',
                    'is-open');
                backdrop?.remove();
                backdrop = null;
                document.body.classList.remove('modal-open');
            };
            const openNative = (target, type) => {
                if (!target) return;
                closeNative();
                if (type === 'modal') {
                    target.classList.add('builder-native-modal');
                    target.removeAttribute('aria-hidden');
                } else {
                    target.classList.add('builder-native-drawer', 'is-open');
                }
                backdrop = document.createElement('div');
                backdrop.className = 'builder-native-backdrop';
                backdrop.addEventListener('click', closeNative, {
                    once: true
                });
                document.body.append(backdrop);
                document.body.classList.add('modal-open');
            };
            main.querySelectorAll('[data-bs-toggle="modal"]').forEach((trigger) => trigger.addEventListener(
                'click', () => {
                    openNative(document.querySelector(trigger.dataset.bsTarget), 'modal');
                }));
            main.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach((trigger) => trigger.addEventListener(
                'click', () => {
                    openNative(document.querySelector(trigger.dataset.bsTarget), 'offcanvas');
                }));
            main.querySelectorAll('[data-bs-dismiss="modal"], [data-bs-dismiss="offcanvas"]').forEach((
                trigger) => trigger.addEventListener('click', closeNative));
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeNative();
            }, {
                once: false
            });
        }

        // Keep links and forms native. This lets touch browsers deliver the
        // click/submit without an AJAX handler swallowing the interaction.
        if (window.bootstrap)[...main.querySelectorAll('[data-builder-issue]')].forEach((badge) =>
            new bootstrap.Tooltip(badge, {
                container: 'body',
                boundary: document.body,
                placement: 'top',
                trigger: 'hover focus',
                customClass: 'builder-issue-tooltip',
                html: false,
            })
        );
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindBuilderNavigation, {
            once: true
        });
    } else {
        bindBuilderNavigation();
    }
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>