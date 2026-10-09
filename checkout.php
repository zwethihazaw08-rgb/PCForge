<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/orders.php';

require_login('checkout.php');
$currentUser = auth_user();
$shippingDefaults = [];
try {
    $shippingQuery = db()->prepare('SELECT name, address, address_line2, city, postal_code, phone, region, country FROM user_shipping_details WHERE user_id = :id');
    $shippingQuery->execute(['id' => $currentUser['id']]);
    $shippingDefaults = $shippingQuery->fetch() ?: [];
    if (!empty($shippingDefaults['address_line2'])) {
        $shippingDefaults['address'] .= ', ' . $shippingDefaults['address_line2'];
    }
} catch (PDOException $exception) {
    // Checkout remains available before the shipping-details migration is applied.
    error_log('PCForge checkout shipping defaults unavailable: ' . $exception->getMessage());
}


$categories = component_categories();
$confirmation = null;
$errors = [];
header('Cache-Control: no-store, private');
if (is_string($_SESSION['demo_order']['number'] ?? null)) {
    try {
        $confirmation = customer_order_receipt($_SESSION['demo_order']['number'], (int) $currentUser['id']);
        if (!$confirmation) unset($_SESSION['demo_order']);
    } catch (PDOException $exception) {
        error_log('PCForge order confirmation load failed: ' . $exception->getMessage());
        $errors[] = 'Your order confirmation could not be loaded. Please try again later.';
    }
}
$cart = is_array($_SESSION['cart'] ?? null) ? $_SESSION['cart'] : [];

function checkoutCents($price): ?int
{
    if (!is_scalar($price) || !preg_match('/^(\d+)\.(\d{2})$/', (string) $price, $matches)) return null;
    return (int) $matches[1] * 100 + (int) $matches[2];
}
function checkoutMoney(int $cents): string
{
    return money(intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT));
}

function checkoutInput(string $field, string $default = ''): string
{
    return is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : $default;
}

