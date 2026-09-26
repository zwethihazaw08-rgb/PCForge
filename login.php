<?php

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/oauth.php';

$target = auth_redirect_target('index.php');
if (auth_user()) {
    redirect(login_destination(auth_user(), $target));
}

$errors = [];
$login = trim((string) ($_POST['login'] ?? ''));
$oauthMessages = [
    'google_not_configured' => 'Google sign-in needs to be configured in config/oauth.php first.',
    'google_cancelled' => 'Google sign-in was cancelled.',
    'google_state' => 'Google sign-in expired. Please try again.',
    'google_failed' => 'Google sign-in could not be completed. Please try again.',
];
$oauthMessage = $oauthMessages[(string) ($_GET['oauth_error'] ?? '')] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        csrf_verify();
        $password = (string) ($_POST['password'] ?? '');
        if ($login === '' || $password === '') {
            $errors[] = 'Enter your username or email and password.';
        }

        if (!$errors) {
            $statement = db()->prepare("SELECT id, username, email, password, role FROM users WHERE (email = :email OR username = :username) AND status = 'active' LIMIT 1");
            $statement->execute(['email' => $login, 'username' => $login]);
            $user = $statement->fetch();
            if (!$user || !password_verify($password, $user['password'])) {
                $errors[] = 'The username/email or password is incorrect.';
            } else {
                login_user($user);
                redirect(login_destination($user, $target));
            }
        }
    } catch (PDOException $exception) {
        error_log('PCForge login failed: ' . $exception->getMessage());
        $errors[] = 'Sign in could not be completed. Please try again.';
    }
}

$pageTitle = 'Sign In';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main id="main-content" tabindex="-1">
    <div class="container section-padding">
        <div class="mx-auto" style="max-width: 520px">
            <p class="small text-secondary text-uppercase fw-semibold">PCForge account</p>
            <h1>Sign in</h1>
            <p class="lead text-secondary">Sign in to continue to checkout and access your account.</p>

            <?php if ($errors): ?>
                <div class="alert alert-warning" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if ($oauthMessage): ?><div class="alert alert-info" role="status"><?= e($oauthMessage) ?></div><?php endif; ?>

            <form method="post" action="<?= e(url('login.php')) ?>" class="border rounded-4 p-4 p-md-5">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect" value="<?= e($target) ?>">
                <div class="mb-3">
                    <label class="form-label" for="login">Username or email</label>
                    <input class="form-control" type="text" id="login" name="login" autocomplete="username" required value="<?= e($login) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" type="password" id="password" name="password" autocomplete="current-password" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Sign in</button>
                <div class="d-flex align-items-center gap-3 my-4"><hr class="flex-grow-1"><span class="small text-secondary">or</span><hr class="flex-grow-1"></div>
                <a class="btn btn-outline-dark w-100" href="<?= e(url('auth/google-start.php?redirect=' . rawurlencode($target))) ?>">Continue with Google</a>
                <p class="small text-secondary text-center mt-4 mb-0">Need an account? <a href="<?= e(url('register.php?redirect=' . rawurlencode($target))) ?>">Create one</a></p>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
