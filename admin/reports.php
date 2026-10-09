<?php
require __DIR__ . '/../includes/admin-functions.php';
$range = admin_text($_GET,'range','30');
$end = date('Y-m-d');
$start = match($range) { 'today'=>$end,'7'=>date('Y-m-d',strtotime('-6 days')),'year'=>date('Y-01-01'),default=>date('Y-m-d',strtotime('-29 days')) };
if ($range === 'custom') { $start=admin_text($_GET,'start'); $end=admin_text($_GET,'end'); }
foreach ([$start,$end] as $date) {
    $parsed=DateTimeImmutable::createFromFormat('!Y-m-d',$date);
    if (!$parsed || $parsed->format('Y-m-d') !== $date) throw new InvalidArgumentException('Choose valid report dates.');
}
if ($start > $end) throw new InvalidArgumentException('Start date must be before end date.');
$until = (new DateTimeImmutable($end))->modify('+1 day')->format('Y-m-d');
$values = [$start,$until];
$count = (int)admin_query('SELECT COUNT(*) FROM orders WHERE created_at >= ? AND created_at < ?',$values)->fetchColumn();
$sales = admin_query("SELECT currency,COUNT(*) AS orders,SUM(total) AS revenue,AVG(total) AS average FROM orders WHERE created_at >= ? AND created_at < ? AND status='completed' AND payment_status='paid' GROUP BY currency",$values)->fetchAll();
$demo = (int)admin_query("SELECT COUNT(*) FROM orders WHERE created_at >= ? AND created_at < ? AND payment_status='demo'",$values)->fetchColumn();
$monthly = admin_query("SELECT DATE_FORMAT(created_at,'%Y-%m') AS month,currency,SUM(total) AS revenue FROM orders WHERE created_at >= ? AND created_at < ? AND status='completed' AND payment_status='paid' GROUP BY month,currency ORDER BY month",$values)->fetchAll();
$top = admin_query("SELECT i.category,i.product_id,MAX(i.product_name) AS name,SUM(i.quantity) AS units FROM order_items i JOIN orders o ON o.id=i.order_id WHERE o.created_at >= ? AND o.created_at < ? AND o.status='completed' GROUP BY i.category,i.product_id ORDER BY units DESC LIMIT 10",$values)->fetchAll();
$popular = admin_query("SELECT i.category,SUM(i.quantity) AS units FROM order_items i JOIN orders o ON o.id=i.order_id WHERE o.created_at >= ? AND o.created_at < ? AND o.status='completed' GROUP BY i.category ORDER BY units DESC LIMIT 1",$values)->fetch();
$lowCount = (int)admin_query('SELECT COUNT(*) FROM ' . admin_catalog_sql() . ' WHERE stock > 0 AND stock <= ?',[(int)store_settings()['low_stock_threshold']])->fetchColumn();
admin_start('Reports','Simple sales and fulfillment summaries. Date filters use the order creation date.');
?>
<section class="admin-panel mb-4">
    <form class="admin-filters" method="get">
        <div><label for="range">Period</label><select class="form-select" name="range"
                id="range"><?php foreach (['today'=>'Today','7'=>'Last 7 days','30'=>'Last 30 days','year'=>'This year','custom'=>'Custom'] as $value=>$label): ?>
                <option value="<?= e((string)$value) ?>" <?= $range === (string)$value ? 'selected' : '' ?>>
                    <?= e($label) ?></option><?php endforeach; ?>
            </select></div>
        <div><label for="start">Start (custom)</label><input class="form-control" type="date" id="start" name="start"
                value="<?= e($start) ?>"></div>
        <div><label for="end">End (custom)</label><input class="form-control" type="date" id="end" name="end"
                value="<?= e($end) ?>"></div><button class="btn btn-dark">Apply dates</button>
    </form>
    <p class="small text-muted mb-0"><?= e($start) ?> through <?= e($end) ?> · <?= $count ?> orders · <?= $demo ?> demo
        orders</p>
</section>
<div class="row g-4">
    <div class="col-lg-7">
        <section class="admin-panel">
            <h2 class="h5">Revenue and average order value</h2>
            <p class="small text-muted">Completed, paid orders only. Demo payments are excluded; currencies are never
                combined.</p>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Currency</th>
                            <th>Paid orders</th>
                            <th>Revenue</th>
                            <th>Average</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($sales as $sale): ?><tr>
                            <td><?= e($sale['currency']) ?></td>
                            <td><?= (int)$sale['orders'] ?></td>
                            <td><?= e(admin_money($sale['revenue'],$sale['currency'])) ?></td>
                            <td><?= e(admin_money($sale['average'],$sale['currency'])) ?></td>
                        </tr>
                        <?php endforeach; if (!$sales) admin_empty(4,'No completed paid orders in this period. Revenue is 0.'); ?>
                    </tbody>
                </table>
            </div>
            <h2 class="h5 mt-4">Monthly paid sales</h2><?php foreach ($monthly as $month): ?><div class="activity-row">
                <span><?= e($month['month']) ?></span><strong><?= e(admin_money($month['revenue'],$month['currency'])) ?></strong>
            </div><?php endforeach; if (!$monthly): ?><p class="text-muted">No paid sales in this period.</p>
            <?php endif; ?>
        </section>
    </div>
    <div class="col-lg-5">
        <section class="admin-panel">
            <h2 class="h5">Catalog snapshot</h2>
            <p>Most purchased category:
                <strong><?= e($popular ? component_categories()[$popular['category']] : 'No completed orders') ?></strong>
            </p><a href="inventory.php?filter=low"><?= $lowCount ?> currently low-stock products →</a>
        </section>
    </div>
</div>
<section class="admin-panel mt-4">
    <h2 class="h5">Top purchased components</h2>
    <p class="small text-muted">Units on completed orders, including demonstration orders.</p>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Units</th>
                </tr>
            </thead>
            <tbody><?php foreach ($top as $product): ?><tr>
                    <td><?= e($product['name']) ?></td>
                    <td><?= e(component_categories()[$product['category']]) ?></td>
                    <td><?= (int)$product['units'] ?></td>
                </tr><?php endforeach; if (!$top) admin_empty(3,'No completed orders in this period.'); ?></tbody>
        </table>
    </div>
</section><?php admin_end(); ?>