<?php
require __DIR__ . '/../includes/admin-functions.php';

$catalog = admin_catalog_sql();
$threshold = (int)store_settings()['low_stock_threshold'];
$counts = admin_query("SELECT COUNT(*) AS total, COALESCE(SUM(status='active'),0) AS active, COALESCE(SUM(stock > 0 AND stock <= ?),0) AS low, COALESCE(SUM(stock=0),0) AS out_of_stock FROM $catalog", [$threshold])->fetch();
$customers = (int)admin_query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn();
$builds = (int)admin_query('SELECT COUNT(*) FROM saved_builds')->fetchColumn();
$orderCount = (int)admin_query('SELECT COUNT(*) FROM orders')->fetchColumn();
$revenue = admin_query("SELECT currency, SUM(total) AS total FROM orders WHERE status='completed' AND payment_status='paid' GROUP BY currency")->fetchAll();
$revenueText = $revenue ? implode(' · ', array_map(fn($row)=>admin_money($row['total'],$row['currency']), $revenue)) : admin_money('0');
$recent = admin_query('SELECT id,order_number,user_id,customer_name,total,currency,status,created_at FROM orders ORDER BY id DESC LIMIT 5')->fetchAll();
$latest = admin_query("SELECT * FROM $catalog WHERE created_at IS NOT NULL ORDER BY created_at DESC, category, id DESC LIMIT 5")->fetchAll();
$low = admin_query("SELECT * FROM $catalog WHERE stock > 0 AND stock <= ? ORDER BY stock,name LIMIT 5", [$threshold])->fetchAll();
$distribution = admin_query("SELECT category,COUNT(*) AS total FROM $catalog GROUP BY category")->fetchAll(PDO::FETCH_KEY_PAIR);
$statuses = admin_query('SELECT status,COUNT(*) FROM orders GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
$currency = store_settings()['currency'];
$sales = admin_query("SELECT DATE_FORMAT(created_at,'%Y-%m') AS month,SUM(total) AS total FROM orders WHERE status='completed' AND payment_status='paid' AND currency=? AND created_at >= DATE_FORMAT(CURRENT_DATE - INTERVAL 5 MONTH,'%Y-%m-01') GROUP BY month ORDER BY month", [$currency])->fetchAll(PDO::FETCH_KEY_PAIR);
$months = [];
$monthValues = [];
for ($i=5; $i>=0; $i--) {
    $month = date('Y-m', strtotime(date('Y-m-01') . " -$i months"));
    $months[] = $month;
    $monthValues[] = (float)($sales[$month] ?? 0);
}
$cards = [
    ['Total products',$counts['total'],'products.php','cpu'],
    ['Active products',$counts['active'],'products.php?status=active','check-circle'],
    ['Customers',$customers,'users.php','people'],
    ['Total orders',$orderCount,'orders.php','receipt'],
    ['Paid revenue',$revenueText,'reports.php','cash-stack'],
    ['Saved PC builds',$builds,'builds.php','pc-display'],
    ['Low stock',$counts['low'],'inventory.php?filter=low','exclamation-circle'],
    ['Out of stock',$counts['out_of_stock'],'inventory.php?filter=out','box'],
];
admin_start('Dashboard', 'Welcome back, ' . auth_user()['username'] . '. Here is your store at a glance.');
?>
<div class="kpi-grid">
    <?php foreach ($cards as [$label,$value,$link,$icon]): ?>
    <a class="admin-panel kpi-card" href="<?= e($link) ?>">
        <span><?= e($label) ?><i class="bi bi-<?= e($icon) ?>" aria-hidden="true"></i></span>
        <strong><?= e((string)$value) ?></strong>
        <small>View details ↗</small>
    </a>
    <?php endforeach; ?>
</div>
<div class="row g-4 mt-1">
    <div class="col-xl-6">
        <section class="admin-panel h-100">
            <h2 class="h5">Sales overview <small class="text-muted"><?= e($currency) ?></small></h2>
            <p class="text-muted small">Completed, paid orders by order month. Demo payments are excluded.</p>
            <div class="chart-wrap"><canvas id="sales-chart" role="img"
                    aria-label="Paid revenue over the last six months"></canvas></div>
            <details>
                <summary>View sales data</summary>
                <?php foreach ($months as $i=>$month): ?>
                <div class="activity-row">
                    <span><?= e($month) ?></span><span><?= e(admin_money($monthValues[$i],$currency)) ?></span>
                </div>
                <?php endforeach; ?>
            </details>
        </section>
    </div>
    <div class="col-md-6 col-xl-3">
        <section class="admin-panel h-100">
            <h2 class="h5">Orders</h2>
            <div class="chart-wrap"><canvas id="orders-chart" role="img" aria-label="Orders by status"></canvas></div>
            <div class="chart-legend">
                <?php foreach (['pending','processing','completed','cancelled'] as $status): ?>
                <a href="orders.php?status=<?= e($status) ?>"><?= e(ucfirst($status)) ?>
                    <strong><?= (int)($statuses[$status] ?? 0) ?></strong></a>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    <div class="col-md-6 col-xl-3">
        <section class="admin-panel h-100">
            <h2 class="h5">Component mix</h2>
            <div class="chart-wrap"><canvas id="components-chart" role="img"
                    aria-label="Products by component category"></canvas></div>
            <details>
                <summary>View category counts</summary>
                <?php foreach (component_categories() as $type=>$label): ?>
                <div class="activity-row">
                    <span><?= e($label) ?></span><span><?= (int)($distribution[$type] ?? 0) ?></span>
                </div>
                <?php endforeach; ?>
            </details>
        </section>
    </div>
</div>
<section class="admin-panel mt-4">
    <div class="panel-heading mb-3">
        <h2>Recent orders</h2><a href="orders.php">View all →</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $order): ?>
                <tr>
                    <td><a href="order-view.php?id=<?= (int)$order['id'] ?>"><?= e($order['order_number']) ?></a></td>
                    <td>
                        <?php if ($order['user_id']): ?><a
                            href="user-view.php?id=<?= (int)$order['user_id'] ?>"><?= e($order['customer_name']) ?></a>
                        <?php else: ?><?= e($order['customer_name']) ?><?php endif; ?>
                    </td>
                    <td><?= e(admin_money($order['total'],$order['currency'])) ?></td>
                    <td><?= admin_badge($order['status']) ?></td>
                    <td><?= e($order['created_at']) ?></td>
                </tr>
                <?php endforeach; if (!$recent) admin_empty(5,'No orders yet. New checkouts will appear here.'); ?>
            </tbody>
        </table>
    </div>
