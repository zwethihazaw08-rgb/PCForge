<?php

require_once __DIR__ . '/includes/functions.php';

$categories = ['cpu' => 'Processor', 'mb' => 'Motherboard', 'memory' => 'Memory', 'gpu' => 'Graphics Card', 'storage' => 'Storage', 'cooling' => 'Cooling', 'psu' => 'Power Supply', 'case_box' => 'Case', 'fans' => 'Fans', 'monitor' => 'Monitor'];
if (!is_array($_SESSION['cart'] ?? null)) $_SESSION['cart'] = [];
$error = '';
$notice = $_SESSION['cart_notice'] ?? '';
unset($_SESSION['cart_notice']);

function cartProduct(string $category, int $id): ?array
{
    global $categories;
    if (!isset($categories[$category]) || $id < 1) return null;
    $statement = db()->prepare("SELECT id, name, price, stock FROM `$category` WHERE id = :id AND status = 'active'");
    $statement->execute(['id' => $id]);
    return $statement->fetch() ?: null;
}

function cartCents($price): ?int
{
    if (!is_scalar($price) || !preg_match('/^(\d+)\.(\d{2})$/', (string) $price, $matches)) return null;
    return (int) $matches[1] * 100 + (int) $matches[2];
}

function cartMoney(int $cents): string
{
    return money(intdiv($cents, 100) . '.' . str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT));
}

try {
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        csrf_verify();
        $action = is_string($_POST['action'] ?? null) ? $_POST['action'] : '';
        $key = is_string($_POST['item'] ?? null) ? $_POST['item'] : '';
        if ($action === 'remove' && isset($_SESSION['cart'][$key])) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart_notice'] = 'Item removed.';
            redirect('cart.php');
        } elseif ($action === 'update' && isset($_SESSION['cart'][$key])) {
            $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);
            if ($quantity === false) throw new InvalidArgumentException('Choose a quantity from 1 to 99.');
            $_SESSION['cart'][$key]['quantity'] = $quantity;
            $_SESSION['cart_notice'] = 'Quantity updated.';
            redirect('cart.php');
        } elseif (in_array($action, ['add_product', 'add_build'], true)) {
            $parts = [];
            if ($action === 'add_product') {
                $category = is_string($_POST['category'] ?? null) ? $_POST['category'] : '';
                $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                if (!isset($categories[$category]) || $id === false) throw new InvalidArgumentException('Choose a valid component.');
                $parts[$category] = $id;
            } else {
                $build = is_array($_SESSION['build'] ?? null) ? $_SESSION['build'] : [];
                foreach (array_diff(array_keys($categories), ['fans', 'monitor']) as $category) {
                    $id = filter_var($build[$category] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                    if ($id === false) throw new InvalidArgumentException('Complete all eight builder steps before adding the build.');
                    $parts[$category] = $id;
                }
            }
            foreach ($parts as $category => $id) {
                $product = cartProduct($category, $id);
                if (!$product || cartCents($product['price']) === null) throw new InvalidArgumentException('A selected part is unavailable or has no price. Please choose a replacement.');
            }
            // Build entries keep a snapshot of IDs; later builder edits do not change the cart.
            ksort($parts);
            $key = hash('sha256', $action . json_encode($parts));
            $quantity = ($_SESSION['cart'][$key]['quantity'] ?? 0) + 1;
            if ($quantity > 99) throw new InvalidArgumentException('The maximum quantity per item is 99.');
            $_SESSION['cart'][$key] = ['type' => $action === 'add_build' ? 'build' : 'product', 'parts' => $parts, 'quantity' => $quantity];
            unset($_SESSION['demo_order']);
            $_SESSION['cart_notice'] = 'Added to your cart.';
            redirect('cart.php');
        } else {
            throw new InvalidArgumentException('That cart action is no longer available. Please try again.');
        }
    }
} catch (InvalidArgumentException $exception) {
    $error = $exception->getMessage();
} catch (PDOException $exception) {
    error_log('PCForge cart update failed: ' . $exception->getMessage());
    $error = 'The cart could not be updated. Please try again later.';
}

