<?php

require_once __DIR__ . '/includes/functions.php';

$categories = ['cpu' => 'Processors', 'gpu' => 'Graphics Cards', 'mb' => 'Motherboards', 'memory' => 'Memory', 'storage' => 'Storage', 'psu' => 'Power Supplies', 'case_box' => 'Cases', 'cooling' => 'Cooling', 'fans' => 'Fans', 'monitor' => 'Monitors'];
$category = $_GET['category'] ?? 'cpu';
$notice = '';
if (!is_string($category) || !isset($categories[$category])) {
    $category = 'cpu';
    $notice = 'Choose a valid component category.';
}

// Accept the existing product-page Compare link, or up to three form selections.
$requestedIds = $_GET['ids'] ?? [$_GET['id'] ?? ''];
$selectedIds = [];
if (!is_array($requestedIds)) $requestedIds = [];
foreach (array_slice($requestedIds, 0, 3) as $value) {
    $id = is_scalar($value) ? filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
    if ($id !== false && !in_array($id, $selectedIds, true)) $selectedIds[] = $id;
}

$options = [];
$products = [];
$databaseError = false;
try {
    // Table names are restricted to the category map, never arbitrary URL input.
    $options = db()->query("SELECT * FROM `$category` WHERE status = 'active' ORDER BY name")->fetchAll();
    if ($selectedIds) {
        $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
        $statement = db()->prepare("SELECT * FROM `$category` WHERE status = 'active' AND id IN ($placeholders)");
        $statement->execute($selectedIds);
        $records = array_column($statement->fetchAll(), null, 'id');
        foreach ($selectedIds as $id) {
            if (isset($records[$id])) $products[] = $records[$id];
        }
        if (count($products) !== count($selectedIds)) $notice = 'Some selected components are unavailable in this category. Choose replacements below.';
        $selectedIds = array_map('intval', array_column($products, 'id'));
    }
} catch (PDOException $exception) {
    error_log('PCForge comparison query failed: ' . $exception->getMessage());
    http_response_code(503);
    $databaseError = true;
}

