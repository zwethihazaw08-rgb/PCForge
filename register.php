<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/oauth.php';
require_once __DIR__ . '/config/mail.php';

$mailSettings = pcforge_mail_settings();
$target = auth_redirect_target('index.php');
if (auth_user()) {
    redirect($target);
}

if (isset($_GET['reset'])) {
    unset($_SESSION['pending_registration']);
}

$pending = $_SESSION['pending_registration'] ?? null;
if (is_array($pending) && (int) ($pending['expires_at'] ?? 0) < time()) {
    unset($_SESSION['pending_registration']);
    $pending = null;
}
if (is_array($pending) && !isset($_GET['redirect']) && !isset($_POST['redirect'])) {
    $target = auth_redirect_target((string) ($pending['redirect'] ?? 'index.php'));
}

$errors = [];
$notice = '';
$username = is_array($pending) ? (string) ($pending['username'] ?? '') : trim((string) ($_POST['username'] ?? ''));
$email = is_array($pending) ? (string) ($pending['email'] ?? '') : strtolower(trim((string) ($_POST['email'] ?? '')));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_verify();
        $action = (string) ($_POST['action'] ?? 'register');
        $target = auth_redirect_target($target);

        if ($action === 'verify_otp') {
            if (!is_array($pending)) {
                $errors[] = 'Your verification session has expired. Please register again.';
            } elseif ((int) ($pending['attempts'] ?? 0) >= (int) $mailSettings['otp_max_attempts']) {
                unset($_SESSION['pending_registration']);
                $pending = null;
                $errors[] = 'Too many incorrect codes. Please register again.';
            } else {
                $otp = preg_replace('/\D+/', '', (string) ($_POST['otp'] ?? ''));
                $pending['attempts'] = (int) ($pending['attempts'] ?? 0) + 1;
                $_SESSION['pending_registration'] = $pending;
                if (strlen($otp) !== 6 || !password_verify($otp, (string) ($pending['otp_hash'] ?? ''))) {
                    $errors[] = 'That verification code is incorrect.';
                } else {
                    $statement = db()->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
                    $statement->execute([
                        'username' => $pending['username'],
                        'email' => $pending['email'],
                        'password' => $pending['password_hash'],
                    ]);
                    unset($_SESSION['pending_registration']);
                    login_user(['id' => db()->lastInsertId()]);
                    redirect($target);
                }
            }
        } elseif ($action === 'resend_otp') {
            if (!is_array($pending)) {
                $errors[] = 'Your verification session has expired. Please register again.';
            } else {
                $otp = (string) random_int(100000, 999999);
                if (send_registration_otp($pending['email'], $otp)) {
                    $pending['otp_hash'] = password_hash($otp, PASSWORD_DEFAULT);
                    $pending['expires_at'] = time() + (int) $mailSettings['otp_ttl_seconds'];
                    $pending['attempts'] = 0;
                    $_SESSION['pending_registration'] = $pending;
                    $notice = 'A new verification code was sent.';
                } else {
                    $errors[] = 'The verification email could not be sent. Configure PHP mail/SMTP and try again.';
                }
            }
        } else {
            $password = (string) ($_POST['password'] ?? '');
            $confirmation = (string) ($_POST['password_confirmation'] ?? '');

            if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
                $errors[] = 'Username must be 3–50 characters using letters, numbers, or underscores.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
                $errors[] = 'Enter a valid email address.';
            }
            if (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters.';
            }
            if ($password !== $confirmation) {
                $errors[] = 'Passwords do not match.';
            }

            if (!$errors) {
                $existing = db()->prepare('SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1');
                $existing->execute(['username' => $username, 'email' => $email]);
                if ($existing->fetch()) {
                    $errors[] = 'That username or email is already registered.';
                }
            }

            if (!$errors) {
                $otp = (string) random_int(100000, 999999);
                if (!send_registration_otp($email, $otp)) {
                    $errors[] = 'The verification email could not be sent. Configure PHP mail/SMTP and try again.';
                } else {
                    $_SESSION['pending_registration'] = [
                        'username' => $username,
                        'email' => $email,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
                        'expires_at' => time() + (int) $mailSettings['otp_ttl_seconds'],
                        'attempts' => 0,
                        'redirect' => $target,
                    ];
                    $pending = $_SESSION['pending_registration'];
                    $notice = 'We sent a six-digit verification code to your email.';
                }
            }
        }
    } catch (PDOException $exception) {
        error_log('PCForge registration failed: ' . $exception->getMessage());
        $errors[] = 'Registration could not be completed. Please try again.';
    } catch (Throwable $exception) {
        error_log('PCForge registration email failed: ' . $exception->getMessage());
        $errors[] = 'Registration could not be completed. Please try again.';
    }
}

