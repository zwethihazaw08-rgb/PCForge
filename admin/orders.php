<?php
require __DIR__ . '/../includes/admin-functions.php';
$where = []; $values = []; $status = admin_text($_GET,'status');
if ($status !== '') { $where[] = 'o.status = ?'; $values[] = $status; }
if (isset($_GET['user_id'])) { $where[] = 'o.user_id = ?'; $values[] = admin_id($_GET['user_id']); }
$q = admin_text($_GET,'q');
if ($q !== '') { $where[] = '(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ?)'; array_push($values,"%$q%","%$q%","%$q%"); }
$where = $where ? ' WHERE ' . implode(' AND ',$where) : '';
$total = (int)admin_query('SELECT COUNT(*) FROM orders o' . $where,$values)->fetchColumn();
[$page,$offset,$pages] = admin_pagination($total);
$orders = admin_query('SELECT o.*, (SELECT SUM(quantity) FROM order_items WHERE order_id=o.id) AS item_count FROM orders o' . $where . " ORDER BY o.id DESC LIMIT 20 OFFSET $offset",$values)->fetchAll();
admin_start('Orders','Review customer orders and move them through fulfillment. Demo orders do not represent real payments.');
?>
<section class="admin-panel">
    <form method="get" class="admin-filters">
        <div><label for="q">Order or customer</label><input class="form-control" id="q" name="q" value="<?= e($q) ?>">
        </div>
        <div><label for="status">Status</label><select class="form-select" id="status" name="status">
                <option value="">All statuses</option>
                <?php foreach (['pending','processing','completed','cancelled'] as $choice): ?><option
                    <?= $status === $choice ? 'selected' : '' ?>><?= e($choice) ?></option><?php endforeach; ?>
            </select></div><?php if (isset($_GET['user_id'])): ?><input type="hidden" name="user_id"
            value="<?= admin_id($_GET['user_id']) ?>"><?php endif; ?><button class="btn btn-dark">Filter</button><a
            class="btn btn-outline-secondary" href="orders.php">Clear</a>
    </form>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?><tr>
                    <td><a href="order-view.php?id=<?= (int)$order['id'] ?>"><?= e($order['order_number']) ?></a></td>
                    <td><?php if ($order['user_id']): ?><a
                            href="user-view.php?id=<?= (int)$order['user_id'] ?>"><?= e($order['customer_name']) ?></a><?php else: ?><?= e($order['customer_name']) ?><?php endif; ?>
                    </td>
                    <td><?= (int)$order['item_count'] ?></td>
                    <td class="text-nowrap"><?= e(admin_money($order['total'],$order['currency'])) ?></td>
                    <td><?= admin_badge($order['payment_status']) ?></td>
                    <td><?= admin_badge($order['status']) ?></td>
                    <td><?= e($order['created_at']) ?></td>
                </tr>
                <?php endforeach; if (!$orders) admin_empty(7,'No orders found. Orders appear here when customers complete checkout.'); ?>
            </tbody>
        </table>
    </div><?php admin_pager($page,$pages,$total); ?>
</section><?php admin_end(); ?>