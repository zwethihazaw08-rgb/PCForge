<?php
require_once __DIR__ . '/admin-functions.php';

function admin_product_columns(string $type): array
{
    $type = component_type($type);
    $columns = [];
    foreach (admin_query("SHOW COLUMNS FROM `$type`")->fetchAll() as $column) {
        if (!in_array($column['Field'], ['id','created_at','updated_at'],true)) $columns[$column['Field']] = $column;
    }
    return $columns;
}

function admin_field_label(string $field): string
{
    return ['tdp'=>'TDP (W)','image_url'=>'Image URL or existing filename','length_mm'=>'Length (mm)','height_mm'=>'Height (mm)','max_gpu_length'=>'Maximum GPU length (mm)','stock'=>'Stock quantity','psu'=>'Recommended PSU (text)','memory_type'=>'Memory type','power_watts'=>'Power (W)'][$field] ?? ucwords(str_replace('_',' ',$field));
}

function admin_validate_product(array $input, array $columns): array
{
    $data = [];
    foreach ($columns as $field=>$column) {
        if (isset($input[$field]) && !is_string($input[$field])) throw new InvalidArgumentException('Invalid ' . admin_field_label($field) . '.');
        $value = admin_text($input,$field);
        $label = admin_field_label($field);
        if ($value === '') {
            if ($column['Null'] === 'NO') throw new InvalidArgumentException($label . ' is required.');
            $data[$field] = null; continue;
        }
        if (str_starts_with($column['Type'],'int')) {
            $number = filter_var($value,FILTER_VALIDATE_INT,['options'=>['min_range'=>0,'max_range'=>2147483647]]);
            if ($number === false) throw new InvalidArgumentException($label . ' must be a nonnegative whole number.');
            $value = $number;
        } elseif (str_starts_with($column['Type'],'decimal')) {
            $value = cents_decimal(price_cents($value));
        } elseif (preg_match('/^varchar\((\d+)\)/',$column['Type'],$match) && mb_strlen($value) > (int)$match[1]) {
            throw new InvalidArgumentException($label . ' is too long (maximum ' . $match[1] . ' characters).');
        } elseif (str_starts_with($column['Type'],'enum')) {
            preg_match_all("/'([^']+)'/",$column['Type'],$choices);
            if (!in_array($value,$choices[1],true)) throw new InvalidArgumentException('Choose a valid ' . strtolower($label) . '.');
        } elseif ($column['Type'] === 'text' && strlen($value) > 60000) {
            throw new InvalidArgumentException('Description is too long.');
        }
        if ($field === 'image_url' && $value !== 'placeholder.png' && !admin_image($value)) throw new InvalidArgumentException('Use an HTTPS image URL, an existing image filename, or upload an image.');
        $data[$field] = $value;
    }
    return $data;
}

function admin_upload_image(): ?string
{
    if (!isset($_FILES['image']) || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    $file = $_FILES['image'];
    if (!is_string($file['tmp_name'] ?? null) || !is_string($file['name'] ?? null) || $file['error'] !== UPLOAD_ERR_OK || $file['size'] > 3 * 1024 * 1024) throw new InvalidArgumentException('Upload a JPG, PNG or WebP image no larger than 3 MB.');
    $allowed = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp'];
    $extension = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($allowed[$extension]) || $allowed[$extension] !== $mime || !getimagesize($file['tmp_name'])) throw new InvalidArgumentException('Invalid image type. Only JPG, PNG and WebP are accepted.');
    $filename = 'product_' . bin2hex(random_bytes(16)) . '.' . $extension;
    if (!move_uploaded_file($file['tmp_name'],__DIR__ . '/../assets/images/' . $filename)) throw new RuntimeException('Image upload could not be saved.');
    return $filename;
}

