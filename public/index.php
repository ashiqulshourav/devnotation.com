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
    <meta name="theme-color" content="#070707">
    <title>Devnotation — Build useful things.</title>
    <link rel="stylesheet" href="./assets/css/tailwind.css">
</head>
<body class="bg-[#070707] text-zinc-100 antialiased selection:bg-lime-300 selection:text-black">
<a class="fixed left-4 top-[-100px] z-[100] rounded-full bg-lime-300 px-4 py-2 text-sm font-bold text-black focus:top-4" href="#main">Skip to content</a>

<header class="absolute inset-x-0 top-0 z-20">
    <div class="mx-auto flex min-h-[84px] w-[min(1180px,calc(100%-32px))] items-center justify-between border-b border-white/10">
        <a class="group flex items-center gap-3 text-lg font-bold tracking-[-0.04em]" href="/" aria-label="Devnotation home">
            <span class="grid size-9 place-items-center rounded-xl border border-white/10 bg-white/[0.03] font-mono text-xs text-lime-300 transition group-hover:border-lime-300/40">&lt;/&gt;</span>
            <span>devnotation<span class="text-lime-300">.</span></span>
        </a>
        <a class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-zinc-400 transition hover:border-lime-300/40 hover:text-lime-300" href="#contact">Contact</a>
    </div>
</header>

