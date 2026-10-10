<?php
// Additive, repeatable catalog upgrade. Never resets existing products or inventory.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/catalog.php';

function web_catalog_manifest(): array
{
    $manifest = json_decode(file_get_contents(__DIR__ . '/web_catalog.json'), true, 512, JSON_THROW_ON_ERROR);
    $counts = array_fill_keys(array_keys(component_categories()), 0);
    $identities = [];
    foreach ($manifest['records'] as $record) {
        $category = component_type($record['category']);
        $product = $record['product'];
        $identity = $category . '/' . $product['id'];
        if (isset($identities[$identity]) || $product['id'] < 1001 || $product['id'] > 1020
            || $record['source_id'] !== 2026100900 + $product['id'] - 1000
            || $product['status'] !== 'active' || $product['name'] === '' || $product['description'] === ''
            || $product['stock'] !== null || price_cents($product['price']) <= 0) {
            throw new RuntimeException('Invalid product in web catalog: ' . $identity);
        }
        $identities[$identity] = true;
        $counts[$category]++;
        $image = $product['image_url'];
        $path = __DIR__ . '/../assets/images/' . $image;
        if ($image !== basename($image) || !is_file($path) || !getimagesize($path)
            || hash_file('sha256', $path) !== $record['source']['image_sha256']) {
            throw new RuntimeException('Missing or changed product image: ' . $image);
        }
        foreach (['product_url', 'image_url', 'exchange_rate_source'] as $field) {
            if (!filter_var($record['source'][$field], FILTER_VALIDATE_URL)
                || parse_url($record['source'][$field], PHP_URL_SCHEME) !== 'https') {
                throw new RuntimeException('Invalid source URL: ' . $identity);
            }
        }
        foreach ($record['support'] as $kind => $values) {
            if (!(($category === 'case_box' && $kind === 'form_factors') || ($category === 'cooling' && $kind === 'sockets'))
                || !is_array($values) || count($values) !== count(array_unique($values))) {
                throw new RuntimeException('Invalid compatibility support: ' . $identity);
            }
        }
    }
    foreach ($counts as $category => $count) {
        if ($count !== 20) throw new RuntimeException('Expected 20 additions for ' . $category);
    }
    return $manifest;
}

function import_web_catalog(PDO $pdo, array $manifest, bool $apply = false, bool $refreshImages = false): array
{
    if ($pdo->inTransaction()) throw new RuntimeException('Import needs its own transaction.');
    $lock = 'pcforge_web_catalog_' . md5((string) $pdo->query('SELECT DATABASE()')->fetchColumn());
    $statement = $pdo->prepare('SELECT GET_LOCK(?, 10)');
    $statement->execute([$lock]);
    if ((int) $statement->fetchColumn() !== 1) throw new RuntimeException('Another catalog import is running.');
    $inserted = $skipped = $imagesUpdated = 0;
    try {
        $pdo->beginTransaction();
        $columns = [];
        foreach (component_categories() as $category => $label) {
            $columns[$category] = $pdo->query("SHOW COLUMNS FROM `$category`")->fetchAll(PDO::FETCH_COLUMN);
        }
        foreach ($manifest['records'] as $record) {
            $category = component_type($record['category']);
            $product = $record['product'];
            if (array_diff(array_keys($product), $columns[$category])) throw new RuntimeException('Unknown catalog column.');
            $statement = $pdo->prepare('SELECT product_id, source_file, source_values FROM product_data_sources WHERE category = ? AND source_id = ?');
            $statement->execute([$category, $record['source_id']]);
            $source = $statement->fetch();
            if ($source) {
                $original = json_decode($source['source_values'], true, 512, JSON_THROW_ON_ERROR);
                $statement = $pdo->prepare("SELECT id, image_url FROM `$category` WHERE id = ? FOR UPDATE");
                $statement->execute([$source['product_id']]);
                $existing = $statement->fetch();
                if ((int) $source['product_id'] !== $product['id'] || $source['source_file'] !== 'Database/web_catalog.json'
                    || ($original['product_url'] ?? null) !== $record['source']['product_url'] || !$existing) {
                    throw new RuntimeException('Conflicting source mapping: ' . $category . '/' . $record['source_id']);
                }
                $processing = $record['source']['image_processing'] ?? [];
                // Only upgrade the exact original batch photo; keep administrator replacements.
                if ($refreshImages && isset($processing['original_file'], $processing['source_sha256'])
                    && $existing['image_url'] === $processing['original_file']
                    && ($original['image_sha256'] ?? null) === $processing['source_sha256']) {
                    if ($apply) {
                        $pdo->prepare("UPDATE `$category` SET image_url = ? WHERE id = ?")
                            ->execute([$product['image_url'], $product['id']]);
                        $original['image_sha256'] = $record['source']['image_sha256'];
                        $original['image_processing'] = $processing;
                        $pdo->prepare('UPDATE product_data_sources SET source_values = ? WHERE category = ? AND source_id = ?')
                            ->execute([json_encode($original, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                                $category, $record['source_id']]);
                    }
                    $imagesUpdated++;
                }
                $skipped++;
                continue; // Preserve every later admin edit, including name, price, stock, and status.
            }
            $statement = $pdo->prepare("SELECT id FROM `$category` WHERE id = ? OR name = ? LIMIT 1");
            $statement->execute([$product['id'], $product['name']]);
            if ($statement->fetchColumn()) throw new RuntimeException('Existing ID or product name conflicts with ' . $category . '/' . $product['id']);
            if ($apply) {
                $fields = array_keys($product);
                $sql = "INSERT INTO `$category` (`" . implode('`,`', $fields) . '`) VALUES (' . implode(',', array_fill(0, count($fields), '?')) . ')';
                $pdo->prepare($sql)->execute(array_values($product));
                foreach ($record['support']['form_factors'] ?? [] as $value) {
                    $pdo->prepare('INSERT INTO case_motherboard_support (case_id, form_factor) VALUES (?, ?)')->execute([$product['id'], $value]);
                }
                foreach ($record['support']['sockets'] ?? [] as $value) {
                    $pdo->prepare('INSERT INTO cooling_socket_support (cooling_id, socket) VALUES (?, ?)')->execute([$product['id'], $value]);
                }
                $pdo->prepare('INSERT INTO product_data_sources (category, source_id, product_id, source_file, source_row, source_values) VALUES (?, ?, ?, ?, ?, ?)')
                    ->execute([$category, $record['source_id'], $product['id'], 'Database/web_catalog.json', $product['id'] - 1000,
                        json_encode($record['source'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)]);
            }
            $inserted++;
        }
        if ($apply) $pdo->commit(); else $pdo->rollBack();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $exception;
    } finally {
        $pdo->prepare('SELECT RELEASE_LOCK(?)')->execute([$lock]);
    }
    $result = ['added' => $inserted, 'skipped' => $skipped];
    if ($refreshImages) $result['images_updated'] = $imagesUpdated;
    return $result;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        $apply = in_array('--apply', $argv, true);
        $refreshImages = in_array('--refresh-images', $argv, true);
        $result = import_web_catalog(db(), web_catalog_manifest(), $apply, $refreshImages);
        echo ($apply ? 'Applied' : 'Dry run') . ': ' . $result['added'] . ' additions, ' . $result['skipped'] . " already imported.\n";
        if ($refreshImages) echo $result['images_updated'] . " original photos upgraded to transparent images.\n";
        if (!$apply) echo "Run with --apply to add the products.\n";
    } catch (Throwable $exception) {
        fwrite(STDERR, $exception->getMessage() . "\n");
        exit(1);
    }
}
