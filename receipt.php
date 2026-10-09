<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/orders.php';

$number = is_string($_GET['order'] ?? null) ? $_GET['order'] : '';
require_login('receipt.php?order=' . rawurlencode($number));
header('Cache-Control: no-store, private');
header('Referrer-Policy: no-referrer');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
$order = null;
$error = '';
try {
    $order = customer_order_receipt($number, (int) auth_user()['id']);
    if (!$order) {
        http_response_code(404);
        $error = 'This receipt is unavailable for your account.';
    }
} catch (PDOException $exception) {
    error_log('PCForge receipt load failed: ' . $exception->getMessage());
    http_response_code(503);
    $error = 'Your receipt could not be loaded. Please try again later.';
}
$pageTitle = 'Demo Order Receipt';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<main id="main-content" class="receipt-page" tabindex="-1">
    <link rel="stylesheet" href="<?= e(url('assets/css/receipt.css')) ?>">
    <div class="container section-padding">
        <?php if (!$order): ?>
        <div class="border rounded-4 p-4">
            <h1 class="h3">Receipt unavailable</h1>
            <p><?= e($error) ?></p><a class="btn btn-outline-dark" href="<?= e(url('products.php')) ?>">Continue
                shopping</a>
        </div>
        <?php else: ?>
        <div class="receipt-actions mb-4 d-flex flex-wrap gap-2 justify-content-between">
            <a class="btn btn-outline-dark" href="<?= e(url('products.php')) ?>">Continue shopping</a>
            <button class="btn btn-primary" type="button" data-print-receipt hidden>Print / save PDF</button>
        </div>
        <article class="receipt-card">
            <header class="receipt-heading">
                <div>
                    <p class="receipt-eyebrow">PCForge / Order receipt</p>
                    <h1>Thanks for your order.</h1>
                    <p class="mb-0">Demo payment · No money was charged.</p>
                </div>
                <div class="receipt-reference"><strong><?= e($order['order_number']) ?></strong><span>Placed
                        <?= e($order['created_at']) ?></span><span>Order status:
                        <?= e(ucfirst($order['status'])) ?></span></div>
            </header>
            <div class="receipt-customer">
                <section>
                    <h2 class="h6">Customer</h2>
                    <p><?= e($order['customer_name']) ?><br><?= e($order['customer_email']) ?></p>
                </section>
                <section>
                    <h2 class="h6">Delivery details</h2>
                    <p><?= e($order['shipping_address']) ?><br><?= e($order['shipping_city'] . ' ' . $order['shipping_postal_code']) ?><br><?= e(trim($order['shipping_region'] . ' ' . $order['shipping_country'])) ?><?php if ($order['shipping_phone'] !== ''): ?><br><?= e($order['shipping_phone']) ?><?php endif; ?>
                    </p>
                </section>
            </div>
            <div class="receipt-table-wrap">
                <table class="receipt-table">
                    <caption class="visually-hidden">Purchased components and prices at checkout</caption>
                    <thead>
                        <tr>
                            <th scope="col">Component</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Unit price</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody><?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><strong><?= e($item['product_name']) ?></strong><small><?= e(component_categories()[$item['category']] ?? 'Component') ?></small>
                            </td>
                            <td><?= (int) $item['quantity'] ?></td>
                            <td><?= e(receipt_money($item['unit_price'], $order['currency'])) ?></td>
                            <td><?= e(receipt_money($item['line_total'], $order['currency'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="receipt-bottom">
                <?php $receiptUrl = url('receipt.php?order=' . rawurlencode($order['order_number'])); $receiptQrUrl = public_url('receipt.php?order=' . rawurlencode($order['order_number'])); require __DIR__ . '/includes/receipt-qr.php'; ?>
                <div>
                    <dl class="receipt-totals">
                        <div>
                            <dt>Subtotal</dt>
                            <dd><?= e(receipt_money($order['subtotal'], $order['currency'])) ?></dd>
                        </div>
                        <div>
                            <dt>Delivery</dt>
                            <dd><?= e(receipt_money($order['shipping_total'], $order['currency'])) ?></dd>
                        </div>
                        <div class="receipt-grand-total">
                            <dt>Demo total</dt>
                            <dd><?= e(receipt_money($order['total'], $order['currency'])) ?></dd>
                        </div>
                        <div>
                            <dt>Amount charged</dt>
                            <dd><?= e(receipt_money('0.00', $order['currency'])) ?></dd>
                        </div>
                    </dl>
                    <p class="small text-secondary">This is a demonstration order receipt. Prices are saved at checkout.
                        Delivery is not charged in this demo; taxes are not calculated.</p>
                </div>
            </div>
        </article>
        <?php endif; ?>
    </div>
</main>
<?php if ($order): ?>
<script src="<?= e(url('assets/js/vendor/qrcodegen-v1.8.0.js')) ?>" defer></script>
<script src="<?= e(url('assets/js/receipt.js')) ?>" defer></script>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>