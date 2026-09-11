<?php
declare(strict_types=1);

function send_security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    header(
        "Content-Security-Policy: " .
        "default-src 'self'; " .
        "base-uri 'self'; " .
        "form-action 'self'; " .
        "frame-ancestors 'none'; " .
        "object-src 'none'; " .
        "img-src 'self' data:; " .
        "style-src 'self'; " .
        "script-src 'self' https://challenges.cloudflare.com; " .
        "connect-src 'self' https://challenges.cloudflare.com; " .
        "frame-src https://challenges.cloudflare.com; " .
        "font-src 'self';"
    );
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function csrf_token(): string
{
    start_secure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool
{
    start_secure_session();

    return isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function client_ip(): string
{
    /*
     * Do not trust X-Forwarded-For by default. If you configure a trusted
     * reverse proxy, replace this with a proxy-aware implementation.
     */
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function clean_header_value(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect_with_status(string $status): never
{
    $allowed = ['sent', 'error', 'invalid', 'busy', 'forbidden'];
    if (!in_array($status, $allowed, true)) {
        $status = 'error';
    }

    $scriptPath = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/'));
    $basePath = rtrim(str_replace('\\', '/', dirname($scriptPath)), '/');
    $location = ($basePath === '' ? '' : $basePath)
        . '/?status=' . rawurlencode($status) . '#contact';

    header('Location: ' . $location, true, 303);
    exit;
}
