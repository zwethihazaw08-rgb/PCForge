<?php

require_once __DIR__ . '/functions.php';

$categories = [
    'cpu' => 'Processor', 'mb' => 'Motherboard', 'memory' => 'Memory',
    'gpu' => 'Graphics card', 'storage' => 'Storage', 'cooling' => 'Cooling',
    'psu' => 'Power supply', 'case_box' => 'Case',
];

// Saved JSON uses the same category => component ID format as the builder.
function savedBuildIds($data): array
{
    global $categories;
    if (!is_array($data) || array_diff_key($data, $categories)) {
        throw new InvalidArgumentException('This build could not be read. Please save a new copy from the builder.');
    }
    $ids = [];
    foreach ($data as $category => $value) {
        $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) throw new InvalidArgumentException('This build contains an invalid component.');
        $ids[$category] = $id;
    }
    return $ids;
}

function savedBuildName($value): string
{
    $name = is_string($value) ? trim($value) : '';
    if ($name === '' || mb_strlen($name) > 100) {
        throw new InvalidArgumentException('Give your build a name between 1 and 100 characters.');
    }
    return $name;
}

function savedBuildCreate(PDO $connection, int $userId, string $name, array $ids): int
{
    $ids = savedBuildIds($ids);
    if (!$ids) throw new InvalidArgumentException('Choose at least one component before saving.');
    $query = $connection->prepare('INSERT INTO saved_builds (user_id, build_name, build_data, share_token) VALUES (:user, :name, :data, :token)');
    $query->execute([
        'user' => $userId, 'name' => savedBuildName($name),
        'data' => json_encode($ids, JSON_THROW_ON_ERROR), 'token' => bin2hex(random_bytes(32)),
    ]);
    return (int) $connection->lastInsertId();
}

function savedBuildPreview(array $ids): array
{
    global $categories;
    static $products = [];
    $preview = ['parts' => [], 'cents' => 0, 'unavailable' => 0, 'missing_prices' => 0];
    foreach ($categories as $category => $label) {
        if (!isset($ids[$category])) continue;
        $key = $category . ':' . $ids[$category];
        if (!array_key_exists($key, $products)) {
            // Table names come only from the fixed category list above.
            $query = db()->prepare("SELECT id, name, price, image_url FROM `$category` WHERE id = :id AND status = 'active'");
            $query->execute(['id' => $ids[$category]]);
            $products[$key] = $query->fetch() ?: null;
        }
        $product = $products[$key];
        $preview['parts'][$category] = $product;
        if (!$product) {
            $preview['unavailable']++;
        } elseif (preg_match('/^(\d+)\.(\d{2})$/', (string) $product['price'], $amount)) {
            $preview['cents'] += (int) $amount[1] * 100 + (int) $amount[2];
        } else {
            $preview['missing_prices']++;
        }
    }
    return $preview;
}

function savedBuildPrice(int $cents): string
{
    return money(intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT));
}

function savedBuildImages(array $parts): void
{
    global $categories;
    ?>
    <div class="saved-build-images">
        <?php foreach (['cpu', 'gpu', 'memory', 'storage'] as $category): ?>
            <?php
            $product = $parts[$category] ?? null;
            $stored = (string) ($product['image_url'] ?? '');
            $image = null;
            if (filter_var($stored, FILTER_VALIDATE_URL) && strtolower(parse_url($stored, PHP_URL_SCHEME) ?? '') === 'https') {
                $image = $stored;
            } elseif ($stored !== '' && is_file(__DIR__ . '/../assets/images/' . basename($stored))) {
                $image = url('assets/images/' . rawurlencode(basename($stored)));
            }
            ?>
            <div class="saved-build-image">
                <div class="saved-build-image-box">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8 8h8v8H8zM1 9h3m16 0h3M1 15h3m16 0h3M9 1v3m6-3v3M9 20v3m6-3v3"/></svg>
                    <?php if ($image): ?><img src="<?= e($image) ?>" alt="<?= e($product['name']) ?>" width="160" height="120" loading="lazy" onerror="this.hidden = true;"><?php endif; ?>
                </div>
                <span><?= e($categories[$category]) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

