<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

send_security_headers();
start_secure_session();

$status = $_GET['status'] ?? null;
$csrf = csrf_token();
$turnstileEnabled = filter_var(app_env('TURNSTILE_ENABLED', 'false'), FILTER_VALIDATE_BOOL);
$turnstileSiteKey = app_env('TURNSTILE_SITE_KEY', '');

$statusMessages = [
    'sent' => ['success', 'Message sent successfully. We’ll get back to you soon.'],
    'error' => ['error', 'Something went wrong while sending your message. Please try again.'],
    'invalid' => ['error', 'Please check the form and try again.'],
    'busy' => ['error', 'Too many requests. Please wait a little and try again.'],
    'forbidden' => ['error', 'Your request could not be verified. Please refresh and try again.'],
];

[$statusType, $statusMessage] = $statusMessages[$status] ?? ['', ''];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Devnotation builds practical digital products, web experiences and technology solutions.">
    <meta name="theme-color" content="#08090b">
    <title>Devnotation — Build. Solve. Ship.</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="/" aria-label="Devnotation home">
            <span class="brand-mark">&lt;/&gt;</span>
            <span>devnotation</span>
        </a>
        <a class="nav-link" href="#contact">Contact</a>
    </div>
</header>

<main id="main">
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Independent technology studio</p>
                <h1>Ideas into <span>useful digital products.</span></h1>
                <p class="hero-text">
                    Devnotation is a small, focused technology company building practical
                    web experiences, automation and software solutions.
                </p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#contact">Start a conversation <span>↗</span></a>
                    <a class="text-link" href="#services">Explore what we do</a>
                </div>
            </div>

            <div class="hero-art" aria-hidden="true">
                <div class="terminal">
                    <div class="terminal-bar">
                        <i></i><i></i><i></i>
                        <span>devnotation</span>
                    </div>
                    <div class="terminal-body">
                        <p><b>$</b> build --idea</p>
                        <p class="muted">Planning the next useful thing...</p>
                        <p><b>→</b> design</p>
                        <p><b>→</b> develop</p>
                        <p><b>→</b> ship</p>
                        <p class="cursor"><b>$</b> <span></span></p>
                    </div>
                </div>
                <div class="orb orb-one"></div>
                <div class="orb orb-two"></div>
            </div>
        </div>
    </section>

    <section class="services" id="services">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">What we do</p>
                <h2>Technology without unnecessary complexity.</h2>
            </div>

            <div class="service-grid">
                <article class="service-card">
                    <span class="service-number">01</span>
                    <h3>Web Development</h3>
                    <p>Fast, responsive websites and web applications designed around real business needs.</p>
                </article>
                <article class="service-card">
                    <span class="service-number">02</span>
                    <h3>Automation</h3>
                    <p>Turn repetitive processes into dependable workflows that save time and reduce manual work.</p>
                </article>
                <article class="service-card">
                    <span class="service-number">03</span>
                    <h3>AI &amp; Software</h3>
                    <p>Explore practical ways to use modern AI and software to solve specific problems.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="contact-section" id="contact">
        <div class="container contact-grid">
            <div class="contact-copy">
                <p class="eyebrow">Get in touch</p>
                <h2>Have something worth building?</h2>
                <p>
                    Tell us what you’re working on. A short description is enough to start
                    the conversation.
                </p>
                <div class="contact-note">
                    <span>↳</span>
                    <p>Your message is sent securely and your contact details are never published.</p>
                </div>
            </div>

            <div class="form-card">
                <?php if ($statusMessage !== ''): ?>
                    <div class="alert <?= h($statusType) ?>" role="status"><?= h($statusMessage) ?></div>
                <?php endif; ?>

                <form action="/contact.php" method="post" id="contact-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">

                    <!-- Honeypot: real users should leave this empty. -->
                    <div class="hp-field" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="field">
                        <label for="email">Your email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            maxlength="254"
                            autocomplete="email"
                            required
                            placeholder="you@example.com"
                        >
                    </div>

                    <div class="field">
                        <label for="subject">Subject</label>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            maxlength="150"
                            required
                            placeholder="What can we help with?"
                        >
                    </div>

                    <div class="field">
                        <label for="message">Description</label>
                        <textarea
                            id="message"
                            name="message"
                            maxlength="5000"
                            minlength="10"
                            rows="7"
                            required
                            placeholder="Tell us a little about your idea or project..."
                        ></textarea>
                        <div class="field-meta"><span id="message-count">0</span>/5000</div>
                    </div>

                    <?php if ($turnstileEnabled && $turnstileSiteKey !== ''): ?>
                        <div class="turnstile-wrap">
                            <div class="cf-turnstile" data-sitekey="<?= h($turnstileSiteKey) ?>"></div>
                        </div>
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
                    <?php endif; ?>

                    <button class="button button-submit" type="submit" id="submit-button">
                        <span>Send message</span>
                        <span aria-hidden="true">→</span>
                    </button>

                    <p class="form-disclaimer">By sending this form, you agree that we may use your details to respond to your message.</p>
                </form>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="container footer-top">
        <div>
            <a class="brand" href="/">
                <span class="brand-mark">&lt;/&gt;</span>
                <span>devnotation</span>
            </a>
            <p>Building useful things with technology.</p>
        </div>
        <a class="footer-contact" href="#contact">Let’s talk <span>↗</span></a>
    </div>
    <div class="container footer-bottom">
        <span>© <?= date('Y') ?> Devnotation. All rights reserved.</span>
        <span>Independent technology studio</span>
    </div>
</footer>

<script src="/assets/js/app.js" defer></script>
</body>
</html>
