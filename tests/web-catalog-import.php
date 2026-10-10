<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../Database/import_web_catalog.php';
$pdo = db();
$original = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
$temporary = 'pcforge_web_import_test_' . bin2hex(random_bytes(8));
$created = false;
$manifest = web_catalog_manifest();
try {
    $pdo->exec("CREATE DATABASE `$temporary` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $created = true;
    $pdo->exec("USE `$temporary`");
    $pdo->exec(file_get_contents(__DIR__ . '/../Database/schema.sql'));
    $pdo->exec("INSERT INTO cpu (id, name, price, stock, status) VALUES (1, 'Existing store product', 123.45, 7, 'inactive')");
    $before = $pdo->query('SELECT * FROM cpu WHERE id = 1')->fetch();
    if (import_web_catalog($pdo, $manifest) !== ['added'=>200, 'skipped'=>0]
        || (int) $pdo->query('SELECT COUNT(*) FROM cpu')->fetchColumn() !== 1) throw new RuntimeException('Dry run changed products.');

    // Force a late conflict: earlier inserts must all roll back.
    $pdo->exec("INSERT INTO cooling (id, name) VALUES (1020, 'Existing conflicting ID')");
    $failed = false;
    try { import_web_catalog($pdo, $manifest, true); } catch (RuntimeException $e) { $failed = str_contains($e->getMessage(), 'conflicts'); }
    if (!$failed || (int) $pdo->query('SELECT COUNT(*) FROM product_data_sources')->fetchColumn() !== 0
        || (int) $pdo->query('SELECT COUNT(*) FROM cpu')->fetchColumn() !== 1) throw new RuntimeException('Conflict did not roll back the batch.');
    $pdo->exec('DELETE FROM cooling WHERE id = 1020');
    if (import_web_catalog($pdo, $manifest, true) !== ['added'=>200, 'skipped'=>0]) throw new RuntimeException('Wrong import count.');
    if ($pdo->query('SELECT * FROM cpu WHERE id = 1')->fetch() !== $before) throw new RuntimeException('Existing product changed.');
    $pdo->exec("UPDATE cpu SET name = 'Admin edited name', price = 222.22, stock = 3, status = 'inactive' WHERE id = 1001");
    $edited = $pdo->query('SELECT * FROM cpu WHERE id = 1001')->fetch();
    if (import_web_catalog($pdo, $manifest, true) !== ['added'=>0, 'skipped'=>200]
        || $pdo->query('SELECT * FROM cpu WHERE id = 1001')->fetch() !== $edited) throw new RuntimeException('Repeat import overwrote an admin edit.');
    // Simulate an installation that imported the batch before background removal.
    foreach (array_slice($manifest['records'], 0, 3) as $record) {
        $source = $record['source'];
        $source['image_sha256'] = $source['image_processing']['source_sha256'];
        unset($source['image_processing']);
        $pdo->prepare('UPDATE cpu SET image_url = ? WHERE id = ?')
            ->execute([$record['source']['image_processing']['original_file'], $record['product']['id']]);
        $pdo->prepare("UPDATE product_data_sources SET source_values = ? WHERE category = 'cpu' AND source_id = ?")
            ->execute([json_encode($source, JSON_THROW_ON_ERROR), $record['source_id']]);
    }
    $pdo->exec("UPDATE cpu SET image_url = 'admin-replacement.png' WHERE id = 1002");
    $beforeRefresh = $pdo->query('SELECT * FROM cpu WHERE id = 1001')->fetch();
    if (import_web_catalog($pdo, $manifest, false, true)['images_updated'] !== 2
        || $pdo->query('SELECT * FROM cpu WHERE id = 1001')->fetch() !== $beforeRefresh) throw new RuntimeException('Image dry run changed products.');
    if (import_web_catalog($pdo, $manifest, true, true)['images_updated'] !== 2
        || $pdo->query('SELECT * FROM cpu WHERE id = 1001')->fetch() !== $edited
        || $pdo->query('SELECT image_url FROM cpu WHERE id = 1002')->fetchColumn() !== 'admin-replacement.png'
        || import_web_catalog($pdo, $manifest, true, true)['images_updated'] !== 0) throw new RuntimeException('Image refresh did not preserve admin edits or was not idempotent.');
    foreach (component_categories() as $category => $label) {
        $count = (int) $pdo->query("SELECT COUNT(*) FROM `$category` WHERE id BETWEEN 1001 AND 1020")->fetchColumn();
        if ($count !== 20) throw new RuntimeException('Wrong category additions: ' . $category);
    }
} finally {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $pdo->exec('USE `' . str_replace('`', '``', $original) . '`');
    if ($created) $pdo->exec("DROP DATABASE `$temporary`");
}
echo "PASS additive upgrade: 20 per category, dry run, atomic conflict rollback, preserved existing products and admin edits, repeat import without duplicates, safe repeatable image refresh.\n";