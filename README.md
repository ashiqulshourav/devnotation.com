# Devnotation

A minimal, security-focused single-page PHP website with a private contact-form mail flow.

## Stack
- PHP 8.3+
- HTML/CSS/Vanilla JS
- No database
- SMTP via PHPMailer
- Optional Cloudflare Turnstile
- CSRF token
- Honeypot
- File-backed rate limiting
- Security headers

## 1. Install dependencies

From the project root:

```bash
composer require phpmailer/phpmailer
```

This creates `vendor/`, which should be deployed with the application unless your server runs Composer.

## 2. Configure secrets

Copy `.env.example` to `.env` and fill in SMTP credentials.

The preferred production setup is to put `.env` OUTSIDE the public web root.

Example:

```text
/home/account/
  devnotation-private/.env
  public_html/devnotation/
```

If your hosting does not support environment variables, keep `.env` above `public/` and load it through the bootstrap file.

## 3. Web root

Point the domain `devnotation.com` document root to:

```text
/path/to/devnotation/public
```

Do not point the domain to the project root.

## 4. Permissions

The PHP process must be able to write:

```text
storage/rate-limit/
```

Do not make this directory publicly accessible.

## 5. Mail

The contact form does NOT expose the destination email to visitors.

Incoming messages are sent by PHP through SMTP. Configure:

- MAIL_FROM_ADDRESS: an address you control on the sending domain
- MAIL_TO_ADDRESS: your private destination mailbox

## 6. Cloudflare

Recommended production setup:
- SSL/TLS mode: Full (strict)
- Always Use HTTPS: enabled
- WAF/rate limiting: enabled
- Restrict `/contact.php` to sensible request rates
- Optional Turnstile on the contact form

## 7. Apache

The included `public/.htaccess` adds basic security headers and disables directory listing. If your host uses Nginx, configure equivalent rules in Nginx.

## 8. Important isolation rule

Keep this project completely separate from:

`madok.devnotation.com`

Do not share:
- databases
- `.env` files
- API keys
- filesystem directories
- application credentials
- deployment users

The Devnotation site intentionally has no database.
