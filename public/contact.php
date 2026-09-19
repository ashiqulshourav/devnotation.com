<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

send_security_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Method Not Allowed');
}

start_secure_session();

if (!verify_csrf((string)($_POST['csrf_token'] ?? ''))) {
    redirect_with_status('forbidden');
}

/*
 * Honeypot: silently reject bots. Do not send an email.
 */
if (trim((string)($_POST['website'] ?? '')) !== '') {
    redirect_with_status('sent');
}

$maxRequests = max(1, (int) app_env('RATE_LIMIT_MAX_REQUESTS', '5'));
$window = max(60, (int) app_env('RATE_LIMIT_WINDOW', '900'));

if (!rate_limit('contact:' . client_ip(), $maxRequests, $window)) {
    redirect_with_status('busy');
}

$globalMaxRequests = max(1, (int) app_env('GLOBAL_RATE_LIMIT_MAX_REQUESTS', '30'));

$globalWindow = max(60, (int) app_env('GLOBAL_RATE_LIMIT_WINDOW', '900'));

if (!rate_limit('contact:global', $globalMaxRequests, $globalWindow)) {
    redirect_with_status('busy');
}

$email = trim((string)($_POST['email'] ?? ''));
$subject = trim((string)($_POST['subject'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

$emailRateLimit = max(1, (int) app_env('EMAIL_RATE_LIMIT_MAX_REQUESTS', '3'));
$emailRateLimitWindow = max(60, (int) app_env('EMAIL_RATE_LIMIT_WINDOW', '3600'));
$emailKey = 'contact:email:' . strtolower($email);

if (!rate_limit($emailKey, $emailRateLimit, $emailRateLimitWindow)) {
    redirect_with_status('busy');
}

$validEmail = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
$validSubject = $subject !== '' && mb_strlen($subject) <= 150;
$validMessage = mb_strlen($message) >= 10 && mb_strlen($message) <= 5000;

if (!$validEmail || !$validSubject || !$validMessage) {
    redirect_with_status('invalid');
}

$email = clean_header_value($email);
$subject = clean_header_value($subject);

$turnstileEnabled = filter_var(app_env('TURNSTILE_ENABLED', 'true'), FILTER_VALIDATE_BOOL);
$turnstileSecret = trim((string) app_env('TURNSTILE_SECRET_KEY', ''));
$turnstileHostnames = array_values(array_filter(array_map(
    'trim',
    explode(',', (string) app_env('TURNSTILE_HOSTNAMES', ''))
)));

if ($turnstileEnabled) {
    if ($turnstileSecret === '' || $turnstileHostnames === []) {
        redirect_with_status('error');
    }

    $token = (string)($_POST['cf-turnstile-response'] ?? '');
    if ($token === '' || strlen($token) > 2048) {
        redirect_with_status('invalid');
    }

    $postData = http_build_query([
        'secret' => $turnstileSecret,
        'response' => $token,
        'remoteip' => client_ip(),
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $postData,
            'timeout' => 5,
        ],
    ]);

    $verification = @file_get_contents(
        'https://challenges.cloudflare.com/turnstile/v0/siteverify',
        false,
        $context
    );

    $verificationData = json_decode($verification ?: '', true);

    if (
        !is_array($verificationData)
        || empty($verificationData['success'])
        || ($verificationData['action'] ?? '') !== 'contact'
        || !in_array((string)($verificationData['hostname'] ?? ''), $turnstileHostnames, true)
    ) {
        redirect_with_status('invalid');
    }
}

$to = app_env('MAIL_TO_ADDRESS');
$host = app_env('MAIL_HOST');
$port = (int) app_env('MAIL_PORT', '587');
$username = app_env('MAIL_USERNAME');
$password = app_env('MAIL_PASSWORD');
$encryption = strtolower((string) app_env('MAIL_ENCRYPTION', 'tls'));
$fromAddress = app_env('MAIL_FROM_ADDRESS');
$fromName = app_env('MAIL_FROM_NAME', 'Devnotation');

if (!$to || !$host || !$username || !$password || !$fromAddress) {
    redirect_with_status('error');
}

try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->Port = $port;

    if ($encryption === 'ssl' || $port === 465) {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mail->CharSet = 'UTF-8';
    $mail->setFrom($fromAddress, $fromName);
    $mail->addAddress($to);
    $mail->addReplyTo($email);

    $mail->Subject = '[Devnotation Contact] ' . $subject;

    $safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    $safeEmail = htmlspecialchars($email, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $safeSubject = htmlspecialchars($subject, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $mail->isHTML(true);
    $mail->Body = <<<HTML
        <h2>New Devnotation contact message</h2>
        <p><strong>From:</strong> {$safeEmail}</p>
        <p><strong>Subject:</strong> {$safeSubject}</p>
        <hr>
        <p>{$safeMessage}</p>
    HTML;

    $mail->AltBody =
        "New Devnotation contact message\n\n" .
        "From: {$email}\n" .
        "Subject: {$subject}\n\n" .
        $message;

    $mail->send();

    /*
     * Rotate the CSRF token after a successful submission.
     */
    unset($_SESSION['csrf_token']);

    redirect_with_status('sent');
} catch (Throwable) {
    /*
     * Never expose SMTP/server details to the visitor.
     */
    error_log('Devnotation contact mail failed.');
    redirect_with_status('error');
}
