<?php
require __DIR__ . '/../includes/admin-functions.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    try {
        $type = component_type($_POST['type'] ?? null);
        $status = admin_text($_POST,'status');
        if (!in_array($status,['active','inactive'],true)) throw new InvalidArgumentException('Invalid status.');
        admin_query("UPDATE `$type` SET status = ?",[$status]);
        flash_set('Category products updated.');
    } catch (Throwable $exception) { flash_set(admin_error($exception),'danger'); }
    redirect('admin/categories.php');
}
$counts = admin_query('SELECT category, COUNT(*) AS total, SUM(status = \'active\') AS active FROM ' . admin_catalog_sql() . ' GROUP BY category')->fetchAll(PDO::FETCH_UNIQUE);
admin_start('Categories','Component types use the existing database tables. Manage products and visibility within each category.');
?>
<div class="category-grid"><?php foreach (component_categories() as $type=>$label): ?><section class="admin-panel"><i
            class="bi bi-cpu" aria-hidden="true"></i>
        <h2><?= e($label) ?></h2>
        <p><?= (int)($counts[$type]['total'] ?? 0) ?> products · <?= (int)($counts[$type]['active'] ?? 0) ?> active</p>
        <div class="table-actions mb-3"><a href="products.php?category=<?= e($type) ?>">Manage</a><a
                href="product-add.php?type=<?= e($type) ?>">Add product</a></div>
        <form method="post" data-confirm="This changes visibility for every product in this category. Continue?">
            <?= csrf_field() ?><input type="hidden" name="type" value="<?= e($type) ?>"><label class="visually-hidden"
                for="status-<?= e($type) ?>">Visibility for <?= e($label) ?></label>
            <div class="d-flex gap-2"><select class="form-select" id="status-<?= e($type) ?>" name="status">
                    <option value="inactive">Disable all</option>
                    <option value="active">Activate all</option>
                </select><button class="btn btn-outline-secondary">Apply</button></div>
        </form>
    </section><?php endforeach; ?></div><?php admin_end(); ?>