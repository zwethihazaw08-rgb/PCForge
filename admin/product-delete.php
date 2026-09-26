<?php
require __DIR__ . '/../includes/admin-functions.php';
$type = component_type($_GET['type'] ?? null);
$id = admin_id($_GET['id'] ?? null);
$product = admin_product($type,$id);
$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    try {
        admin_query("UPDATE `$type` SET status = 'inactive' WHERE id = ?",[$id]);
        flash_set('Product disabled. Order history and saved build references are preserved.');
        redirect('admin/products.php');
    } catch (Throwable $exception) { $error = admin_error($exception); }
}
admin_start('Disable product','Products are soft-deleted to preserve customer history.'); admin_alert($error);
?>
<section class="admin-panel"><h2 class="h4">Disable <?= e($product['name']) ?>?</h2><p>The product will be hidden from the storefront. Its record remains available for orders and saved builds. You can reactivate it from Edit product.</p><form method="post" data-confirm="Disable this product and hide it from the storefront?"><?= csrf_field() ?><button class="btn btn-danger" type="submit">Disable product</button> <a class="btn btn-outline-secondary" href="products.php">Cancel</a></form></section>
<?php admin_end(); ?>
