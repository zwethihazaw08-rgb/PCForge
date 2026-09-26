<?php
require __DIR__ . '/../includes/admin-functions.php';
[$where,$values] = admin_product_filters();
$source = admin_catalog_sql();
$total = (int) admin_query('SELECT COUNT(*) FROM ' . $source . $where,$values)->fetchColumn();
[$page,$offset,$pages] = admin_pagination($total);
$products = admin_query('SELECT * FROM ' . $source . $where . " ORDER BY name, category, id LIMIT 20 OFFSET $offset",$values)->fetchAll();
admin_start('Products','Your entire hardware catalog, organized in one place.');
?>
<section class="admin-panel">
<div class="panel-heading mb-4"><h2>Component catalog</h2><a class="btn btn-dark" href="product-add.php"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add product</a></div>
<?php admin_filter_form(); ?>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Product</th><th>Category</th><th>Brand</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($products as $product): $image = admin_image($product['image_url']); ?>
<tr><td><div class="product-cell"><?php if ($image): ?><img class="product-thumb" loading="lazy" src="<?= e($image) ?>" alt="<?= e($product['name']) ?>"><?php endif; ?><a href="<?= e(admin_product_link($product)) ?>"><?= e($product['name']) ?></a></div></td><td><?= e(component_categories()[$product['category']]) ?></td><td><?= e($product['brand']) ?></td><td class="text-nowrap"><?= e(admin_money($product['price'])) ?></td><td><?= $product['stock'] === null ? 'Unknown' : (int)$product['stock'] ?></td><td><?= admin_badge($product['status']) ?></td><td><div class="table-actions"><a href="<?= e(admin_product_link($product)) ?>">View</a><a href="<?= e(admin_product_link($product,'edit')) ?>">Edit</a><a href="<?= e(admin_product_link($product,'delete')) ?>">Disable / delete</a></div></td></tr>
<?php endforeach; if (!$products) admin_empty(7,'No products match your filters. Clear the filters or add a component.'); ?>
</tbody></table></div><?php admin_pager($page,$pages,$total); ?>
</section><?php admin_end(); ?>
