<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/mail.php';
require_login('profile.php');
$profileUser = auth_user();
$errors = [];
$notice = $_SESSION['profile_notice'] ?? '';
unset($_SESSION['profile_notice']);
$account = ['username' => $profileUser['username'], 'email' => $profileUser['email']];
$shippingFields = [
    'name' => ['Full name', 100, 'name', true],
    'phone' => ['Phone number', 30, 'tel', true],
    'address' => ['Street address', 160, 'address-line1', true],
    'address_line2' => ['Apartment, suite, etc. (optional)', 100, 'address-line2', false],
    'city' => ['City / township', 100, 'address-level2', true],
    'region' => ['State / region (optional)', 100, 'address-level1', false],
    'postal_code' => ['Postal code (optional)', 30, 'postal-code', false],
    'country' => ['Country', 100, 'country-name', true],
];
$shipping = array_fill_keys(array_keys($shippingFields), '');
$shippingAvailable = true;
try {
    $query = db()->prepare('SELECT * FROM user_shipping_details WHERE user_id = :id');
    $query->execute(['id' => $profileUser['id']]);
    $shipping = array_intersect_key($query->fetch() ?: $shipping, $shipping);
} catch (PDOException $exception) {
    error_log('PCForge shipping load failed: ' . $exception->getMessage());
    $shippingAvailable = false;
}
function profileInput(string $key): string
{
    return is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : '';
}
function profileSaved(string $message): void
{
    $_SESSION['profile_notice'] = $message;
    redirect('profile.php');
}
$pending = $_SESSION['profile_email_change'] ?? null;
if ($pending && ($pending['user_id'] !== (int) $profileUser['id'] || $pending['expires_at'] < time())) {
    unset($_SESSION['profile_email_change']);
    $pending = null;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $action = profileInput('action');
    try {
        if ($action === 'shipping') {
            foreach ($shippingFields as $field => [$label, $limit, $autocomplete, $required]) {
                $shipping[$field] = profileInput($field);
                if (($required && $shipping[$field] === '') || mb_strlen($shipping[$field]) > $limit) {
                    $errors[] = $label . ($required ? ' is required and' : '') . ' must be at most ' . $limit . ' characters.';
                }
            }
            if ($shipping['phone'] !== '' && !preg_match('/^[+0-9().\s-]{5,30}$/', $shipping['phone'])) $errors[] = 'Enter a valid phone number.';
            if (!$shippingAvailable) $errors[] = 'Shipping settings are temporarily unavailable. Please try again later.';
            if (!$errors) {
                $query = db()->prepare('INSERT INTO user_shipping_details (user_id, name, phone, address, address_line2, city, region, postal_code, country) VALUES (:user_id, :name, :phone, :address, :address_line2, :city, :region, :postal_code, :country) ON DUPLICATE KEY UPDATE name = VALUES(name), phone = VALUES(phone), address = VALUES(address), address_line2 = VALUES(address_line2), city = VALUES(city), region = VALUES(region), postal_code = VALUES(postal_code), country = VALUES(country)');
                $query->execute(['user_id' => $profileUser['id']] + $shipping);
                profileSaved('Your shipping details have been saved.');
            }
        } elseif ($action === 'account') {
            $account = ['username' => profileInput('username'), 'email' => strtolower(profileInput('email'))];
            if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $account['username'])) $errors[] = 'Username must be 3–50 characters using letters, numbers, or underscores.';
            if (!filter_var($account['email'], FILTER_VALIDATE_EMAIL) || strlen($account['email']) > 100) $errors[] = 'Enter a valid email address of at most 100 characters.';
            $emailChanged = strcasecmp($account['email'], $profileUser['email']) !== 0;
            if ($emailChanged) {
                $query = db()->prepare('SELECT password FROM users WHERE id = :id');
                $query->execute(['id' => $profileUser['id']]);
                if (!password_verify(is_string($_POST['current_password'] ?? null) ? $_POST['current_password'] : '', $query->fetchColumn())) $errors[] = 'Enter your current password to change your email address.';
            }
            if (!$errors) {
                $query = db()->prepare('SELECT id FROM users WHERE (username = :username OR email = :email) AND id <> :id');
                $query->execute($account + ['id' => $profileUser['id']]);
                if ($query->fetch()) $errors[] = 'That username or email is already in use.';
            }
            if (!$errors && $emailChanged) {
                if ($pending && $pending['sent_at'] > time() - 60) {
                    $errors[] = 'Please wait a minute before requesting another verification code.';
                } else {
                    $otp = (string) random_int(100000, 999999);
                    if (send_registration_otp($account['email'], $otp, true)) {
                        $settings = pcforge_mail_settings();
                        $_SESSION['profile_email_change'] = $account + ['user_id' => (int) $profileUser['id'], 'original_email' => $profileUser['email'], 'hash' => password_hash($otp, PASSWORD_DEFAULT), 'attempts' => 0, 'sent_at' => time(), 'expires_at' => time() + (int) $settings['otp_ttl_seconds']];
                        profileSaved('A verification code was sent to your new email. Verify it below to save your account changes.');
                    } else {
                        $errors[] = 'The verification email could not be sent. Your account details have not changed.';
                    }
                }
            } elseif (!$errors) {
                $query = db()->prepare('UPDATE users SET username = :username WHERE id = :id');
                $query->execute(['username' => $account['username'], 'id' => $profileUser['id']]);
                unset($_SESSION['profile_email_change']);
                profileSaved('Your account details have been updated.');
            }
        } elseif ($action === 'verify_email') {
            if (!$pending || $pending['attempts'] >= pcforge_mail_settings()['otp_max_attempts']) {
                unset($_SESSION['profile_email_change']);
                $pending = null;
                $errors[] = 'Verification expired or the attempt limit was reached. Save your account details again to request a new code.';
            } else {
                $_SESSION['profile_email_change']['attempts']++;
                if (!preg_match('/^\d{6}$/', profileInput('otp')) || !password_verify(profileInput('otp'), $pending['hash'])) {
                    $errors[] = 'That verification code is incorrect.';
                } else {
                    $query = db()->prepare('UPDATE users SET username = :username, email = :email WHERE id = :id AND email = :original_email');
                    $query->execute(['username' => $pending['username'], 'email' => $pending['email'], 'id' => $profileUser['id'], 'original_email' => $pending['original_email']]);
                    unset($_SESSION['profile_email_change']);
                    profileSaved($query->rowCount() ? 'Your account details have been updated.' : 'Your account changed during verification. Please review your details and try again.');
                }
            }
        } elseif ($action === 'cancel_email') {
            unset($_SESSION['profile_email_change']);
            profileSaved('The pending email change was cancelled.');
        } elseif ($action === 'password') {
            $password = is_string($_POST['new_password'] ?? null) ? $_POST['new_password'] : '';
            $current = is_string($_POST['password_current'] ?? null) ? $_POST['password_current'] : '';
            $query = db()->prepare('SELECT password FROM users WHERE id = :id');
            $query->execute(['id' => $profileUser['id']]);
            if (!password_verify($current, $query->fetchColumn())) $errors[] = 'Your current password is incorrect.';
            if (strlen($password) < 8 || strlen($password) > 72) $errors[] = 'Use a new password between 8 and 72 bytes long.';
            if ($password !== ($_POST['password_confirmation'] ?? null)) $errors[] = 'The new passwords do not match.';
            if (!$errors) {
                $query = db()->prepare('UPDATE users SET password = :password WHERE id = :id');
                $query->execute(['password' => password_hash($password, PASSWORD_DEFAULT), 'id' => $profileUser['id']]);
                unset($_SESSION['profile_email_change']);
                session_regenerate_id(true);
                profileSaved('Your password has been changed.');
            }
        } else {
            $errors[] = 'Choose a valid settings action.';
        }
    } catch (PDOException $exception) {
        error_log('PCForge profile update failed: ' . $exception->getMessage());
        $errors[] = $exception->getCode() === '23000' ? 'That username or email is already in use.' : 'Your changes could not be saved. Please try again later.';
    } catch (Throwable $exception) {
        error_log('PCForge profile update failed: ' . $exception->getMessage());
        $errors[] = 'Your changes could not be saved. Please try again later.';
    }
}
$pageTitle = 'Account Settings';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main id="main-content" tabindex="-1">
    <style>
        .settings-page { max-width: 1160px; }
        .settings-heading { margin-bottom: 2.5rem; }
        .settings-heading h1 { font-size: clamp(2rem, 4vw, 3rem); letter-spacing: -0.04em; }
        .settings-layout { display: grid; grid-template-columns: 250px minmax(0, 1fr); gap: 2rem; align-items: start; }
        .settings-sidebar { position: sticky; top: 6rem; border: 1px solid var(--forge-border); border-radius: 1.25rem; padding: 1.5rem; background: var(--forge-surface); overflow-wrap: anywhere; }
        .settings-avatar { display: grid; place-items: center; width: 56px; height: 56px; border-radius: 18px; background: var(--bs-body-color); color: var(--bs-body-bg); font-size: 1.4rem; font-weight: 700; margin-bottom: 1rem; }
        .settings-nav { display: grid; gap: 0.35rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--forge-border); }
        .settings-nav a { padding: 0.7rem 0.8rem; border-radius: 0.6rem; text-decoration: none; color: var(--bs-body-color); font-size: 0.9rem; }
        .settings-nav a:hover, .settings-nav a:focus-visible { background: var(--forge-surface-raised); }
        .settings-panels { display: grid; gap: 1.5rem; min-width: 0; }
        .settings-panel { border: 1px solid var(--forge-border); border-radius: 1.25rem; background: var(--forge-surface-raised); padding: clamp(1.25rem, 3vw, 2rem); scroll-margin-top: 6rem; }
        .settings-panel h2 { font-size: 1.35rem; letter-spacing: -0.02em; }
        .settings-panel .form-control { min-height: 46px; border-radius: 0.6rem; }
        .settings-panel .form-label { font-size: 0.88rem; font-weight: 600; }
        .settings-footer { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--forge-border); }
        .settings-footer p { margin: 0; font-size: 0.8rem; color: var(--forge-muted); }
        @media (max-width: 767.98px) { .settings-layout { grid-template-columns: minmax(0, 1fr); } .settings-sidebar { position: static; } .settings-nav { display: flex; flex-wrap: wrap; } }
        @media (max-width: 479.98px) { .settings-footer .btn { width: 100%; } }
    </style>
    <div class="container section-padding settings-page">
        <header class="settings-heading">
            <p class="small text-secondary text-uppercase fw-semibold mb-2">Your PCForge account</p>
            <h1>Make yourself at home.</h1>
            <p class="text-secondary mb-0">Manage your personal details, delivery address, and account security.</p>
        </header>
        <?php if ($notice): ?><div class="alert alert-success" role="status"><?= e($notice) ?></div><?php endif; ?>
        <?php if ($errors): ?><div class="alert alert-warning" role="alert"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
        <div class="settings-layout">
            <aside class="settings-sidebar" aria-label="Profile navigation">
                <div class="settings-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($profileUser['username'], 0, 1))) ?></div>
                <p class="fw-semibold mb-1"><?= e($profileUser['username']) ?></p>
                <p class="small text-secondary mb-0"><?= e($profileUser['email']) ?></p>
                <nav class="settings-nav" aria-label="Account settings">
                    <a href="#account">Account details &rarr;</a>
                    <a href="#shipping">Shipping details &rarr;</a>
                    <a href="#security">Password &amp; security &rarr;</a>
                    <a href="<?= e(url('saved-builds.php')) ?>">Saved builds &nearr;</a>
                </nav>
            </aside>
            <div class="settings-panels">
                <section class="settings-panel" id="account" aria-labelledby="account-heading">
                    <h2 id="account-heading">Account details</h2>
                    <p class="text-secondary small mb-4">Keep your username and sign-in email up to date.</p>
                    <form method="post" action="<?= e(url('profile.php#account')) ?>">
                        <?= csrf_field() ?><input type="hidden" name="action" value="account">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label" for="username">Username</label><input class="form-control" id="username" name="username" autocomplete="username" minlength="3" maxlength="50" pattern="[A-Za-z0-9_]{3,50}" required value="<?= e($account['username']) ?>" aria-describedby="username-help"><div id="username-help" class="form-text">Letters, numbers, and underscores.</div></div>
                            <div class="col-md-6"><label class="form-label" for="email">Email address</label><input class="form-control" type="email" id="email" name="email" autocomplete="email" maxlength="100" required value="<?= e($account['email']) ?>"></div>
                            <div class="col-12"><label class="form-label" for="current-password">Current password <span class="fw-normal text-secondary">(only for email changes)</span></label><input class="form-control" type="password" id="current-password" name="current_password" autocomplete="current-password" aria-describedby="email-help"><div class="form-text" id="email-help">Changing your email also requires a code sent to the new address. If you joined with Google, keep using your Google sign-in email.</div></div>
                        </div>
                        <div class="settings-footer"><p>Your email is used to sign in.</p><button class="btn btn-primary" type="submit">Save account details</button></div>
                    </form>
                    <?php if ($pending): ?>
                        <div class="border rounded-3 p-3 mt-4">
                            <h3 class="h6">Verify your new email</h3><p class="small text-secondary">Enter the code sent to <?= e($pending['email']) ?>. Your current email stays active until verification.</p>
                            <form method="post" action="<?= e(url('profile.php#account')) ?>" class="d-flex flex-wrap gap-2">
                                <?= csrf_field() ?><input type="hidden" name="action" value="verify_email">
                                <label class="visually-hidden" for="email-otp">Six-digit verification code</label><input class="form-control" style="max-width: 190px" id="email-otp" name="otp" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required><button class="btn btn-primary" type="submit">Verify &amp; save</button>
                            </form>
                            <form method="post" action="<?= e(url('profile.php#account')) ?>" class="mt-2"><?= csrf_field() ?><input type="hidden" name="action" value="cancel_email"><button class="btn btn-link p-0 small" type="submit">Cancel email change</button></form>
                        </div>
                    <?php endif; ?>
                </section>
                <section class="settings-panel" id="shipping" aria-labelledby="shipping-heading">
                    <h2 id="shipping-heading">Shipping details</h2>
                    <p class="text-secondary small mb-4">Save the contact and address details for your deliveries.</p>
                    <?php if (!$shippingAvailable): ?><p class="alert alert-warning" role="status">Shipping settings are temporarily unavailable. Please try again later.</p><?php endif; ?>
                    <form method="post" action="<?= e(url('profile.php#shipping')) ?>">
                        <?= csrf_field() ?><input type="hidden" name="action" value="shipping">
                        <fieldset <?= !$shippingAvailable ? 'disabled' : '' ?>>
                            <legend class="visually-hidden">Delivery address</legend>
                            <div class="row g-3">
                                <?php foreach ($shippingFields as $field => [$label, $limit, $autocomplete, $required]): ?>
                                    <div class="<?= in_array($field, ['address', 'address_line2'], true) ? 'col-12' : 'col-md-6' ?>"><label class="form-label" for="shipping-<?= e($field) ?>"><?= e($label) ?></label><input class="form-control" type="<?= $field === 'phone' ? 'tel' : 'text' ?>" id="shipping-<?= e($field) ?>" name="<?= e($field) ?>" autocomplete="shipping <?= e($autocomplete) ?>" maxlength="<?= $limit ?>" <?= $required ? 'required' : '' ?> value="<?= e($shipping[$field]) ?>"></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="settings-footer"><p>You can update this address anytime.</p><button class="btn btn-primary" type="submit">Save shipping details</button></div>
                        </fieldset>
                    </form>
                </section>
                <section class="settings-panel" id="security" aria-labelledby="security-heading">
                    <h2 id="security-heading">Password &amp; security</h2>
                    <p class="text-secondary small mb-4">Choose a strong password you do not use elsewhere.</p>
                    <form method="post" action="<?= e(url('profile.php#security')) ?>">
                        <?= csrf_field() ?><input type="hidden" name="action" value="password">
                        <div class="mb-3"><label class="form-label" for="password-current">Current password</label><input class="form-control" type="password" id="password-current" name="password_current" autocomplete="current-password" required></div>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label" for="new-password">New password</label><input class="form-control" type="password" id="new-password" name="new_password" autocomplete="new-password" minlength="8" maxlength="72" required aria-describedby="password-help"><div class="form-text" id="password-help">Use at least 8 characters.</div></div>
                            <div class="col-md-6"><label class="form-label" for="password-confirmation">Confirm new password</label><input class="form-control" type="password" id="password-confirmation" name="password_confirmation" autocomplete="new-password" minlength="8" maxlength="72" required></div>
                        </div>
                        <div class="settings-footer"><p>Joined with Google? Manage your password in your Google account.</p><button class="btn btn-primary" type="submit">Update password</button></div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