</section>
<div class="row g-4 mt-1">
    <div class="col-lg-8">
        <section class="admin-panel h-100">
            <div class="panel-heading mb-3">
                <h2>Recently added products</h2><a href="product-add.php">Add product →</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($latest as $product): ?>
                        <tr>
                            <td><a href="<?= e(admin_product_link($product)) ?>"><?= e($product['name']) ?></a></td>
                            <td><?= e(component_categories()[$product['category']]) ?></td>
                            <td><?= e(admin_money($product['price'])) ?></td>
                            <td><?= e($product['stock'] === null ? 'Unknown' : (string)$product['stock']) ?></td>
                            <td><?= admin_badge($product['status']) ?></td>
                        </tr>
                        <?php endforeach; if (!$latest) admin_empty(5,'No products with a recorded creation date yet. Existing product history is unknown.'); ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
    <div class="col-lg-4">
        <section class="admin-panel h-100">
            <div class="panel-heading mb-3">
                <h2>Low stock</h2><a href="inventory.php?filter=low">Manage →</a>
            </div>
            <?php foreach ($low as $product): ?>
            <div class="activity-row">
                <a href="<?= e(admin_product_link($product,'edit')) ?>"><?= e($product['name']) ?><small
                        class="d-block text-muted"><?= e(component_categories()[$product['category']]) ?></small></a>
                <span class="badge text-bg-warning"><?= (int)$product['stock'] ?> left</span>
            </div>
            <?php endforeach; if (!$low): ?><p class="text-muted">No products with known low stock.</p><?php endif; ?>
        </section>
    </div>
</div>
<script type="application/json" id="dashboard-data">
<?= json_encode([
    'months'=>$months, 'sales'=>$monthValues,
    'statuses'=>array_map(fn($status)=>(int)($statuses[$status] ?? 0),['pending','processing','completed','cancelled']),
    'labels'=>array_values(component_categories()),
    'counts'=>array_map(fn($type)=>(int)($distribution[$type] ?? 0),array_keys(component_categories()))
], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?>
</script>
<script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
<script defer src="<?= e(url('assets/js/admin-charts.js')) ?>"></script>
<?php admin_end(); ?>