<?php
require __DIR__ . '/../includes/admin-functions.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    try {
        $type = component_type($_POST['type'] ?? null); $id = admin_id($_POST['id'] ?? null);
        $stock = filter_var($_POST['stock'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>0,'max_range'=>2147483647]]);
        if ($stock === false) throw new InvalidArgumentException('Stock must be a nonnegative whole number.');
        $old = admin_text($_POST,'previous');
        if ($old !== '' && filter_var($old,FILTER_VALIDATE_INT,['options'=>['min_range'=>0]]) === false) throw new InvalidArgumentException('Invalid previous stock.');
        // Prevent a stale form from overwriting stock deducted by a completed order.
        $updated = admin_query("UPDATE `$type` SET stock = ? WHERE id = ? AND stock <=> ?",[$stock,$id,$old === '' ? null : (int)$old]);
        if (!$updated->rowCount() && (string)admin_product($type,$id)['stock'] !== (string)$stock) throw new InvalidArgumentException('Stock changed since you opened this page. Review the new value and try again.');
        flash_set('Stock updated.');
    } catch (Throwable $exception) { flash_set(admin_error($exception),'danger'); }
    redirect('admin/inventory.php');
}
[$where,$values] = admin_product_filters(); $source = admin_catalog_sql();
$total = (int)admin_query('SELECT COUNT(*) FROM ' . $source . $where,$values)->fetchColumn();
[$page,$offset,$pages] = admin_pagination($total);
$products = admin_query('SELECT * FROM ' . $source . $where . " ORDER BY stock IS NULL, stock, name, category, id LIMIT 20 OFFSET $offset",$values)->fetchAll();
admin_start('Inventory','Low stock: 1–' . store_settings()['low_stock_threshold'] . ' units. Blank stock values are unknown.');
?>
<section class="admin-panel"><?php admin_filter_form(); ?><div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current stock</th>
                    <th>Stock status</th>
                    <th>Last updated</th>
                    <th>Update stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?><tr>
                    <td><a href="<?= e(admin_product_link($product)) ?>"><?= e($product['name']) ?></a></td>
                    <td><?= e(component_categories()[$product['category']]) ?></td>
                    <td><?= $product['stock'] === null ? 'Unknown' : (int)$product['stock'] ?></td>
                    <td><?= admin_badge(admin_stock_status($product['stock'])) ?></td>
                    <td><?= e($product['updated_at'] ?? 'Unknown') ?></td>
                    <td>
                        <form class="stock-form" method="post"><?= csrf_field() ?><input type="hidden" name="type"
                                value="<?= e($product['category']) ?>"><input type="hidden" name="id"
                                value="<?= (int)$product['id'] ?>"><input type="hidden" name="previous"
                                value="<?= e((string)$product['stock']) ?>"><input class="form-control" name="stock"
                                type="number" min="0" max="2147483647" required
                                value="<?= e((string)$product['stock']) ?>"
                                aria-label="<?= e('Stock for ' . $product['name']) ?>"><button
                                class="btn btn-dark btn-sm">Update</button></form>
                    </td>
                </tr><?php endforeach; if (!$products) admin_empty(6,'No products match your stock filters.'); ?>
            </tbody>
        </table>
    </div><?php admin_pager($page,$pages,$total); ?></section><?php admin_end(); ?>