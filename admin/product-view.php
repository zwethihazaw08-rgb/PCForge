<?php
require __DIR__ . '/../includes/admin-functions.php';
$type = component_type($_GET['type'] ?? null);
$product = admin_product($type,admin_id($_GET['id'] ?? null));
$product['category'] = $type;
admin_start($product['name'],component_categories()[$type]);
$image = admin_image($product['image_url']);
?>
<div class="admin-panel"><div class="d-flex flex-wrap gap-3 mb-4"><?php if ($image): ?><img class="product-preview" src="<?= e($image) ?>" alt="<?= e($product['name']) ?>"><?php endif; ?><div><p class="h3"><?= e(admin_money($product['price'])) ?></p><p><?= admin_badge($product['status']) ?> <?= admin_badge(admin_stock_status($product['stock'])) ?></p><a class="btn btn-dark" href="<?= e(admin_product_link($product,'edit')) ?>">Edit product</a> <a class="btn btn-outline-secondary" href="<?= e(admin_product_link($product,'delete')) ?>">Disable / delete</a></div></div>
<dl class="spec-grid"><?php foreach ($product as $key=>$value): if (in_array($key,['image_url','category'],true)) continue; ?><div><dt><?= e(ucwords(str_replace('_',' ',$key))) ?></dt><dd><?= e($value === null ? 'Unknown' : (string)$value) ?></dd></div><?php endforeach; ?></dl>
<?php if ($type === 'case_box' || $type === 'cooling'): $supported = $type === 'case_box' ? admin_query('SELECT form_factor FROM case_motherboard_support WHERE case_id = ?',[$product['id']])->fetchAll(PDO::FETCH_COLUMN) : admin_query('SELECT socket FROM cooling_socket_support WHERE cooling_id = ?',[$product['id']])->fetchAll(PDO::FETCH_COLUMN); ?><p><strong><?= $type === 'case_box' ? 'Supported motherboards' : 'Supported sockets' ?>:</strong> <?= e($supported ? implode(', ',$supported) : 'Unknown') ?></p><?php endif; ?>
</div><?php admin_end(); ?>
