<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/oauth.php';

$target = auth_redirect_target('index.php');
if (auth_user()) {
    redirect(login_destination(auth_user(), $target));
}

if (!google_oauth_enabled()) {
    redirect('login.php?oauth_error=google_not_configured&redirect=' . rawurlencode($target));
}

$config = google_oauth_config();
$state = bin2hex(random_bytes(32));
$_SESSION['google_oauth_state'] = $state;
$_SESSION['google_oauth_redirect'] = $target;
$_SESSION['google_oauth_started'] = time();

$query = http_build_query([
    'client_id' => $config['client_id'],
    'redirect_uri' => $config['redirect_uri'],
    'response_type' => 'code',
    'scope' => 'openid profile email',
    'state' => $state,
    'access_type' => 'online',
    'prompt' => 'select_account',
], '', '&', PHP_QUERY_RFC3986);

header('Location: ' . $config['authorization_endpoint'] . '?' . $query, true, 302);
exit;
