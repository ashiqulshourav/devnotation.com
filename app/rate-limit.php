<?php
declare(strict_types=1);

function rate_limit(string $key, int $maxRequests, int $windowSeconds): bool
{
    $directory = PROJECT_ROOT . '/storage/rate-limit';

    if (!is_dir($directory)) {
        @mkdir($directory, 0700, true);
    }

    if (!is_dir($directory) || !is_writable($directory)) {
        /*
         * Fail closed for a public mail endpoint. If rate-limit storage is
         * unavailable, don't process the message.
         */
        return false;
    }

    $safeKey = hash('sha256', $key);
    $file = $directory . '/' . $safeKey . '.json';
    $now = time();

    $fp = @fopen($file, 'c+');
    if ($fp === false) {
        return false;
    }

    try {
        if (!flock($fp, LOCK_EX)) {
            return false;
        }

        rewind($fp);
        $raw = stream_get_contents($fp);
        $data = json_decode($raw ?: '', true);

        if (!is_array($data)) {
            $data = [];
        }

        $timestamps = [];
        foreach (($data['timestamps'] ?? []) as $timestamp) {
            if (is_int($timestamp) || is_numeric($timestamp)) {
                $timestamp = (int) $timestamp;
                if ($timestamp > $now - $windowSeconds) {
                    $timestamps[] = $timestamp;
                }
            }
        }

        if (count($timestamps) >= $maxRequests) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return false;
        }

        $timestamps[] = $now;

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(['timestamps' => $timestamps], JSON_THROW_ON_ERROR));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return true;
    } catch (Throwable) {
        @flock($fp, LOCK_UN);
        @fclose($fp);
        return false;
    }
}
