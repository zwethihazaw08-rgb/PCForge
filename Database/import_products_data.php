<?php
// CLI only. Dry run by default; --apply backs up the database before any writes.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../config/database.php';

function products_data_backup(PDO $pdo): string
{
    $directory = __DIR__ . '/backups';
    if (!is_dir($directory) || trim((string)file_get_contents($directory . '/.htaccess')) !== 'Require all denied') {
        throw new RuntimeException('A protected Database/backups directory is required.');
    }
    $path = $directory . '/before-products-data-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.sql';
    $file = fopen($path, 'xb');
    if (!$file) throw new RuntimeException('Could not create backup.');
    $write = static function (string $sql) use ($file): void {
        if (fwrite($file, $sql) !== strlen($sql)) throw new RuntimeException('Incomplete database backup.');
    };
    try {
        $pdo->beginTransaction();
        $write("-- PCForge before ProductsData import\nSET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n");
        foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
            $identifier = '`' . str_replace('`', '``', $table) . '`';
            $schema = $pdo->query("SHOW CREATE TABLE $identifier")->fetch(PDO::FETCH_NUM)[1];
            $write("DROP TABLE IF EXISTS $identifier;\n$schema;\n");
            foreach ($pdo->query("SELECT * FROM $identifier") as $row) {
                $columns = '`' . implode('`,`', array_keys($row)) . '`';
                $values = array_map(static fn($value) => $value === null ? 'NULL' : $pdo->quote((string)$value), array_values($row));
                $write("INSERT INTO $identifier ($columns) VALUES (" . implode(',', $values) . ");\n");
            }
        }
        $write("SET FOREIGN_KEY_CHECKS=1;\n");
        $pdo->commit();
    } finally {
        if ($pdo->inTransaction()) $pdo->rollBack();
        fclose($file);
    }
    return $path;
}