$verifyMode = is_array($pending);
$pageTitle = $verifyMode ? 'Verify Your Email' : 'Create Account';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<link rel="stylesheet"
    href="<?= e(url('assets/css/auth.css?v=' . (string) filemtime(__DIR__ . '/assets/css/auth.css'))) ?>">

<main id="main-content" class="auth-page" tabindex="-1">
    <div class="auth-container">
        <div class="auth-heading">
            <p class="auth-eyebrow small text-secondary text-uppercase fw-semibold">PCForge account</p>
            <h1><?= $verifyMode ? 'Check your email' : 'Create your account' ?></h1>
            <p class="lead text-secondary">
                <?= $verifyMode ? 'Enter the six-digit code we sent to ' . e($email) . '.' : 'Save builds and complete checkout faster.' ?>
            </p>
        </div>

        <?php if ($errors): ?>
        <div class="alert alert-warning" role="alert">
            <ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>
        <?php if ($notice): ?><div class="alert alert-info" role="status"><?= e($notice) ?></div><?php endif; ?>

        <?php if ($verifyMode): ?>
        <form method="post" action="<?= e(url('register.php')) ?>" class="auth-form border rounded-4 p-4 p-md-5">
            <?= csrf_field() ?><input type="hidden" name="action" value="verify_otp"><input type="hidden"
                name="redirect" value="<?= e($target) ?>">
            <label class="form-label" for="otp">Verification code</label>
            <input class="form-control form-control-lg text-center" type="text" id="otp" name="otp" inputmode="numeric"
                autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required>
            <button class="btn btn-primary w-100 mt-4" type="submit">Verify email and create account</button>
        </form>
        <form method="post" action="<?= e(url('register.php')) ?>" class="mt-2">
            <?= csrf_field() ?><input type="hidden" name="action" value="resend_otp"><input type="hidden"
                name="redirect" value="<?= e($target) ?>">
            <button class="btn btn-outline-dark w-100" type="submit">Send a new code</button>
        </form>
        <a class="d-block text-center small mt-3"
            href="<?= e(url('register.php?reset=1&redirect=' . rawurlencode($target))) ?>">Use a different email</a>
        <?php else: ?>
        <form method="post" action="<?= e(url('register.php')) ?>" class="auth-form border rounded-4 p-4 p-md-5">
            <?= csrf_field() ?><input type="hidden" name="redirect" value="<?= e($target) ?>">
            <div class="mb-3"><label class="form-label" for="username">Username</label><input class="form-control"
                    type="text" id="username" name="username" maxlength="50" autocomplete="username" required
                    value="<?= e($username) ?>"></div>
            <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control"
                    type="email" id="email" name="email" maxlength="100" autocomplete="email" required
                    value="<?= e($email) ?>"></div>
            <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control"
                    type="password" id="password" name="password" minlength="8" autocomplete="new-password" required>
            </div>
            <div class="mb-4"><label class="form-label" for="password_confirmation">Confirm password</label><input
                    class="form-control" type="password" id="password_confirmation" name="password_confirmation"
                    minlength="8" autocomplete="new-password" required></div>
            <button class="btn btn-primary w-100" type="submit">Send verification code</button>
            <div class="d-flex align-items-center gap-3 my-4">
                <hr class="flex-grow-1"><span class="small text-secondary">or</span>
                <hr class="flex-grow-1">
            </div>
            <a class="btn btn-outline-dark w-100"
                href="<?= e(url('auth/google-start.php?redirect=' . rawurlencode($target))) ?>">Continue with
                Google</a>
            <p class="small text-secondary text-center mt-4 mb-0">Already have an account? <a
                    href="<?= e(url('login.php?redirect=' . rawurlencode($target))) ?>">Sign in</a></p>
        </form>
        <?php endif; ?>
    </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>