$items = [];
$totalCents = 0;
$cartInvalid = false;
try {
    foreach ($cart as $key => $item) {
        $itemCents = 0;
        $names = [];
        foreach ($item['parts'] as $category => $id) {
            if (!isset($categories[$category])) { $cartInvalid = true; continue 2; }
            $statement = db()->prepare("SELECT name, price, status FROM `$category` WHERE id = :id");
            $statement->execute(['id' => $id]);
            $product = $statement->fetch();
            $price = $product ? checkoutCents($product['price']) : null;
            if (!$product || $product['status'] !== 'active' || $price === null) { $cartInvalid = true; continue 2; }
            $itemCents += $price;
            $names[] = $product['name'];
        }
        $quantity = max(1, min(99, (int) ($item['quantity'] ?? 1)));
        $items[] = ['key' => $key, 'name' => $item['type'] === 'build' ? ($item['name'] ?? 'Custom PC build') : ($names[0] ?? 'Component'), 'parts' => $names, 'quantity' => $quantity, 'cents' => $itemCents];
        $totalCents += $itemCents * $quantity;
    }
} catch (PDOException $exception) {
    error_log('PCForge checkout load failed: ' . $exception->getMessage());
    $errors[] = 'The order total could not be loaded. Please try again later.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$confirmation) {
    try {
        csrf_verify();
        $required = ['name' => 'Full name', 'email' => 'Email', 'address' => 'Address', 'city' => 'City', 'postal_code' => 'Postal code'];
        $details = [];
        foreach ($required as $field => $label) {
            $value = checkoutInput($field);
            $maxLength = $field === 'address' ? 262 : ($field === 'postal_code' ? 30 : 160);
            if ($value === '' || mb_strlen($value) > $maxLength) $errors[] = $label . ' is required and must be at most ' . $maxLength . ' characters.';
            $details[$field] = $value;
        }
        foreach (['phone'=>30, 'region'=>100, 'country'=>100] as $field=>$maxLength) {
            $details[$field] = checkoutInput($field);
            if (mb_strlen($details[$field]) > $maxLength) $errors[] = ucfirst($field) . ' is too long.';
        }
        if ($details['email'] !== '' && !filter_var($details['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
        if (($_POST['payment_method'] ?? '') !== 'demo') $errors[] = 'Choose Demo Payment to continue.';
        if (!$items || $cartInvalid) $errors[] = 'Your cart contains unavailable items. Return to the cart and update it.';
        if (!$errors) {
            $_SESSION['demo_order'] = create_demo_order($cart, $details, (int)$currentUser['id']);
            $_SESSION['cart'] = [];
            redirect('checkout.php');
        }
    } catch (InvalidArgumentException $exception) {
        $errors[] = $exception->getMessage();
    } catch (Throwable $exception) {
        error_log('PCForge order creation failed: ' . $exception->getMessage());
        $errors[] = 'Your order could not be saved. Please try again.';
    }
}

$pageTitle = $confirmation ? 'Demo Order Confirmed' : 'Checkout';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content" tabindex="-1">
    <?php if ($confirmation): ?>
    <link rel="stylesheet" href="<?= e(url('assets/css/receipt.css')) ?>"><?php endif; ?>
    <div class="container section-padding">
        <?php if ($errors): ?><div class="alert alert-warning" role="alert">
            <ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
        </div><?php endif; ?>
        <?php if ($confirmation): ?>
        <div class="border rounded-4 p-4 p-md-5 text-center mx-auto" style="max-width: 700px">
            <span class="compatibility-status compatible">Demo order created</span>
            <h1 class="mt-3">Thanks, <?= e($confirmation['customer_name']) ?>.</h1>
            <p class="lead text-secondary">Your demonstration order has been saved for administrator review.</p>
            <div class="bg-light rounded-4 p-4 text-start my-4">
                <div class="d-flex justify-content-between gap-3"><span>Order
                        number</span><strong><?= e($confirmation['order_number']) ?></strong></div>
                <div class="d-flex justify-content-between gap-3 mt-2">
                    <span>Components</span><strong><?= (int) array_sum(array_column($confirmation['items'], 'quantity')) ?></strong>
                </div>
                <div class="d-flex justify-content-between gap-3 mt-2"><span>Demo
                        total</span><strong><?= e(receipt_money($confirmation['total'], $confirmation['currency'])) ?></strong>
                </div>
                <div class="d-flex justify-content-between gap-3 mt-2"><span>Status at checkout</span><strong>Pending
                        review</strong></div>
            </div>
            <p class="small text-secondary">No payment was taken. This demo order is saved in PCForge; stock is deducted
                when an administrator completes it.</p>
            <?php $receiptUrl = url('receipt.php?order=' . rawurlencode($confirmation['order_number'])); $receiptQrUrl = public_url('receipt.php?order=' . rawurlencode($confirmation['order_number'])); require __DIR__ . '/includes/receipt-qr.php'; ?>
            <a class="btn btn-primary mt-3" href="<?= e($receiptUrl) ?>">View / print receipt</a>
            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4"><a class="btn btn-primary"
                    href="<?= e(url('products.php')) ?>">Continue shopping</a><a class="btn btn-outline-dark"
                    href="<?= e(url('builder.php')) ?>">Build another PC</a></div>
        </div>
        <?php elseif (!$items || $cartInvalid): ?>
        <div class="border rounded-4 p-5 text-center">
            <h1 class="h3">Your cart needs attention</h1>
            <p class="text-secondary">Add available items to your cart before opening checkout.</p>
            <a class="btn btn-primary" href="<?= e(url('cart.php')) ?>">Return to cart</a>
        </div>
        <?php else: ?>
        <p class="small text-secondary text-uppercase fw-semibold">Demo checkout</p>
        <h1>Complete your order</h1>
        <p class="lead text-secondary">This checkout demonstrates the order flow. It does not process a real payment.
        </p>
        <form method="post" action="<?= e(url('checkout.php')) ?>" class="row g-4">
            <section class="col-lg-7" aria-labelledby="details-heading">
                <div class="border rounded-4 p-4">
                    <h2 id="details-heading" class="h4">Customer and delivery details</h2>
                    <?= csrf_field() ?>
                    <div class="row g-3 mt-1">
                        <div class="col-12"><label class="form-label" for="name">Full name</label><input
                                class="form-control" id="name" name="name" maxlength="160" required
                                value="<?= e(checkoutInput('name', (string)($shippingDefaults['name'] ?? $currentUser['username'] ?? ''))) ?>">
                        </div>
                        <div class="col-12"><label class="form-label" for="email">Email</label><input
                                class="form-control" type="email" id="email" name="email" maxlength="160" required
                                value="<?= e(checkoutInput('email', (string)($currentUser['email'] ?? ''))) ?>"></div>
                        <div class="col-12"><label class="form-label" for="address">Address</label><input
                                class="form-control" id="address" name="address" maxlength="262" required
                                value="<?= e(checkoutInput('address', (string)($shippingDefaults['address'] ?? ''))) ?>">
                        </div>
                        <div class="col-md-8"><label class="form-label" for="city">City</label><input
                                class="form-control" id="city" name="city" maxlength="160" required
                                value="<?= e(checkoutInput('city', (string)($shippingDefaults['city'] ?? ''))) ?>">
                        </div>
                        <div class="col-md-4"><label class="form-label" for="postal_code">Postal code</label><input
                                class="form-control" id="postal_code" name="postal_code" maxlength="30" required
                                value="<?= e(checkoutInput('postal_code', (string)($shippingDefaults['postal_code'] ?? ''))) ?>">
                        </div>
                        <?php foreach (['phone'=>'Phone (optional)','region'=>'Region (optional)','country'=>'Country (optional)'] as $field=>$label): ?>
                        <div class="col-md-4">
                            <label class="form-label" for="<?= e($field) ?>"><?= e($label) ?></label>
                            <input class="form-control" id="<?= e($field) ?>" name="<?= e($field) ?>"
                                maxlength="<?= $field === 'phone' ? 30 : 100 ?>"
                                value="<?= e(checkoutInput($field, (string)($shippingDefaults[$field] ?? ''))) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <h2 class="h4 mt-5">Payment method</h2>
                    <div class="border rounded-3 p-3">
                        <div class="form-check"><input class="form-check-input" type="radio" name="payment_method"
                                id="demo-payment" value="demo" checked><label class="form-check-label"
                                for="demo-payment"><strong>Demo Payment</strong><br><small class="text-secondary">No
                                    payment service is connected. This only demonstrates checkout.</small></label></div>
                    </div>
                </div>
            </section>
            <aside class="col-lg-5" aria-labelledby="order-summary-heading">
                <div class="build-summary">
                    <h2 id="order-summary-heading" class="h4">Order summary</h2>
                    <?php foreach ($items as $item): ?><div
                        class="d-flex justify-content-between gap-3 py-2 border-bottom">
                        <span><?= e($item['name']) ?><small class="d-block text-secondary">Qty
                                <?= (int) $item['quantity'] ?></small></span><strong><?= e(checkoutMoney($item['cents'] * $item['quantity'])) ?></strong>
                    </div><?php endforeach; ?>
                    <div class="d-flex justify-content-between gap-3 pt-3"><span>Demo
                            total</span><strong><?= e(checkoutMoney($totalCents)) ?></strong></div>
                    <button class="btn btn-primary w-100 mt-4" type="submit">Place demo order</button>
                    <a class="btn btn-outline-dark w-100 mt-2" href="<?= e(url('cart.php')) ?>">Back to cart</a>
                </div>
            </aside>
        </form>
        <?php endif; ?>
    </div>
</main>
<?php if ($confirmation): ?>
<script src="<?= e(url('assets/js/vendor/qrcodegen-v1.8.0.js')) ?>" defer></script>
<script src="<?= e(url('assets/js/receipt.js')) ?>" defer></script>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>