try {
    $mode = $argv[1] ?? '--dry-run';
    if (!in_array($mode, ['--dry-run', '--apply', '--verify'], true)) throw new InvalidArgumentException('Use --dry-run, --apply, or --verify.');
    $root = dirname(__DIR__);
    $manifest = json_decode(file_get_contents(__DIR__ . '/products_data.json'), true, 512, JSON_THROW_ON_ERROR);
    $categories = ['cpu','gpu','mb','memory','storage','psu','case_box','cooling','monitor'];
    if ($manifest['version'] !== 1 || $manifest['currency'] !== 'USD' || count($manifest['products']) !== 135) {
        throw new RuntimeException('Unexpected manifest. Rebuild and review it before importing.');
    }
    $sourcePath = static function (string $relative) use ($root): string {
        $path = realpath($root . '/' . $relative);
        $sourceRoot = realpath($root . '/ProductsData') . DIRECTORY_SEPARATOR;
        if (!$path || !str_starts_with($path, $sourceRoot) || !is_file($path)) throw new RuntimeException('Invalid source path.');
        return $path;
    };
    foreach ($manifest['sources'] as $source) {
        if (hash_file('sha256', $sourcePath($source['path'])) !== $source['sha256']) throw new RuntimeException('Source workbook changed. Run prepare_products_data.py again.');
    }
    $overridePath = __DIR__ . '/product_image_overrides.json';
    $imageOverrides = is_file($overridePath) ? json_decode(file_get_contents($overridePath), true, 512, JSON_THROW_ON_ERROR) : [];
    $seen = [];
    foreach ($manifest['products'] as &$product) {
        $type = $product['category'];
        $id = $product['source_id'];
        if (!in_array($type, $categories, true) || !is_int($id) || $id < 1 || $id > 15 || isset($seen[$type][$id])) throw new RuntimeException('Invalid or duplicate source identity.');
        $seen[$type][$id] = true;
        if (!preg_match('/^productsdata-' . $type . '-' . $id . '\.(jpg|jpeg|png|avif|webp)$/D', $product['values']['image_url'])) throw new RuntimeException('Invalid image filename.');
        if (hash_file('sha256', $sourcePath($product['image_source'])) !== $product['image_sha256']) throw new RuntimeException('Source image changed. Rebuild the manifest.');
        if (!preg_match('/^\d+\.\d{2}$/D', $product['values']['price']) || $product['values']['name'] === '') throw new RuntimeException('Invalid product name or price.');
        $product['catalog_image_source'] = $sourcePath($product['image_source']);
        $product['catalog_image_sha256'] = $product['image_sha256'];
        $override = $imageOverrides[$type . '-' . $id] ?? null;
        if ($override !== null) {
            $expectedName = "productsdata-$type-$id-transparent.png";
            $path = $root . '/assets/images/' . $expectedName;
            if ($override['image_url'] !== $expectedName || $override['source_sha256'] !== $product['image_sha256'] || !is_file($path) || hash_file('sha256', $path) !== $override['sha256']) throw new RuntimeException('Transparent image is stale or invalid; rerun background removal: ' . $type . '-' . $id);
            $product['values']['image_url'] = $expectedName;
            $product['catalog_image_source'] = $path;
            $product['catalog_image_sha256'] = $override['sha256'];
        }
    }
    unset($product);
    foreach ($categories as $type) if (count($seen[$type] ?? []) !== 15) throw new RuntimeException('Incomplete category: ' . $type);
    $pdo = db();
    if ($pdo->query('SELECT currency FROM store_settings WHERE id=1')->fetchColumn() !== 'USD') throw new RuntimeException('Source prices are USD; the store must use USD before importing.');
    if ($mode === '--dry-run') {
        echo "Validated 135 products, nine USD price lists, and every source image. No database changes.\n";
        exit;
    }
    if ($mode === '--apply') {
        $backup = products_data_backup($pdo);
        echo 'Backup: ' . $backup . PHP_EOL;
        // DDL commits implicitly in MariaDB, so do it before the data transaction.
        $pdo->exec(file_get_contents(__DIR__ . '/migrations/20260925_products_data.sql'));
        // Preflight every field against the actual schema before changing products.
        $columns = [];
        foreach ($categories as $type) $columns[$type] = array_column($pdo->query("SHOW COLUMNS FROM `$type`")->fetchAll(), null, 'Field');
        foreach ($manifest['products'] as $product) {
            foreach ($product['values'] as $field => $value) {
                $column = $columns[$product['category']][$field] ?? null;
                if (!$column) throw new RuntimeException('Unknown import field: ' . $field);
                if ($value !== null && preg_match('/^varchar\((\d+)\)/', $column['Type'], $match) && mb_strlen((string)$value) > (int)$match[1]) throw new RuntimeException('Source exceeds field length: ' . $field);
            }
            $destination = $root . '/assets/images/' . $product['values']['image_url'];
            if (!is_file($destination) || hash_file('sha256', $destination) !== $product['catalog_image_sha256']) {
                if (!copy($product['catalog_image_source'], $destination)) throw new RuntimeException('Image copy failed.');
            }
        }
        $pdo->beginTransaction();
        $retired = 0;
        foreach (['cpu'=>'ForgeCore','gpu'=>'ForgeVision','mb'=>'ForgeBoard','memory'=>'ForgeMemory','storage'=>'ForgeDrive','psu'=>'ForgePower','case_box'=>'ForgeCase','cooling'=>'ForgeCool','fans'=>'ForgeFlow'] as $type=>$brand) {
            // Keep historical IDs and order/build references; remove demos from sale.
            $retire = $pdo->prepare("UPDATE `$type` SET status='inactive' WHERE status='active' AND brand=? AND name LIKE ?");
            $retire->execute([$brand, $brand . ' %']);
            $retired += $retire->rowCount();
        }
        $find = $pdo->prepare('SELECT product_id FROM product_data_sources WHERE category=? AND source_id=?');
        $saveSource = $pdo->prepare('INSERT INTO product_data_sources (category,source_id,product_id,source_file,source_row,source_values) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE source_file=VALUES(source_file),source_row=VALUES(source_row),source_values=VALUES(source_values)');
        $inserted = 0; $updated = 0;
        foreach ($manifest['products'] as $product) {
            $type = $product['category']; $values = $product['values'];
            $find->execute([$type, $product['source_id']]);
            $id = $find->fetchColumn();
            if ($id !== false) {
                $exists = $pdo->prepare("SELECT id FROM `$type` WHERE id=? FOR UPDATE"); $exists->execute([$id]);
                if (!$exists->fetchColumn()) throw new RuntimeException('Imported product was deleted. Review its source mapping before reimporting.');
                $set = implode(',', array_map(static fn($field) => "`$field`=?", array_keys($values)));
                // Reimports preserve stock quantities and publication status set by admins.
                $pdo->prepare("UPDATE `$type` SET $set WHERE id=?")->execute([...array_values($values), $id]);
                $updated++;
            } else {
                $fields = '`' . implode('`,`', array_keys($values)) . '`';
                $placeholders = implode(',', array_fill(0, count($values), '?'));
                $pdo->prepare("INSERT INTO `$type` ($fields,stock,status) VALUES ($placeholders,NULL,'active')")->execute(array_values($values));
                $id = (int)$pdo->lastInsertId(); $inserted++;
            }
            $saveSource->execute([$type,$product['source_id'],$id,$product['source_file'],$product['source_row'],json_encode($product['source_values'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)]);
            if (in_array($type, ['case_box','cooling'], true)) {
                [$table, $key, $field] = $type === 'case_box' ? ['case_motherboard_support','case_id','form_factor'] : ['cooling_socket_support','cooling_id','socket'];
                $pdo->prepare("DELETE FROM `$table` WHERE `$key`=?")->execute([$id]);
                $support = $pdo->prepare("INSERT INTO `$table` (`$key`,`$field`) VALUES (?,?)");
                foreach ($product['support'] as $value) $support->execute([$id,$value]);
            }
        }
        $pdo->commit();
        echo "Imported $inserted new products; refreshed $updated existing imports; retired $retired sample products.\n";
    }
    $find = $pdo->prepare('SELECT product_id,source_values FROM product_data_sources WHERE category=? AND source_id=?');
    $counts = [];
    foreach ($manifest['products'] as $product) {
        $type = $product['category'];
        $find->execute([$type,$product['source_id']]); $source = $find->fetch();
        if (!$source || json_decode($source['source_values'], true, 512, JSON_THROW_ON_ERROR) != $product['source_values']) throw new RuntimeException('Source provenance mismatch.');
        $query = $pdo->prepare("SELECT * FROM `$type` WHERE id=?"); $query->execute([$source['product_id']]); $row = $query->fetch();
        if (!$row) throw new RuntimeException('Missing imported product.');
        foreach ($product['values'] as $field=>$value) {
            if (($row[$field] === null) !== ($value === null) || (string)$row[$field] !== (string)$value) throw new RuntimeException("Mismatch: $type/{$product['source_id']}/$field");
        }
        if (hash_file('sha256', $root . '/assets/images/' . $row['image_url']) !== $product['catalog_image_sha256']) throw new RuntimeException('Imported image mismatch.');
        if (in_array($type, ['case_box','cooling'], true)) {
            [$table,$key,$field] = $type === 'case_box' ? ['case_motherboard_support','case_id','form_factor'] : ['cooling_socket_support','cooling_id','socket'];
            $query = $pdo->prepare("SELECT `$field` FROM `$table` WHERE `$key`=?"); $query->execute([$row['id']]);
            $actual = $query->fetchAll(PDO::FETCH_COLUMN); $expected = $product['support']; sort($actual); sort($expected);
            if ($actual !== $expected) throw new RuntimeException('Compatibility relationship mismatch.');
        }
        $counts[$type] = ($counts[$type] ?? 0) + 1;
    }
    echo 'Verified source values, catalog fields, image hashes, and compatibility relationships: ' . json_encode($counts) . PHP_EOL;
} catch (Throwable $error) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}
