<?php
require __DIR__ . '/../includes/admin-functions.php';
$settings = store_settings(); $error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    try {
        $name = admin_text($_POST,'store_name'); $email = admin_text($_POST,'store_email'); $currency=admin_text($_POST,'currency');
        $threshold=filter_var($_POST['low_stock_threshold'] ?? null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>2147483647]]);
        if ($name === '' || mb_strlen($name)>100) throw new InvalidArgumentException('Store name must be 1–100 characters.');
        if ($email !== '' && (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>160)) throw new InvalidArgumentException('Enter a valid store email.');
        if (!in_array($currency,['USD','MMK','EUR','GBP','SGD','THB'],true)) throw new InvalidArgumentException('Choose a supported currency.');
        if ($threshold === false) throw new InvalidArgumentException('Low stock threshold must be a positive whole number.');
        $maintenance=admin_text($_POST,'maintenance_mode','0');
        if (!in_array($maintenance,['0','1'],true)) throw new InvalidArgumentException('Invalid maintenance mode.');
        admin_query('UPDATE store_settings SET store_name=?,store_email=?,currency=?,low_stock_threshold=?,maintenance_mode=? WHERE id=1',[$name,$email,$currency,$threshold,$maintenance]);
        flash_set('Store settings saved.'); redirect('admin/settings.php');
    } catch (Throwable $exception) { $error=admin_error($exception); $settings=$_POST; }
}
admin_start('Settings','Store identity, currency, inventory alerts, and storefront availability.'); admin_alert($error);
?>
<form method="post" class="admin-panel"
    data-confirm="Save these store-wide settings? Currency changes do not convert existing product prices.">
    <?= csrf_field() ?><div class="row g-4">
        <div class="col-md-6"><label class="form-label" for="store_name">Store name</label><input class="form-control"
                name="store_name" id="store_name" required maxlength="100"
                value="<?= e(admin_text($settings,'store_name')) ?>"></div>
        <div class="col-md-6"><label class="form-label" for="store_email">Store email</label><input class="form-control"
                name="store_email" id="store_email" type="email" maxlength="160"
                value="<?= e(admin_text($settings,'store_email')) ?>"><small class="text-muted">Displayed as the contact
                address in the storefront footer.</small></div>
        <div class="col-md-6"><label class="form-label" for="currency">Currency</label><select class="form-select"
                name="currency" id="currency"><?php foreach (['USD','MMK','EUR','GBP','SGD','THB'] as $currency): ?>
                <option <?= admin_text($settings,'currency')===$currency ? 'selected' : '' ?>><?= e($currency) ?>
                </option><?php endforeach; ?>
            </select><small class="text-muted">Applies to catalog display and new
                orders. This does not convert prices. Existing orders retain their currency.</small></div>
        <div class="col-md-6"><label class="form-label" for="low_stock_threshold">Low stock threshold</label><input
                class="form-control" name="low_stock_threshold" id="low_stock_threshold" type="number" min="1"
                max="2147483647" required value="<?= e((string)($settings['low_stock_threshold'] ?? 5)) ?>"></div>
        <div class="col-12"><label class="form-label" for="maintenance_mode">Storefront availability</label><select
                class="form-select" name="maintenance_mode" id="maintenance_mode">
                <option value="0">Open</option>
                <option value="1" <?= (string)($settings['maintenance_mode'] ?? 0)==='1' ? 'selected' : '' ?>>
                    Maintenance mode</option>
            </select><small class="text-muted">Maintenance blocks customer storefront pages. Login and administrator
                access remain available.</small></div>
    </div><button class="btn btn-dark mt-4">Save settings</button></form><?php admin_end(); ?>