$typeInput = admin_text($_GET,'type');
if ($typeInput === '') {
    admin_start('Add component','Choose a component category to see its specifications.');
    echo '<div class="category-grid">';
    foreach (component_categories() as $key=>$label) echo '<a class="admin-panel" href="?type=' . e($key) . '"><i class="bi bi-cpu" aria-hidden="true"></i><h2>' . e($label) . '</h2><span>Add component →</span></a>';
    echo '</div>'; admin_end(); return;
}
$type = component_type($typeInput);
$editing = $productEditing ?? false;
$id = $editing ? admin_id($_GET['id'] ?? null) : null;
$record = $editing ? admin_product($type,$id) : ['status'=>'active','stock'=>'0'];
$columns = admin_product_columns($type);
$error = '';
$support = '';
if ($editing && in_array($type,['case_box','cooling'],true)) {
    $support = implode(', ', $type === 'case_box'
        ? admin_query('SELECT form_factor FROM case_motherboard_support WHERE case_id = ?',[$id])->fetchAll(PDO::FETCH_COLUMN)
        : admin_query('SELECT socket FROM cooling_socket_support WHERE cooling_id = ?',[$id])->fetchAll(PDO::FETCH_COLUMN));
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $uploaded = null;
    try {
        $input = $_POST;
        if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) $input['image_url'] = '';
        $data = admin_validate_product($input,$columns);
        $support = admin_text($_POST,'support');
        $supports = array_values(array_unique(array_filter(array_map('trim',explode(',',$support)))));
        if (count($supports) > 50) throw new InvalidArgumentException('List at most 50 supported sockets or form factors.');
        foreach ($supports as $entry) if (mb_strlen($entry) > 50) throw new InvalidArgumentException('Each supported socket or form factor must be at most 50 characters.');
        $uploaded = admin_upload_image();
        if ($uploaded) $data['image_url'] = $uploaded;
        db()->beginTransaction();
        if ($editing) {
            $current = admin_query("SELECT stock FROM `$type` WHERE id = ? FOR UPDATE",[$id])->fetch();
            if (!$current || !array_key_exists('previous_stock',$_POST)
                || !is_string($_POST['previous_stock']) || (string)$current['stock'] !== $_POST['previous_stock']) {
                throw new InvalidArgumentException('Stock changed while this form was open. Reload the product before saving.');
            }
            $sets = array_map(fn($column)=>"`$column` = ?",array_keys($data));
            admin_query("UPDATE `$type` SET " . implode(', ',$sets) . ' WHERE id = ?', [...array_values($data),$id]);
        } else {
            $fields = '`' . implode('`, `',array_keys($data)) . '`';
            admin_query("INSERT INTO `$type` ($fields) VALUES (" . implode(',',array_fill(0,count($data),'?')) . ')',array_values($data));
            $id = (int) db()->lastInsertId();
        }
        if ($type === 'case_box') {
            admin_query('DELETE FROM case_motherboard_support WHERE case_id = ?',[$id]);
            foreach ($supports as $entry) admin_query('INSERT INTO case_motherboard_support (case_id,form_factor) VALUES (?,?)',[$id,$entry]);
        } elseif ($type === 'cooling') {
            admin_query('DELETE FROM cooling_socket_support WHERE cooling_id = ?',[$id]);
            foreach ($supports as $entry) admin_query('INSERT INTO cooling_socket_support (cooling_id,socket) VALUES (?,?)',[$id,$entry]);
        }
        db()->commit();
        flash_set($editing ? 'Product updated.' : 'Product added.');
        redirect('admin/product-view.php?type=' . $type . '&id=' . $id);
    } catch (Throwable $exception) {
        if (db()->inTransaction()) db()->rollBack();
        if ($uploaded) unlink(__DIR__ . '/../assets/images/' . $uploaded);
        $error = admin_error($exception);
        $record = $_POST;
    }
}
admin_start($editing ? 'Edit product' : 'Add product',component_categories()[$type] . ' · Fields match the existing database.');
admin_alert($error);
?>
<form method="post" enctype="multipart/form-data" class="admin-panel">
<?= csrf_field() ?>
<?php if ($editing): ?><input type="hidden" name="previous_stock" value="<?= e(is_string($_POST['previous_stock'] ?? null) ? $_POST['previous_stock'] : (string)($record['stock'] ?? '')) ?>"><?php endif; ?>
<div class="row g-4">
<?php foreach ($columns as $field=>$column): $value = is_scalar($record[$field] ?? null) ? (string)$record[$field] : ''; ?>
<div class="<?= $field === 'description' ? 'col-12' : 'col-md-6 col-xl-4' ?>"><label class="form-label" for="<?= e($field) ?>"><?= e(admin_field_label($field)) ?><?= $column['Null'] === 'NO' ? ' *' : '' ?></label>
<?php if (str_starts_with($column['Type'],'enum')): preg_match_all("/'([^']+)'/",$column['Type'],$choices); ?>
<select class="form-select" name="<?= e($field) ?>" id="<?= e($field) ?>"><?php if ($column['Null'] === 'YES'): ?><option value="">Unknown</option><?php endif; ?><?php foreach ($choices[1] as $choice): ?><option <?= $choice === $value ? 'selected' : '' ?> value="<?= e($choice) ?>"><?= e(ucfirst($choice)) ?></option><?php endforeach; ?></select>
<?php elseif ($field === 'description'): ?><textarea class="form-control" rows="4" id="description" name="description"><?= e($value) ?></textarea>
<?php else: $numeric = str_starts_with($column['Type'],'int') || str_starts_with($column['Type'],'decimal'); ?>
<input class="form-control" id="<?= e($field) ?>" name="<?= e($field) ?>" value="<?= e($value) ?>" type="<?= $numeric ? 'number' : 'text' ?>" <?= $numeric ? 'min="0" step="' . ($field === 'price' ? '0.01' : '1') . '"' : '' ?> <?= $column['Null'] === 'NO' ? 'required' : '' ?>>
<?php endif; ?></div><?php endforeach; ?>
<?php if (in_array($type,['case_box','cooling'],true)): ?><div class="col-12"><label for="support" class="form-label"><?= $type === 'case_box' ? 'Supported motherboard sizes (e.g. ATX, Micro-ATX, Mini-ITX)' : 'Supported CPU sockets (e.g. AM4, AM5, LGA1700)' ?></label><input id="support" name="support" class="form-control" value="<?= e($support) ?>"><small class="text-muted">Comma-separated, verified support only. Leave blank when unknown.</small></div><?php endif; ?>
<div class="col-12"><label class="form-label" for="image">Upload image (optional, replaces image URL)</label><input class="form-control" id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp"><small class="text-muted">Maximum 3 MB. Blank specifications and stock mean unknown.</small><img id="upload-preview" alt="Selected product image preview" class="product-preview mt-3" hidden></div>
</div><div class="mt-4 d-flex gap-2"><button class="btn btn-dark" type="submit">Save product</button><a class="btn btn-outline-secondary" href="products.php">Cancel</a></div>
</form><?php admin_end(); ?>
