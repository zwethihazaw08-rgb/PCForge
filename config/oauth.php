<?php

function google_oauth_config(): array
{
    return [
        // Keep OAuth credentials in the Apache/PHP environment, never in source control.
        'client_id' => trim((string) getenv('PCFORGE_GOOGLE_CLIENT_ID')),
        'client_secret' => trim((string) getenv('PCFORGE_GOOGLE_CLIENT_SECRET')),
        // This URI must exactly match the one registered in Google Cloud Console.
        'redirect_uri' => getenv('PCFORGE_GOOGLE_REDIRECT_URI') ?: 'http://localhost/PCForge/auth/google-callback.php',
        'authorization_endpoint' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_endpoint' => 'https://oauth2.googleapis.com/token',
        'userinfo_endpoint' => 'https://openidconnect.googleapis.com/v1/userinfo',
    ];
}

function google_oauth_enabled(): bool
{
    $config = google_oauth_config();
    return $config['client_id'] !== ''
        && $config['client_secret'] !== ''
        && $config['client_id'] !== 'PASTE_GOOGLE_CLIENT_ID_HERE'
        && $config['client_secret'] !== 'PASTE_GOOGLE_CLIENT_SECRET_HERE';
}
