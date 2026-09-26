<?php

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/oauth.php';

function google_http_json(string $url, array $options = []): array
{
    $curl = curl_init($url);
    curl_setopt_array($curl, $options + [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    $body = curl_exec($curl);
    // Retry only a failed connection, never a request Google may have processed:
    // authorization codes can only be exchanged once.
    $connectionFailed = curl_errno($curl) === CURLE_COULDNT_CONNECT
        || (curl_errno($curl) === CURLE_OPERATION_TIMEDOUT
            && (float) curl_getinfo($curl, CURLINFO_CONNECT_TIME) === 0.0);
    if ($body === false && $connectionFailed) {
        $body = curl_exec($curl);
    }
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $errorNumber = curl_errno($curl);
    $error = curl_error($curl);
    curl_close($curl);

    $endpoint = (string) parse_url($url, PHP_URL_HOST) . (string) parse_url($url, PHP_URL_PATH);
    if ($body === false || $error !== '') {
        throw new RuntimeException('Google connection failed at ' . $endpoint . ' (cURL ' . $errorNumber . '): ' . $error);
    }

    $data = json_decode($body, true);
    if ($status < 200 || $status >= 300) {
        // Log only the provider's error identifier, never tokens, codes, or response bodies.
        $providerError = is_array($data) && is_string($data['error'] ?? null)
            ? $data['error'] : 'unknown_error';
        $providerError = substr((string) preg_replace('/[^a-zA-Z0-9_]/', '', $providerError), 0, 80);
        throw new RuntimeException('Google request failed at ' . $endpoint . ' (HTTP ' . $status . ', ' . $providerError . ').');
    }
    if (!is_array($data)) {
        throw new RuntimeException('Google returned an invalid response.');
    }

    return $data;
}

function google_create_username(string $name, string $email): string
{
    $source = $name !== '' ? $name : (string) strstr($email, '@', true);
    $base = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '_', $source));
    $base = trim($base, '_');
    if (strlen($base) < 3) $base = 'google_user';
    $base = substr($base, 0, 42);
    $username = $base;
    $number = 2;

    while (true) {
        $statement = db()->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        if (!$statement->fetch()) return $username;
        $suffix = '_' . $number++;
        $username = substr($base, 0, 50 - strlen($suffix)) . $suffix;
    }
}

$target = auth_redirect_target($_SESSION['google_oauth_redirect'] ?? 'index.php');
$state = $_SESSION['google_oauth_state'] ?? '';
$started = (int) ($_SESSION['google_oauth_started'] ?? 0);
unset($_SESSION['google_oauth_state'], $_SESSION['google_oauth_redirect'], $_SESSION['google_oauth_started']);

$errorCode = 'google_failed';
if (isset($_GET['error'])) {
    $errorCode = $_GET['error'] === 'access_denied' ? 'google_cancelled' : 'google_failed';
} elseif (!is_string($_GET['state'] ?? null) || !hash_equals((string) $state, $_GET['state']) || !$started || time() - $started > 600) {
    $errorCode = 'google_state';
} elseif (!is_string($_GET['code'] ?? null) || $_GET['code'] === '') {
    $errorCode = 'google_failed';
} else {
    try {
        $config = google_oauth_config();
        $token = google_http_json($config['token_endpoint'], [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'code' => $_GET['code'],
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'redirect_uri' => $config['redirect_uri'],
                'grant_type' => 'authorization_code',
            ], '', '&', PHP_QUERY_RFC3986),
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'],
        ]);
        if (empty($token['access_token'])) throw new RuntimeException('Google did not return an access token.');

        $profile = google_http_json($config['userinfo_endpoint'], [
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Authorization: Bearer ' . $token['access_token']],
        ]);
        $email = strtolower(trim((string) ($profile['email'] ?? '')));
        $googleId = trim((string) ($profile['sub'] ?? ''));
        if ($googleId === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($profile['email_verified'])) {
            throw new RuntimeException('Google account details could not be verified.');
        }

        $statement = db()->prepare("SELECT id, username, email, role FROM users WHERE email = :email AND status = 'active' LIMIT 1");
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();
        if (!$user) {
            $username = google_create_username((string) ($profile['name'] ?? ''), $email);
            $insert = db()->prepare('INSERT INTO users (username, email, password) VALUES (:username, :email, :password)');
            $insert->execute([
                'username' => $username,
                'email' => $email,
                'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
            ]);
            $user = ['id' => db()->lastInsertId(), 'username' => $username, 'email' => $email, 'role' => 'customer'];
        }

        login_user($user);
        redirect(login_destination($user, $target));
    } catch (Throwable $exception) {
        error_log('PCForge Google login failed: ' . $exception->getMessage());
    }
}

redirect('login.php?oauth_error=' . rawurlencode($errorCode) . '&redirect=' . rawurlencode($target));
