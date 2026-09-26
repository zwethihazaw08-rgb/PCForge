<?php

require_once __DIR__ . '/includes/functions.php';

$categories = [
    'cpu' => 'Processors',
    'gpu' => 'Graphics Cards',
    'mb' => 'Motherboards',
    'memory' => 'Memory',
    'storage' => 'Storage',
    'psu' => 'Power Supplies',
    'case_box' => 'Cases',
    'cooling' => 'CPU Cooling',
    'fans' => 'Case Fans',
    'monitor' => 'Monitors',
];

$category = (string) ($_GET['category'] ?? 'cpu');
$category = array_key_exists($category, $categories) ? $category : 'cpu';
$search = trim((string) ($_GET['q'] ?? ''));
$brand = trim((string) ($_GET['brand'] ?? ''));
$minimumPrice = is_numeric($_GET['min_price'] ?? null) ? max(0, (float) $_GET['min_price']) : null;
$maximumPrice = is_numeric($_GET['max_price'] ?? null) ? max(0, (float) $_GET['max_price']) : null;
$products = [];
$brands = [];
$catalogError = false;

try {
    $connection = db();

    // The table name is selected only from the fixed whitelist above.
    $brandQuery = $connection->query("SELECT DISTINCT brand FROM `$category` WHERE status = 'active' AND brand IS NOT NULL AND brand <> '' ORDER BY brand");
    $brands = $brandQuery->fetchAll(PDO::FETCH_COLUMN);

    $conditions = ["status = 'active'"];
    $parameters = [];

    if ($search !== '') {
        $conditions[] = '(name LIKE :search_name OR brand LIKE :search_brand)';
        $parameters['search_name'] = '%' . $search . '%';
        $parameters['search_brand'] = '%' . $search . '%';
    }
    if ($brand !== '' && in_array($brand, $brands, true)) {
        $conditions[] = 'brand = :brand';
        $parameters['brand'] = $brand;
    }
    if ($minimumPrice !== null) {
        $conditions[] = 'price >= :minimum_price';
        $parameters['minimum_price'] = $minimumPrice;
    }
    if ($maximumPrice !== null) {
        $conditions[] = 'price <= :maximum_price';
        $parameters['maximum_price'] = $maximumPrice;
    }

    $statement = $connection->prepare(
        "SELECT * FROM `$category` WHERE " . implode(' AND ', $conditions) . ' ORDER BY name LIMIT 100'
    );
    $statement->execute($parameters);
    $products = $statement->fetchAll();
} catch (PDOException $exception) {
    error_log('PCForge product catalog query failed: ' . $exception->getMessage());
    $catalogError = true;
}

