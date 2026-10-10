<?php
// Validate the files shipped to new users without changing the configured database.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../Database/import_web_catalog.php';
$webCatalog = web_catalog_manifest();

$pdo = db();
$originalDatabase = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
$originalSqlMode = (string) $pdo->query('SELECT @@SESSION.sql_mode')->fetchColumn();
$temporaryDatabase = 'pcforge_catalog_test_' . bin2hex(random_bytes(8));
$created = false;
$importedCatalog = [];

try {
    // No IF NOT EXISTS: a collision must fail before this test can use or drop it.
    $pdo->exec("CREATE DATABASE `$temporaryDatabase` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $created = true;
    $pdo->exec("USE `$temporaryDatabase`");
    foreach (['schema.sql', 'catalog.sql'] as $file) {
        $sql = file_get_contents(__DIR__ . '/../Database/' . $file);
        if ($sql === false) throw new RuntimeException('Cannot read ' . $file);
        $pdo->exec($sql);
    }

    $categories = ['cpu', 'gpu', 'mb', 'memory', 'storage', 'psu', 'case_box', 'cooling', 'fans', 'monitor'];
    $images = [];
    foreach ($categories as $table) {
        $rows = $pdo->query("SELECT * FROM `$table` ORDER BY id")->fetchAll();
        $expectedCount = $table === 'fans' ? 20 : 35;
        if (count($rows) !== $expectedCount) throw new RuntimeException('Wrong product count: ' . $table);
        foreach ($rows as $row) {
            if ($row['status'] !== 'active' || $row['name'] === '' || $row['description'] === null) {
                throw new RuntimeException('Incomplete published product: ' . $table . '/' . $row['id']);
            }
            $filename = (string) $row['image_url'];
            $imagePath = __DIR__ . '/../assets/images/' . $filename;
            if ($filename !== basename($filename) || !is_file($imagePath) || filesize($imagePath) === 0) {
                throw new RuntimeException('Missing catalog image: ' . $filename);
            }
            $images[$filename] = true;
        }
        $importedCatalog[$table] = $rows;
    }
    if (count($images) !== 335) throw new RuntimeException('Expected 335 distinct product image files.');

    foreach ($webCatalog['records'] as $record) {
        $table = $record['category'];
        $statement = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
        $statement->execute([$record['product']['id']]);
        $product = $statement->fetch();
        foreach ($record['product'] as $field => $value) {
            if (($value === null && $product[$field] !== null) || ($value !== null && (string) $product[$field] !== (string) $value)) {
                throw new RuntimeException("Catalog/manifest mismatch: $table/$field");
            }
        }
    }

    foreach (['case_motherboard_support' => ['case_id', 'form_factor', 'case_box'],
              'cooling_socket_support' => ['cooling_id', 'socket', 'cooling']] as $table => [$key, $value, $productTable]) {
        $rows = $pdo->query("SELECT * FROM `$table` ORDER BY `$key`, `$value`")->fetchAll();
        $supportKey = $productTable === 'case_box' ? 'form_factors' : 'sockets';
        $newSupport = count(array_filter($webCatalog['records'], fn($record) => $record['category'] === $productTable && !empty($record['support'][$supportKey])));
        if (count(array_unique(array_column($rows, $key))) !== 15 + $newSupport) {
            throw new RuntimeException('Missing supplied compatibility support: ' . $table);
        }
        $orphans = (int) $pdo->query("SELECT COUNT(*) FROM `$table` s LEFT JOIN `$productTable` p ON p.id = s.`$key` WHERE p.id IS NULL")->fetchColumn();
        if ($orphans !== 0) throw new RuntimeException('Orphaned compatibility support: ' . $table);
        $importedCatalog[$table] = $rows;
    }

    if ((int) $pdo->query('SELECT COUNT(*) FROM product_data_sources')->fetchColumn() !== 200) {
        throw new RuntimeException('Expected 200 public web source records.');
    }
    $rerun = import_web_catalog($pdo, $webCatalog, true);
    if ($rerun !== ['added' => 0, 'skipped' => 200]) throw new RuntimeException('Fresh catalog is not recognized by upgrade importer.');

    foreach (['users', 'user_shipping_details', 'orders', 'order_items', 'saved_builds'] as $table) {
        if ((int) $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn() !== 0) {
            throw new RuntimeException('Unexpected records in fresh installation: ' . $table);
        }
    }
    $settings = $pdo->query('SELECT * FROM store_settings')->fetchAll();
    if (count($settings) !== 1 || $settings[0]['currency'] !== 'USD' || $settings[0]['store_email'] !== '' || (int) $settings[0]['maintenance_mode'] !== 0) {
        throw new RuntimeException('Unexpected default store settings.');
    }
} finally {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $pdo->exec('SET SESSION sql_mode = ' . $pdo->quote($originalSqlMode));
    $pdo->exec('USE `' . str_replace('`', '``', $originalDatabase) . '`');
    if ($created) $pdo->exec("DROP DATABASE `$temporaryDatabase`");
}

echo "PASS fresh schema and catalog: 335 products, 335 image files, source records, compatibility support, repeatable import, empty private tables; temporary database removed.\n";