$rows = [];
$totalCents = 0;
$quantityTotal = 0;
$totalIncomplete = false;
$pendingProduct = null;
$pendingCategory = is_string($_GET['category'] ?? null) ? $_GET['category'] : '';
$pendingId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$demand = [];
$stockWarnings = [];
try {
    // GET links only preview an item. Adding it requires a CSRF-protected POST.
    if (isset($_GET['id'])) {
        $pendingProduct = $pendingId !== false ? cartProduct($pendingCategory, $pendingId) : null;
        if (!$pendingProduct) $error = 'That component is unavailable.';
    }
    foreach ($_SESSION['cart'] as $key => $item) {
        $row = ['key' => $key, 'type' => $item['type'], 'quantity' => $item['quantity'], 'parts' => [], 'cents' => 0, 'unavailable' => false];
        foreach ($item['parts'] as $category => $id) {
            $product = cartProduct($category, $id);
            $price = $product ? cartCents($product['price']) : null;
            $row['parts'][] = ['category' => $category, 'id' => $id, 'name' => $product['name'] ?? 'Unavailable component'];
            if ($price === null) $row['unavailable'] = true;
            else $row['cents'] += $price;
            if ($product) {
                $stockKey = $category . ':' . $id;
                if (!isset($demand[$stockKey])) $demand[$stockKey] = ['name' => $product['name'], 'stock' => $product['stock'], 'quantity' => 0];
                $demand[$stockKey]['quantity'] += $item['quantity'];
            }
        }
        $row['name'] = $item['type'] === 'build' ? 'Custom PC build' : $row['parts'][0]['name'];
        $quantityTotal += $item['quantity'];
        if ($row['unavailable']) $totalIncomplete = true;
        else $totalCents += $row['cents'] * $item['quantity'];
        $rows[] = $row;
    }
    foreach ($demand as $part) {
        if ($part['stock'] === null) $stockWarnings[] = $part['name'] . ': availability is unconfirmed.';
        elseif ($part['quantity'] > (int) $part['stock']) $stockWarnings[] = $part['name'] . ': requested quantity exceeds current stock.';
    }
} catch (PDOException $exception) {
    error_log('PCForge cart load failed: ' . $exception->getMessage());
    $error = 'Cart prices could not be loaded. Please try again later.';
    $totalIncomplete = true;
}