$categoryFields = [
    'cpu' => ['series' => 'Series', 'socket' => 'Socket', 'cores' => 'Cores', 'threads' => 'Threads', 'base_clock' => 'Base clock', 'memory_type' => 'Memory support', 'tdp' => 'TDP (W)'],
    'gpu' => ['vram' => 'VRAM', 'length_mm' => 'Length (mm)', 'boost_clock' => 'Boost clock', 'tdp' => 'TDP (W)', 'recommended_psu_watts' => 'Recommended PSU (W)', 'ports' => 'Ports'],
    'mb' => ['socket' => 'Socket', 'chipset' => 'Chipset', 'memory_type' => 'Memory type', 'size' => 'Form factor', 'ram_slots' => 'RAM slots', 'nvme_slots' => 'NVMe slots', 'sata_ports' => 'SATA ports', 'wifi' => 'Wi-Fi'],
    'memory' => ['type' => 'Type', 'capacity' => 'Capacity', 'speed' => 'Speed', 'latency' => 'Latency', 'modules' => 'Kit configuration'],
    'storage' => ['type' => 'Type', 'interface' => 'Interface', 'capacity' => 'Capacity', 'read_speed' => 'Read speed', 'write_speed' => 'Write speed'],
    'psu' => ['wattage_watts' => 'Capacity (W)', 'rating' => 'Efficiency rating', 'modularity' => 'Modularity'],
    'case_box' => ['size' => 'Size', 'color' => 'Color', 'max_gpu_length' => 'GPU clearance (mm)', 'max_radiator_size' => 'Maximum radiator (mm)', 'max_cooler_height_mm' => 'Cooler clearance (mm)'],
    'cooling' => ['type' => 'Type', 'size' => 'Size', 'radiator_size_mm' => 'Radiator (mm)', 'height_mm' => 'Height (mm)', 'power_watts' => 'Power (W)'],
    'fans' => ['size' => 'Size', 'rgb' => 'RGB', 'power_watts' => 'Power (W)'],
    'monitor' => ['screen_size' => 'Screen size', 'panel_type' => 'Panel type', 'resolution' => 'Resolution', 'refresh_rate' => 'Refresh rate', 'response_time_hdr' => 'Response time / HDR'],
];
$fields = ['brand' => 'Brand', 'price' => 'Price', 'stock' => 'Stock quantity'] + $categoryFields[$category];
function comparisonImage(array $product, string $categoryLabel): void
{
    $stored = $product['image_url'] ?? '';
    $image = null;
    if (filter_var($stored, FILTER_VALIDATE_URL) && strtolower(parse_url($stored, PHP_URL_SCHEME) ?? '') === 'https') {
        $image = $stored;
    } elseif ($stored !== '' && is_file(__DIR__ . '/assets/images/' . basename($stored))) {
        $image = url('assets/images/' . rawurlencode(basename($stored)));
    }
    ?>
    <span class="compare-image">
        <span class="compare-image-fallback">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3"/><rect x="8" y="8" width="8" height="8" rx="1"/><path d="M8 1v3m8-3v3M8 20v3m8-3v3M1 8h3m-3 8h3m16-8h3m-3 8h3"/></svg>
            <span><?= e($categoryLabel) ?></span>
            <small>Photo not available</small>
        </span>
        <?php if ($image): ?><img src="<?= e($image) ?>" alt="<?= e($product['name']) ?>" loading="lazy" width="320" height="180" onerror="this.hidden = true;"><?php endif; ?>
    </span>
    <?php
}
$pageTitle = 'Compare Components';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content" tabindex="-1">
    <style>
        .compare-table { min-width: 580px; margin: 0; }
        #comparison-results { scroll-margin-top: 7rem; }
        .compare-table th, .compare-table td { padding: 1rem; vertical-align: top; min-width: 150px; max-width: 300px; overflow-wrap: anywhere; }
        .compare-table .compare-different > * { background: #f3f3f3; }
        .compare-picker { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; max-height: 65vh; overflow-y: auto; padding: 0.25rem; }
        .compare-choice { position: relative; display: block; cursor: pointer; margin: 0; }
        .compare-choice input { position: absolute; top: 0.85rem; left: 0.85rem; z-index: 1; width: 20px; height: 20px; accent-color: #171717; }
        .compare-choice-content { display: flex; flex-direction: column; height: 100%; border: 1px solid #dedede; border-radius: 0.85rem; overflow: hidden; background: #ffffff; }
        .compare-choice input:checked + .compare-choice-content { border-color: #171717; box-shadow: inset 0 0 0 1px #171717; }
        .compare-choice input:focus-visible + .compare-choice-content { outline: 2px solid #171717; outline-offset: 2px; }
        .compare-choice input:disabled + .compare-choice-content { opacity: 0.55; }
        .compare-image { display: grid; place-items: center; position: relative; height: 155px; background: #f5f5f5; }
        .compare-image img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: contain; padding: 1.25rem; background: inherit; }
        .compare-image-fallback { display: grid; justify-items: center; gap: 0.25rem; color: #707070; font-size: 0.8rem; font-weight: 400; }
        .compare-choice-info { padding: 1rem; display: flex; flex: 1; flex-direction: column; gap: 0.35rem; }
        .compare-choice-info strong { overflow-wrap: anywhere; }
        .compare-choice-price { margin-top: auto; padding-top: 0.4rem; font-weight: 700; }
        .compare-table .compare-image { height: 120px; border-radius: 0.65rem; margin-bottom: 0.75rem; }
        .compare-mobile-name { display: none; }
        @media (max-width: 767.98px) {
            .compare-picker { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            #comparison-results { overflow: visible; }
            .compare-table,
            .compare-table thead,
            .compare-table tbody,
            .compare-table tr,
            .compare-table caption { display: block; width: 100%; min-width: 0; }
            .compare-table th,
            .compare-table td { display: block; width: 100%; min-width: 0; max-width: none; padding: 0.75rem; }
            .compare-table thead tr { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .compare-table thead th:first-child { grid-column: 1 / -1; }
            .compare-table tbody th { font-weight: 700 !important; border-bottom: 0; }
            .compare-table tbody td { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 1rem; }
            .compare-mobile-name { display: block; color: #606060; font-size: 0.8rem; }
            .compare-value { text-align: right; }
            .compare-table .compare-image { height: 90px; }
            .compare-table .compare-image-fallback { font-size: 0.7rem; }
        }
        @media (max-width: 420px) { .compare-picker { grid-template-columns: minmax(0, 1fr); } }
    </style>
    <div class="container section-padding">
        <p class="small fw-semibold text-uppercase text-secondary">Look at the details</p>
        <h1>Compare components</h1>
        <p class="lead text-secondary">Compare up to three parts from the same category.</p>
        <?php if ($notice): ?><p class="alert alert-secondary" role="status"><?= e($notice) ?></p><?php endif; ?>
        <form method="get" action="<?= e(url('compare.php')) ?>" class="d-flex flex-wrap align-items-end gap-2 mb-4">
            <div>
                <label for="compare-category" class="form-label">Component category</label>
                <select id="compare-category" name="category" class="form-select">
                    <?php foreach ($categories as $key => $label): ?>
                        <option value="<?= e($key) ?>" <?= $key === $category ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-outline-dark" type="submit">Change category</button>
        </form>
        <?php if ($databaseError): ?>
            <p class="alert alert-secondary">The comparison could not be loaded. Please try again later.</p>
        <?php elseif (!$options): ?>
            <div class="border rounded-4 p-4"><h2 class="h5">No components yet</h2><p class="text-secondary mb-0">No active products are available in this category.</p></div>
        <?php else: ?>
            <form method="get" action="<?= e(url('compare.php#comparison-results')) ?>" class="border rounded-4 p-3 p-lg-4 mb-4" id="compare-picker-form">
                <input type="hidden" name="category" value="<?= e($category) ?>">
                <fieldset>
                    <legend class="h5">Pick the parts you want to compare</legend>
                    <p class="small text-secondary">Select two or three cards. Click a selected card again to remove it.</p>
                    <div class="compare-picker">
                        <?php foreach ($options as $option): ?>
                            <label class="compare-choice">
                                <input type="checkbox" name="ids[]" value="<?= (int) $option['id'] ?>" <?= in_array((int) $option['id'], $selectedIds, true) ? 'checked' : '' ?> aria-label="<?= e('Compare ' . $option['name']) ?>">
                                <span class="compare-choice-content">
                                    <?php comparisonImage($option, $categories[$category]); ?>
                                    <span class="compare-choice-info">
                                        <small class="text-secondary"><?= e($option['brand'] ?? '') ?></small>
                                        <strong><?= e($option['name']) ?></strong>
                                        <?php foreach (array_slice($categoryFields[$category], 0, 2, true) as $field => $label): ?>
                                            <small class="text-secondary"><?= e($label) ?>: <?= e((string) ($option[$field] ?? 'Not provided')) ?></small>
                                        <?php endforeach; ?>
                                        <span class="compare-choice-price"><?= e(money($option['price'])) ?></span>
                                    </span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
                <div class="d-flex flex-wrap gap-3 align-items-center mt-3">
                    <button class="btn btn-primary" type="submit">Compare selected parts</button>
                    <span class="small text-secondary" id="compare-selection-count" role="status"><?= count($selectedIds) ?> / 3 selected</span>
                    <a href="<?= e(url('compare.php?category=' . $category)) ?>">Clear selections</a>
                </div>
            </form>
            <?php if (count($products) < 2): ?>
                <p class="text-secondary" role="status">Choose at least two different components to see their specifications side by side.</p>
            <?php else: ?>
                <div class="table-responsive border rounded-4" id="comparison-results" tabindex="0" role="region" aria-label="Component comparison table">
                    <table class="table compare-table">
                        <caption class="px-3">Shaded rows have different values. Missing specifications are shown as “Not provided”; differences do not indicate which component is better.</caption>
                        <thead>
                            <tr>
                                <th scope="col">Specification</th>
                                <?php foreach ($products as $product): ?>
                                    <th scope="col">
                                        <?php comparisonImage($product, $categories[$category]); ?>
                                        <a href="<?= e(url('product.php?' . http_build_query(['category' => $category, 'id' => $product['id']]))) ?>"><?= e($product['name']) ?></a>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fields as $field => $label): ?>
                                <?php
                                $values = [];
                                foreach ($products as $product) {
                                    $value = $product[$field] ?? null;
                                    $values[] = $value === null || $value === '' ? 'Not provided' : ($field === 'price' ? money((string) $value) : (string) $value);
                                }
                                $different = count(array_unique($values)) > 1;
                                ?>
                                <tr class="<?= $different ? 'compare-different' : '' ?>">
                                    <th scope="row" class="fw-normal"><?= e($label) ?></th>
                                    <?php foreach ($values as $index => $value): ?>
                                        <td><span class="compare-mobile-name" aria-hidden="true"><?= e($products[$index]['name']) ?></span><span class="compare-value"><?= e($value) ?></span></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<script>
    (() => {
        const form = document.querySelector('#compare-picker-form');
        if (!form) return;
        const choices = [...form.querySelectorAll('input[type="checkbox"]')];
        const counter = form.querySelector('#compare-selection-count');
        const updateSelection = () => {
            const count = choices.filter((choice) => choice.checked).length;
            choices.forEach((choice) => { choice.disabled = count >= 3 && !choice.checked; });
            counter.textContent = `${count} / 3 selected`;
        };
        form.addEventListener('change', updateSelection);
        form.addEventListener('submit', (event) => {
            if (choices.filter((choice) => choice.checked).length < 2) {
                event.preventDefault();
                counter.textContent = 'Choose at least two components to compare.';
            }
        });
        updateSelection();
    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