$pageTitle = $categories[$category];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content" tabindex="-1">
    <section class="section-padding pb-3">
        <div class="container">
            <p class="small fw-semibold text-uppercase text-secondary mb-2">PCForge catalog</p>
            <h1 id="catalog-title"><?= e($categories[$category]) ?></h1>
            <p class="lead text-secondary">Browse active components and find the parts for your build.</p>
        </div>
    </section>

    <section class="pb-5">
        <div class="container">
            <form id="catalog-filters" class="border rounded-4 p-3 p-lg-4 bg-light" method="get" action="<?= e(url('products.php')) ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3">
                        <label class="form-label" for="category">Category</label>
                        <select class="form-select" id="category" name="category">
                            <?php foreach ($categories as $value => $label): ?>
                                <option value="<?= e($value) ?>" <?= $category === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label" for="q">Search</label>
                        <input class="form-control" id="q" name="q" value="<?= e($search) ?>" placeholder="Name or brand">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label" for="brand">Brand</label>
                        <select class="form-select" id="brand" name="brand">
                            <option value="">All brands</option>
                            <?php foreach ($brands as $availableBrand): ?>
                                <option value="<?= e($availableBrand) ?>" <?= $brand === $availableBrand ? 'selected' : '' ?>><?= e($availableBrand) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 col-lg-1">
                        <label class="form-label" for="min_price">Min $</label>
                        <input class="form-control" type="number" min="0" step="0.01" id="min_price" name="min_price" value="<?= $minimumPrice !== null ? e((string) $minimumPrice) : '' ?>">
                    </div>
                    <div class="col-6 col-lg-1">
                        <label class="form-label" for="max_price">Max $</label>
                        <input class="form-control" type="number" min="0" step="0.01" id="max_price" name="max_price" value="<?= $maximumPrice !== null ? e((string) $maximumPrice) : '' ?>">
                    </div>
                    <div class="col-lg-2 d-grid">
                        <button class="btn btn-primary" type="submit">Filter parts</button>
                    </div>
                </div>
            </form>
            <p id="catalog-status" class="small text-secondary mt-2 mb-0" role="status" style="min-height: 1.5em;"></p>
        </div>
    </section>

    <section id="catalog-results" class="pb-5" aria-labelledby="results-heading">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
                <h2 id="results-heading" class="h4 mb-0">Available <?= e(strtolower($categories[$category])) ?></h2>
                <?php if ($search !== '' || $brand !== '' || $minimumPrice !== null || $maximumPrice !== null): ?>
                    <a class="small text-secondary" data-clear-filters href="<?= e(url('products.php?category=' . $category)) ?>">Clear filters</a>
                <?php endif; ?>
            </div>

            <?php if ($catalogError): ?>
                <div class="alert alert-secondary" role="status">The catalog is unavailable until the database is connected.</div>
            <?php elseif (!$products): ?>
                <div class="border rounded-4 p-5 text-center">
                    <h3 class="h5">No matching parts</h3>
                    <p class="text-secondary mb-0">Try another category or remove one of the filters.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <?php
                        $productUrl = url('product.php?' . http_build_query(['category' => $category, 'id' => $product['id']]));
                        $imageName = basename((string) ($product['image_url'] ?? ''));
                        $imagePath = __DIR__ . '/assets/images/' . $imageName;
                        $imageUrl = $imageName !== '' && is_file($imagePath) ? url('assets/images/' . rawurlencode($imageName)) : null;
                        ?>
                        <div class="col-sm-6 col-xl-3">
                            <article class="product-card d-flex flex-column">
                                <?php if ($imageUrl): ?>
                                    <img class="product-image" src="<?= e($imageUrl) ?>" alt="<?= e($product['name']) ?>" loading="lazy" width="400" height="300">
                                <?php else: ?>
                                    <div class="product-image d-flex align-items-center justify-content-center text-secondary small">Image unavailable</div>
                                <?php endif; ?>
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <p class="small text-secondary mb-2"><?= e($product['brand'] ?? 'Brand not listed') ?></p>
                                    <h3 class="h6"><a class="text-decoration-none" href="<?= e($productUrl) ?>"><?= e($product['name']) ?></a></h3>
                                    <p class="product-price mt-auto pt-3 mb-1"><?= e(money($product['price'] ?? null)) ?></p>
                                    <p class="small text-secondary mb-3">
                                        <?= $product['stock'] === null ? 'Availability unconfirmed' : ((int) $product['stock'] > 0 ? 'In stock' : 'Out of stock') ?>
                                    </p>
                                    <a class="btn btn-outline-dark" href="<?= e($productUrl) ?>">View details</a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
    (() => {
        const form = document.querySelector('#catalog-filters');
        const results = document.querySelector('#catalog-results');
        const status = document.querySelector('#catalog-status');
        const button = form.querySelector('button[type="submit"]');
        let request;

        async function filterParts(target, updateHistory = true) {
            request?.abort();
            const controller = new AbortController();
            request = controller;
            results.setAttribute('aria-busy', 'true');
            button.disabled = true;
            status.textContent = 'Loading parts…';

            try {
                const response = await fetch(target, {signal: controller.signal});
                if (!response.ok) throw new Error('Catalog request failed');
                const page = new DOMParser().parseFromString(await response.text(), 'text/html');
                const nextResults = page.querySelector('#catalog-results');
                const nextForm = page.querySelector('#catalog-filters');
                if (!nextResults || !nextForm) throw new Error('Catalog content missing');
                if (controller.signal.aborted) return;

                // Keep the current page and scroll position while refreshing the catalogue.
                const scrollY = window.scrollY;
                results.replaceChildren(...nextResults.childNodes);
                form.querySelector('#brand').innerHTML = nextForm.querySelector('#brand').innerHTML;
                for (const name of ['category', 'q', 'brand', 'min_price', 'max_price']) {
                    form.elements.namedItem(name).value = nextForm.elements.namedItem(name).value;
                }
                document.querySelector('#catalog-title').textContent = page.querySelector('#catalog-title').textContent;
                document.title = page.title;
                if (updateHistory && target.href !== window.location.href) {
                    window.history.pushState(null, '', target);
                }
                window.scrollTo({top: scrollY, behavior: 'instant'});
                status.textContent = 'Filters applied. ' + results.querySelector('#results-heading').textContent + ' updated.';
            } catch (error) {
                if (error.name !== 'AbortError') {
                    status.textContent = 'Could not update parts. Please try again.';
                }
            } finally {
                if (request === controller) {
                    results.removeAttribute('aria-busy');
                    button.disabled = false;
                }
            }
        }

        form.addEventListener('submit', event => {
            event.preventDefault();
            const target = new URL(form.getAttribute('action'), window.location.href);
            target.search = new URLSearchParams(new FormData(form)).toString();
            filterParts(target);
        });
        results.addEventListener('click', event => {
            const link = event.target.closest('[data-clear-filters]');
            if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            filterParts(new URL(link.href));
        });
        window.addEventListener('popstate', () => filterParts(new URL(window.location.href), false));
    })();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