<main id="main">
    <section class="relative isolate overflow-hidden border-b border-white/10 bg-[radial-gradient(circle_at_78%_32%,rgba(190,242,100,0.12),transparent_28%),radial-gradient(circle_at_20%_80%,rgba(255,255,255,0.05),transparent_30%)]">
        <div class="pointer-events-none absolute inset-0 -z-10 bg-[linear-gradient(to_right,rgba(255,255,255,0.025)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.025)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:linear-gradient(to_bottom,black,transparent_85%)]"></div>
        <div class="mx-auto grid min-h-[760px] w-[min(1180px,calc(100%-32px))] items-center gap-16 pb-20 pt-32 lg:grid-cols-[1.02fr_.98fr] lg:gap-20">
            <div>
                <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-lime-300/20 bg-lime-300/[0.06] px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.18em] text-lime-300"><span class="size-1.5 rounded-full bg-lime-300 shadow-[0_0_12px_rgba(190,242,100,.9)]"></span>Independent technology studio</p>
                <h1 class="max-w-4xl text-[clamp(3.5rem,7vw,6.4rem)] font-semibold leading-[.9] tracking-[-0.075em] text-white">Build useful things<span class="text-zinc-500"> with technology.</span></h1>
                <p class="mt-7 max-w-xl text-lg leading-8 text-zinc-400 sm:text-xl">Devnotation is a small, focused technology company creating practical web experiences, automation and software solutions.</p>
                <div class="mt-9 flex flex-wrap items-center gap-5">
                    <a class="group inline-flex min-h-12 items-center gap-4 rounded-full bg-lime-300 px-6 font-bold text-black transition hover:-translate-y-0.5 hover:bg-lime-200" href="#contact">Start a conversation <span class="transition-transform group-hover:translate-x-1">↗</span></a>
                    <a class="text-sm font-semibold text-zinc-400 transition hover:text-lime-300" href="#services">Explore what we do <span class="ml-1">↓</span></a>
                </div>
            </div>

            <div class="relative flex min-h-[390px] items-center justify-center">
                <div class="absolute size-[320px] rounded-full border border-lime-300/10 sm:size-[410px]"></div>
                <div class="absolute size-[470px] rounded-full border border-white/5 sm:size-[570px]"></div>
                <div class="relative z-10 w-full max-w-[510px] rotate-[1.5deg] overflow-hidden rounded-2xl border border-white/10 bg-[#111214]/95 shadow-[0_30px_100px_rgba(0,0,0,.55)] backdrop-blur">
                    <div class="flex h-11 items-center gap-1.5 border-b border-white/10 px-4"><i class="size-1.5 rounded-full bg-zinc-600"></i><i class="size-1.5 rounded-full bg-zinc-600"></i><i class="size-1.5 rounded-full bg-zinc-600"></i><span class="mx-auto translate-x-[-10px] font-mono text-[10px] text-zinc-600">devnotation</span></div>
                    <div class="min-h-[300px] p-7 font-mono text-[13px] leading-7"><p><span class="text-lime-300">$</span> build --idea</p><p class="text-zinc-600">Planning the next useful thing...</p><p><span class="text-lime-300">→</span> design</p><p><span class="text-lime-300">→</span> develop</p><p><span class="text-lime-300">→</span> test</p><p><span class="text-lime-300">→</span> ship</p><p class="mt-3"><span class="text-lime-300">$</span> <span class="inline-block h-4 w-2 translate-y-0.5 animate-pulse bg-lime-300"></span></p></div>
                </div>
            </div>
        </div>
        <div class="mx-auto flex w-[min(1180px,calc(100%-32px))] items-center gap-4 pb-8 text-xs font-medium text-zinc-600"><span class="h-px w-12 bg-white/10"></span>Small team. Direct communication. Practical solutions.</div>
    </section>

    <section class="border-b border-white/10 py-24 sm:py-32" id="services">
        <div class="mx-auto w-[min(1180px,calc(100%-32px))]">
            <div class="mb-14 grid gap-6 lg:grid-cols-[.7fr_1.3fr] lg:gap-12"><p class="text-xs font-extrabold uppercase tracking-[0.18em] text-lime-300">What we do</p><h2 class="max-w-3xl text-4xl font-semibold leading-none tracking-[-0.06em] text-white sm:text-6xl">Technology without unnecessary complexity.</h2></div>
            <div class="grid gap-3 md:grid-cols-3">
                <article class="group min-h-[285px] rounded-2xl border border-white/10 bg-white/[0.025] p-7 transition duration-300 hover:-translate-y-1 hover:border-lime-300/30 hover:bg-lime-300/[0.035]"><span class="font-mono text-xs text-zinc-600">01</span><h3 class="mt-20 text-2xl font-semibold tracking-[-0.04em]">Web Development</h3><p class="mt-3 text-sm leading-6 text-zinc-500">Fast, responsive websites and web applications designed around real business needs.</p></article>
                <article class="group min-h-[285px] rounded-2xl border border-white/10 bg-white/[0.025] p-7 transition duration-300 hover:-translate-y-1 hover:border-lime-300/30 hover:bg-lime-300/[0.035]"><span class="font-mono text-xs text-zinc-600">02</span><h3 class="mt-20 text-2xl font-semibold tracking-[-0.04em]">Automation</h3><p class="mt-3 text-sm leading-6 text-zinc-500">Turn repetitive processes into dependable workflows that save time and reduce manual work.</p></article>
                <article class="group min-h-[285px] rounded-2xl border border-white/10 bg-white/[0.025] p-7 transition duration-300 hover:-translate-y-1 hover:border-lime-300/30 hover:bg-lime-300/[0.035]"><span class="font-mono text-xs text-zinc-600">03</span><h3 class="mt-20 text-2xl font-semibold tracking-[-0.04em]">AI &amp; Software</h3><p class="mt-3 text-sm leading-6 text-zinc-500">Explore practical ways to use modern AI and software to solve specific problems.</p></article>
            </div>
        </div>
    </section>

    <section class="py-24 sm:py-32" id="contact">
        <div class="mx-auto grid w-[min(1180px,calc(100%-32px))] items-start gap-14 lg:grid-cols-[.9fr_1.1fr] lg:gap-24">
            <div><p class="text-xs font-extrabold uppercase tracking-[0.18em] text-lime-300">Get in touch</p><h2 class="mt-5 text-5xl font-semibold leading-[.95] tracking-[-0.065em] text-white sm:text-6xl">Have something worth building?</h2><p class="mt-6 max-w-lg text-base leading-7 text-zinc-500">Tell us what you’re working on. A short description is enough to start the conversation.</p><div class="mt-10 flex max-w-lg gap-4 border-t border-white/10 pt-5 text-sm leading-6 text-zinc-600"><span class="text-lime-300">↳</span><p>Your message is sent securely and your contact details are never published.</p></div></div>
            <div class="rounded-2xl border border-white/10 bg-white/[0.025] p-5 sm:p-8">
                <?php if ($statusMessage !== ''): ?><div class="mb-5 rounded-xl border px-4 py-3 text-sm <?= $statusType === 'success' ? 'border-lime-300/30 bg-lime-300/[0.06] text-lime-200' : 'border-red-300/30 bg-red-300/[0.05] text-red-200' ?>" role="status"><?= h($statusMessage) ?></div><?php endif; ?>
                <form action="/contact.php" method="post" id="contact-form" novalidate class="space-y-5">
                    <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                    <div class="absolute h-px w-px overflow-hidden opacity-0" aria-hidden="true"><label for="website">Website</label><input type="text" id="website" name="website" tabindex="-1" autocomplete="off"></div>
                    <div><label class="mb-2 block text-xs font-bold text-zinc-400" for="email">Your email</label><input class="w-full rounded-xl border border-white/10 bg-[#111214] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-zinc-700 focus:border-lime-300/60 focus:ring-4 focus:ring-lime-300/[0.07]" type="email" id="email" name="email" maxlength="254" autocomplete="email" required placeholder="you@example.com"></div>
                    <div><label class="mb-2 block text-xs font-bold text-zinc-400" for="subject">Subject</label><input class="w-full rounded-xl border border-white/10 bg-[#111214] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-zinc-700 focus:border-lime-300/60 focus:ring-4 focus:ring-lime-300/[0.07]" type="text" id="subject" name="subject" maxlength="150" required placeholder="What can we help with?"></div>
                    <div><div class="mb-2 flex items-center justify-between"><label class="block text-xs font-bold text-zinc-400" for="message">Description</label><span class="text-[10px] text-zinc-700"><span id="message-count">0</span>/5000</span></div><textarea class="min-h-[180px] w-full resize-y rounded-xl border border-white/10 bg-[#111214] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-zinc-700 focus:border-lime-300/60 focus:ring-4 focus:ring-lime-300/[0.07]" id="message" name="message" maxlength="5000" minlength="10" rows="7" required placeholder="Tell us a little about your idea or project..."></textarea></div>
                    <?php if ($turnstileEnabled && $turnstileSiteKey !== ''): ?><div class="min-h-16"><div class="cf-turnstile" data-sitekey="<?= h($turnstileSiteKey) ?>"></div></div><script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script><?php endif; ?>
                    <button class="group flex min-h-12 w-full items-center justify-center gap-4 rounded-full bg-lime-300 px-6 font-bold text-black transition hover:-translate-y-0.5 hover:bg-lime-200 disabled:cursor-wait disabled:opacity-50" type="submit" id="submit-button"><span>Send message</span><span class="transition-transform group-hover:translate-x-1" aria-hidden="true">→</span></button>
                    <p class="text-center text-[10px] leading-4 text-zinc-700">By sending this form, you agree that we may use your details to respond to your message.</p>
                </form>
            </div>
        </div>
    </section>
</main>

<footer class="border-t border-white/10 py-12">
    <div class="mx-auto w-[min(1180px,calc(100%-32px))]">
        <div class="flex flex-col items-start justify-between gap-8 pb-12 sm:flex-row sm:items-end"><div><a class="flex items-center gap-3 text-lg font-bold tracking-[-0.04em]" href="/"><span class="grid size-9 place-items-center rounded-xl border border-white/10 font-mono text-xs text-lime-300">&lt;/&gt;</span><span>devnotation<span class="text-lime-300">.</span></span></a><p class="mt-4 text-sm text-zinc-600">Building useful things with technology.</p></div><a class="text-sm font-semibold text-zinc-400 transition hover:text-lime-300" href="#contact">Let’s talk <span class="text-lime-300">↗</span></a></div>
        <div class="flex flex-col justify-between gap-2 border-t border-white/10 pt-5 text-[11px] text-zinc-700 sm:flex-row"><span>© <?= date('Y') ?> Devnotation. All rights reserved.</span><span>Independent technology studio</span></div>
    </div>
</footer>
<script src="./assets/js/app.js" defer></script>
</body>
</html>