$pageTitle = 'Your Cart';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main id="main-content" tabindex="-1">
    <div class="container section-padding">
        <p class="small text-secondary text-uppercase fw-semibold">Your selected hardware</p>
        <h1>Your cart</h1>
        <p class="text-secondary">Review quantities and current prices. Adding items does not reserve stock.</p>
        <?php if ($notice): ?><p class="alert alert-secondary" role="status"><?= e($notice) ?></p><?php endif; ?>
        <?php if ($error): ?><p class="alert alert-warning" role="alert"><?= e($error) ?></p><?php endif; ?>
        <?php if ($pendingProduct): ?>
            <form method="post" action="<?= e(url('cart.php')) ?>" class="border rounded-4 p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add_product">
                <input type="hidden" name="category" value="<?= e($pendingCategory) ?>">
                <input type="hidden" name="id" value="<?= (int) $pendingProduct['id'] ?>">
                <div><strong><?= e($pendingProduct['name']) ?></strong><p class="mb-0 text-secondary"><?= e(money($pendingProduct['price'])) ?></p></div>
                <button class="btn btn-primary" type="submit">Add this component</button>
            </form>
        <?php endif; ?>
        <div class="row g-4 align-items-start">
            <section class="col-lg-8" aria-label="Cart items">
                <?php if (!$rows): ?><div class="border rounded-4 p-5 text-center"><h2 class="h4">Your cart is empty</h2><p class="text-secondary">Choose a component or finish a custom build.</p><a class="btn btn-outline-dark" href="<?= e(url('products.php')) ?>">Browse components</a></div><?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <article class="border rounded-4 p-3 p-md-4 mb-3">
                        <div class="d-flex flex-wrap justify-content-between gap-3">
                            <h2 class="h5"><?= e($row['name']) ?></h2>
                            <strong><?= $row['unavailable'] ? 'Price unavailable' : e(cartMoney($row['cents'] * $row['quantity'])) ?></strong>
                        </div>
                        <?php if ($row['type'] === 'build'): ?>
                            <details class="small mb-3"><summary>View the <?= count($row['parts']) ?> included parts</summary><ul class="mt-2"><?php foreach ($row['parts'] as $part): ?><li><?= e($categories[$part['category']] . ': ' . $part['name']) ?></li><?php endforeach; ?></ul></details>
                        <?php endif; ?>
                        <p class="small text-secondary"><?= $row['unavailable'] ? 'Remove this item and choose available parts with prices.' : e(cartMoney($row['cents'])) . ' per item' ?></p>
                        <div class="d-flex flex-wrap align-items-end gap-3">
                            <form method="post" action="<?= e(url('cart.php')) ?>" class="cart-quantity-form d-flex align-items-end gap-2">
                                <?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="item" value="<?= e($row['key']) ?>">
                                <div><label class="small form-label" for="quantity-<?= e($row['key']) ?>">Quantity</label><input class="form-control" style="width: 85px" type="number" min="1" max="99" required name="quantity" id="quantity-<?= e($row['key']) ?>" value="<?= (int) $row['quantity'] ?>"></div>
                                <span class="small text-secondary cart-save-status" aria-live="polite"></span>
                            </form>
                            <form method="post" action="<?= e(url('cart.php')) ?>">
                                <?= csrf_field() ?><input type="hidden" name="action" value="remove"><input type="hidden" name="item" value="<?= e($row['key']) ?>"><button class="btn btn-outline-secondary" type="submit" aria-label="<?= e('Remove ' . $row['name']) ?>">Remove</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
            <aside class="col-lg-4" aria-labelledby="cart-total-heading">
                <div class="border rounded-4 p-4">
                    <h2 id="cart-total-heading" class="h4">Cart summary</h2>
                    <p><?= $quantityTotal ?> item(s)</p>
                    <div class="d-flex justify-content-between gap-2 border-top pt-3"><span><?= $totalIncomplete ? 'Known-price subtotal' : 'Subtotal' ?></span><strong><?= e(cartMoney($totalCents)) ?></strong></div>
                    <p class="small text-secondary mt-3">Component prices only. Delivery and any applicable taxes are not included.</p>
                    <?php foreach ($stockWarnings as $warning): ?><p class="small text-warning-emphasis"><?= e($warning) ?></p><?php endforeach; ?>
                    <?php if ($totalIncomplete): ?><p class="small">Some items could not be priced. This subtotal is incomplete.</p><?php endif; ?>
                    <?php if ($rows && !$totalIncomplete): ?>
                        <a class="btn btn-primary w-100 mt-3" href="<?= e(url('checkout.php')) ?>">Proceed to checkout</a>
                    <?php elseif ($rows): ?>
                        <p class="small text-warning-emphasis mt-3 mb-0">Remove unavailable items before checkout.</p>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['build'])): ?>
                        <form method="post" action="<?= e(url('cart.php')) ?>" class="mt-2">
                            <?= csrf_field() ?><input type="hidden" name="action" value="add_build"><button class="btn btn-primary w-100" type="submit">Add current PC build</button>
                        </form>
                        <a class="d-block small mt-2" href="<?= e(url('build-summary.php')) ?>">Review build compatibility</a>
                    <?php endif; ?>
                    <a class="btn btn-outline-dark w-100 mt-3" href="<?= e(url('products.php')) ?>">Continue shopping</a>
                </div>
            </aside>
        </div>
    </div>
</main>
<script>
    document.querySelectorAll('.cart-quantity-form').forEach((form) => {
        const input = form.querySelector('input[name="quantity"]');
        const status = form.querySelector('.cart-save-status');
        let timer;
        let saving = false;
        const saveQuantity = () => {
            if (!input.checkValidity() || saving) return;
            clearTimeout(timer);
            timer = window.setTimeout(() => {
                saving = true;
                status.textContent = 'Saving…';
                // Read-only keeps the quantity in the submitted form payload.
                input.readOnly = true;
                form.requestSubmit();
            }, 250);
        };
        input.addEventListener('change', saveQuantity);
        input.addEventListener('blur', saveQuantity);
    });
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
