<?php

require_once __DIR__ . '/includes/functions.php';

$categories = [
    'cpu' => 'Processor',
    'gpu' => 'Graphics Card',
    'mb' => 'Motherboard',
    'memory' => 'Memory',
    'storage' => 'Storage',
    'psu' => 'Power Supply',
    'case_box' => 'Case',
    'cooling' => 'CPU Cooling',
    'fans' => 'Case Fan',
    'monitor' => 'Monitor',
];

$category = (string) ($_GET['category'] ?? '');
$productId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$product = null;
$productError = null;

if (!array_key_exists($category, $categories) || $productId === false) {
    http_response_code(404);
    $productError = 'That component could not be found.';
} else {
    try {
        $statement = db()->prepare("SELECT * FROM `$category` WHERE id = :id AND status = 'active' LIMIT 1");
        $statement->execute(['id' => $productId]);
        $product = $statement->fetch();

        if (!$product) {
            http_response_code(404);
            $productError = 'That component could not be found.';
        }
    } catch (PDOException $exception) {
        error_log('PCForge product detail query failed: ' . $exception->getMessage());
        http_response_code(500);
        $productError = 'The component is temporarily unavailable.';
    }
}

$pageTitle = $product['name'] ?? 'Component details';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content" tabindex="-1">
    <div class="container product-detail-page">
        <?php if ($productError): ?>
        <div class="border rounded-4 p-5 text-center">
            <p class="small fw-semibold text-uppercase text-secondary">Component details</p>
            <h1 class="h3"><?= e($productError) ?></h1>
            <a class="btn btn-primary mt-3" href="<?= e(url('products.php')) ?>">Back to components</a>
        </div>
        <?php else: ?>
        <?php
            $imageName = basename((string) ($product['image_url'] ?? ''));
            $imagePath = __DIR__ . '/assets/images/' . $imageName;
            $imageUrl = $imageName !== '' && is_file($imagePath) ? url('assets/images/' . rawurlencode($imageName)) : null;
            $specifications = [];

            $specificationLabels = [
                'cpu' => ['socket' => 'Socket', 'memory_type' => 'Memory support', 'cores' => 'Cores', 'threads' => 'Threads', 'tdp' => 'TDP'],
                'gpu' => ['vram' => 'VRAM', 'length_mm' => 'Length', 'tdp' => 'Power draw', 'recommended_psu_watts' => 'Recommended PSU', 'ports' => 'Ports'],
                'mb' => ['socket' => 'Socket', 'memory_type' => 'Memory type', 'chipset' => 'Chipset', 'size' => 'Form factor', 'ram_slots' => 'RAM slots', 'nvme_slots' => 'M.2 slots'],
                'memory' => ['type' => 'Memory type', 'capacity' => 'Capacity', 'speed' => 'Speed', 'latency' => 'Latency', 'module_count' => 'Modules'],
                'storage' => ['interface' => 'Interface', 'capacity' => 'Capacity', 'read_speed' => 'Read speed', 'write_speed' => 'Write speed'],
                'psu' => ['wattage_watts' => 'Wattage', 'rating' => 'Efficiency rating', 'modularity' => 'Modularity'],
                'case_box' => ['size' => 'Case size', 'max_gpu_length' => 'GPU clearance', 'max_radiator_size' => 'Radiator support', 'max_cooler_height_mm' => 'Cooler clearance'],
                'cooling' => ['type' => 'Type', 'size' => 'Size', 'radiator_size_mm' => 'Radiator size', 'height_mm' => 'Cooler height'],
                'fans' => ['size' => 'Size', 'rgb' => 'RGB', 'power_watts' => 'Power draw'],
                'monitor' => ['screen_size' => 'Screen size', 'panel_type' => 'Panel type', 'resolution' => 'Resolution', 'refresh_rate' => 'Refresh rate', 'response_time_hdr' => 'Response time / HDR'],
            ][$category];

            foreach ($specificationLabels as $field => $label) {
                $value = $product[$field] ?? null;
                if ($value !== null && $value !== '') {
                    $suffix = in_array($field, ['length_mm', 'max_gpu_length', 'max_radiator_size', 'max_cooler_height_mm', 'radiator_size_mm', 'height_mm'], true) ? ' mm' : '';
                    if (in_array($field, ['tdp', 'recommended_psu_watts', 'wattage_watts', 'power_watts'], true)) {
                        $suffix = ' W';
                    }
                    $specifications[$label] = (string) $value . $suffix;
                }
            }
            ?>
        <nav aria-label="Breadcrumb" class="mb-4">
            <a class="text-secondary text-decoration-none"
                href="<?= e(url('products.php?category=' . $category)) ?>">&larr; <?= e($categories[$category]) ?></a>
        </nav>
        <div class="row product-detail-hero g-4 align-items-start">
            <div class="col-lg-5">
                <div class="product-detail-media border rounded-4 overflow-hidden bg-light">
                    <?php if ($imageUrl): ?>
                    <img class="product-detail-image" src="<?= e($imageUrl) ?>" alt="<?= e($product['name']) ?>"
                        width="700" height="525">
                    <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center text-secondary"
                        style="aspect-ratio: 4 / 3;">Image unavailable</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-7 product-detail-info">
                <p class="small fw-semibold text-uppercase text-secondary mb-2"><?= e($categories[$category]) ?></p>
                <h1><?= e($product['name']) ?></h1>
                <?php if (!empty($product['brand'])): ?><p class="text-secondary mb-3"><?= e($product['brand']) ?></p>
                <?php endif; ?>
                <p class="product-price mb-2"><?= e(money($product['price'] ?? null)) ?></p>
                <p class="text-secondary">
                    <?php if ($product['stock'] === null): ?>Availability is unconfirmed.
                    <?php elseif ((int) $product['stock'] > 0): ?>In stock.
                    <?php else: ?>Currently out of stock.
                    <?php endif; ?>
                </p>
                <div class="d-flex flex-wrap gap-2 my-3">
                    <?php if ($category !== 'monitor'): ?><a class="btn btn-primary"
                        href="<?= e(url('builder.php?category=' . $category . '&id=' . $product['id'])) ?>">Add to
                        Build</a><?php endif; ?>
                    <a class="btn btn-outline-dark"
                        href="<?= e(url('cart.php?category=' . $category . '&id=' . $product['id'])) ?>">Add to Cart</a>
                    <a class="btn btn-outline-dark"
                        href="<?= e(url('compare.php?category=' . $category . '&id=' . $product['id'])) ?>">Compare</a>
                </div>
                <?php if ($category !== 'monitor'): ?><p class="small text-secondary mb-0">Compatibility is checked
                    again by the builder before a build is finalized.</p><?php endif; ?>
            </div>
        </div>
        <section class="product-detail-specs mt-4 pt-4 border-top" aria-labelledby="specifications-heading">
            <h2 id="specifications-heading" class="h4 mb-3">Specifications</h2>
            <?php if (!$specifications): ?>
            <p class="text-secondary">No specifications have been added for this component yet.</p>
            <?php else: ?>
            <dl class="row g-0 border rounded-4 overflow-hidden">
                <?php foreach ($specifications as $label => $value): ?>
                <div class="col-md-6 d-flex justify-content-between gap-3 p-3 border-bottom">
                    <dt class="mb-0 text-secondary fw-normal"><?= e($label) ?></dt>
                    <dd class="mb-0 text-end fw-semibold"><?= e($value) ?></dd>
                </div>
                <?php endforeach; ?>
            </dl>
            <?php endif; ?>
        </section>
        <?php if (!empty($product['description'])): ?>
        <section class="mt-4" aria-labelledby="product-details-heading">
            <h2 id="product-details-heading" class="h4 mb-3">Product details</h2>
            <p class="text-secondary"><?= nl2br(e($product['description'])) ?></p>
        </section>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>