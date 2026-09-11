<?php
declare(strict_types=1);

/*
 * Keep this file and .env outside the public web root in production.
 */

const PROJECT_ROOT = __DIR__ . '/..';

function load_env_file(string $path): array
{
    if (!is_readable($path)) {
        return [];
    }

    $values = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        if (
            strlen($value) >= 2 &&
            (($value[0] === '"' && $value[-1] === '"') ||
             ($value[0] === "'" && $value[-1] === "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        $values[$key] = $value;
    }

    return $values;
}

/*
 * Production recommendation:
 * set ENV_FILE to an absolute path outside public/.
 */
$envPath = getenv('ENV_FILE') ?: PROJECT_ROOT . '/.env';
$env = load_env_file($envPath);

foreach ($env as $key => $value) {
    if (getenv($key) === false) {
        putenv($key . '=' . $value);
    }
}

function env_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function app_env(string $key, ?string $default = null): ?string
{
    return env_value($key, $default);
}

date_default_timezone_set('UTC');

require_once PROJECT_ROOT . '/vendor/autoload.php';

require_once __DIR__ . '/security.php';
require_once __DIR__ . '/rate-limit.php';
