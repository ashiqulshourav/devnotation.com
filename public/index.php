<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/bootstrap.php';

send_security_headers();
start_secure_session();

$status = $_GET['status'] ?? null;
$csrf = csrf_token();

$turnstileEnabled = filter_var(
    app_env('TURNSTILE_ENABLED', 'false'),
    FILTER_VALIDATE_BOOL
);

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="description"
        content="Devnotation builds practical digital products, web experiences and technology solutions."
    >

    <link rel="apple-touch-icon" sizes="180x180" href="./assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/img/favicon-16x16.png">
    <link rel="manifest" href="./assets/img/site.webmanifest">

    <meta
        name="theme-color"
        content="#1B59A6"
    >

    <title>Devnotation — Build useful things.</title>

    <link
        rel="stylesheet"
        href="./assets/css/tailwind.css?v=1.0.1"
    >
</head>

<body class="min-h-screen bg-white text-zinc-900 antialiased selection:bg-[#1B59A6] selection:text-white">

    <!-- SKIP LINK -->
    <a
        class="fixed left-4 -top-25 z-100 rounded-full bg-[#1B59A6] px-4 py-2 text-sm font-bold text-white transition focus:top-4"
        href="#main"
    >
        Skip to content
    </a>


    <!-- HEADER -->
    <header class="sticky inset-x-0 top-0 z-50 border-b border-[#D9E5F4] bg-white/95 backdrop-blur-md">

        <div class="mx-auto flex min-h-21 w-[min(1180px,calc(100%-32px))] items-center justify-between">

            <a
                class="group flex items-center gap-3 text-lg font-bold tracking-[-0.04em] text-[#14457F]"
                href="./"
                aria-label="Devnotation home"
            >

                <span
                    class="w-10 h-10 block rounded-full"
                >
                    <img src="./assets/img/logo.jpg" alt="Logo of Devnotation" class="rounded-full">
                </span>

                <span>
                    Devnotation<span class="text-[#1B59A6]"></span>
                </span>

            </a>


            <a
                class="rounded-full border border-[#B9CDE5] bg-white px-4 py-2 text-sm font-semibold text-[#14457F] transition hover:border-[#1B59A6] hover:bg-[#EAF2FC] hover:text-[#1B59A6]"
                href="#contact"
            >
                Contact
            </a>

        </div>

    </header>


    <main id="main">


        <!-- HERO -->
        <section
            class="relative isolate overflow-hidden border-b border-[#D9E5F4] bg-white"
        >

            <!-- subtle blue grid -->
            <div
                class="hero-grid-pattern pointer-events-none absolute inset-0 -z-10 opacity-70"
            ></div>


            <!-- blue glow -->
            <div
                class="pointer-events-none absolute right-[5%] top-[15%] -z-10 h-105 w-105 rounded-full bg-[#BFD8F4]/40 blur-3xl"
            ></div>


            <div
                class="mx-auto grid min-h-190 w-[min(1180px,calc(100%-32px))] items-center gap-16 pb-20 pt-32 lg:grid-cols-[1.02fr_.98fr] lg:gap-20"
            >

                <div>

                    <p
                        class="mb-5 inline-flex items-center gap-2 rounded-full border border-[#C8D9ED] bg-[#EAF2FC] px-3 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.18em] text-[#1B59A6]"
                    >

                        <span class="size-1.5 rounded-full bg-[#1B59A6]"></span>

                        Independent technology studio

                    </p>


                    <h1
                        class="max-w-4xl text-[clamp(3.5rem,7vw,6.4rem)] font-semibold leading-[.9] tracking-[-0.075em] text-zinc-950"
                    >
                        Build useful things

                        <span class="text-[#7893B2]">
                            with technology.
                        </span>
                    </h1>


                    <p
                        class="mt-7 max-w-xl text-lg leading-8 text-zinc-600 sm:text-xl"
                    >
                        Devnotation is a small, focused technology company creating
                        practical web experiences, automation and software solutions.
                    </p>


                    <div class="mt-9 flex flex-wrap items-center gap-5">

                        <a
                            class="group inline-flex min-h-12 items-center gap-4 rounded-full bg-[#1B59A6] px-6 font-bold text-white shadow-[0_12px_30px_rgba(27,89,166,.20)] transition hover:-translate-y-0.5 hover:bg-[#14457F]"
                            href="#contact"
                        >
                            Start a conversation

                            <span class="transition-transform group-hover:translate-x-1">
                                ↗
                            </span>
                        </a>


                        <a
                            class="text-sm font-semibold text-zinc-600 transition hover:text-[#1B59A6]"
                            href="#services"
                        >
                            Explore what we do

                            <span class="ml-1">↓</span>
                        </a>

                    </div>

                </div>


                <!-- CODE CARD -->
                <div class="relative flex min-h-97.5 items-center justify-center">

                    <div
                        class="absolute size-80 rounded-full border border-[#C8D9ED] sm:size-102.5"
                    ></div>

                    <div
                        class="absolute size-117.5 rounded-full border border-[#E4EDF7] sm:size-142.5"
                    ></div>


                    <div
                        class="relative z-10 w-full max-w-127.5 rotate-[1.5deg] overflow-hidden rounded-2xl border border-[#C8D9ED] bg-white shadow-[0_30px_80px_rgba(27,89,166,.14)]"
                    >

                        <!-- browser header -->
                        <div class="flex h-11 items-center gap-1.5 border-b border-[#D9E5F4] px-4">

                            <i class="size-1.5 rounded-full bg-[#B9CDE5]"></i>
                            <i class="size-1.5 rounded-full bg-[#B9CDE5]"></i>
                            <i class="size-1.5 rounded-full bg-[#B9CDE5]"></i>

                            <span class="mx-auto -translate-x-2.5 font-mono text-[10px] text-[#7893B2]">
                                Devnotation
                            </span>

                        </div>


                        <div
                            class="min-h-75 bg-[#F5F8FC] p-7 font-mono text-[13px] leading-7 text-zinc-700"
                        >

                            <p>
                                <span class="text-[#1B59A6]">$</span>
                                build --idea
                            </p>

                            <p class="text-[#7893B2]">
                                Planning the next useful thing...
                            </p>

                            <p>
                                <span class="text-[#1B59A6]">→</span>
                                design
                            </p>

                            <p>
                                <span class="text-[#1B59A6]">→</span>
                                develop
                            </p>

                            <p>
                                <span class="text-[#1B59A6]">→</span>
                                test
                            </p>

                            <p>
                                <span class="text-[#1B59A6]">→</span>
                                ship
                            </p>

                            <p class="mt-3">
                                <span class="text-[#1B59A6]">$</span>

                                <span
                                    class="inline-block h-4 w-2 translate-y-0.5 animate-pulse bg-[#1B59A6]"
                                ></span>
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <div
                class="mx-auto flex w-[min(1180px,calc(100%-32px))] items-center gap-4 pb-8 text-xs font-medium text-[#7893B2]"
            >

                <span class="h-px w-12 bg-[#C8D9ED]"></span>

                Small team. Direct communication. Practical solutions.

            </div>

        </section>


        <!-- SERVICES -->
        <section
            class="border-b border-[#D9E5F4] bg-[#F5F8FC] py-24 sm:py-32"
            id="services"
        >

            <div class="mx-auto w-[min(1180px,calc(100%-32px))]">

                <div class="mb-14 grid gap-6 lg:grid-cols-[.7fr_1.3fr] lg:gap-12">

                    <p
                        class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#1B59A6]"
                    >
                        What we do
                    </p>

                    <h2
                        class="max-w-3xl text-4xl font-semibold leading-none tracking-[-0.06em] text-zinc-950 sm:text-6xl"
                    >
                        Technology without unnecessary complexity.
                    </h2>

                </div>


                <div class="grid gap-4 md:grid-cols-3">


                    <!-- SERVICE 01 -->
                    <article
                        class="group min-h-71.25 rounded-2xl border border-[#D9E5F4] bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#9EBBDD] hover:shadow-[0_18px_40px_rgba(27,89,166,.10)]"
                    >

                        <span class="font-mono text-xs text-[#7893B2]">
                            01
                        </span>

                        <h3
                            class="mt-20 text-2xl font-semibold tracking-[-0.04em] text-zinc-900"
                        >
                            Web Development
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-zinc-600">
                            Fast, responsive websites and web applications designed
                            around real business needs.
                        </p>

                    </article>


                    <!-- SERVICE 02 -->
                    <article
                        class="group min-h-71.25 rounded-2xl border border-[#D9E5F4] bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#9EBBDD] hover:shadow-[0_18px_40px_rgba(27,89,166,.10)]"
                    >

                        <span class="font-mono text-xs text-[#7893B2]">
                            02
                        </span>

                        <h3
                            class="mt-20 text-2xl font-semibold tracking-[-0.04em] text-zinc-900"
                        >
                            Automation
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-zinc-600">
                            Turn repetitive processes into dependable workflows
                            that save time and reduce manual work.
                        </p>

                    </article>


                    <!-- SERVICE 03 -->
                    <article
                        class="group min-h-71.25 rounded-2xl border border-[#D9E5F4] bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#9EBBDD] hover:shadow-[0_18px_40px_rgba(27,89,166,.10)]"
                    >

                        <span class="font-mono text-xs text-[#7893B2]">
                            03
                        </span>

                        <h3
                            class="mt-20 text-2xl font-semibold tracking-[-0.04em] text-zinc-900"
                        >
                            AI &amp; Software
                        </h3>

                        <p class="mt-3 text-sm leading-6 text-zinc-600">
                            Explore practical ways to use modern AI and software
                            to solve specific problems.
                        </p>

                    </article>

                </div>

            </div>

        </section>


        <!-- CONTACT -->
        <section
            class="bg-white py-24 sm:py-32"
            id="contact"
        >

            <div
                class="mx-auto grid w-[min(1180px,calc(100%-32px))] items-start gap-14 lg:grid-cols-[.9fr_1.1fr] lg:gap-24"
            >

                <div>

                    <p
                        class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#1B59A6]"
                    >
                        Get in touch
                    </p>


                    <h2
                        class="mt-5 text-5xl font-semibold leading-[.95] tracking-[-0.065em] text-zinc-950 sm:text-6xl"
                    >
                        Have something worth building?
                    </h2>


                    <p class="mt-6 max-w-lg text-base leading-7 text-zinc-600">
                        Tell us what you’re working on. A short description is
                        enough to start the conversation.
                    </p>


                    <div
                        class="mt-10 flex max-w-lg gap-4 border-t border-[#D9E5F4] pt-5 text-sm leading-6 text-zinc-500"
                    >

                        <span class="text-[#1B59A6]">
                            ↳
                        </span>

                        <p>
                            Your message is sent securely and your contact details
                            are never published.
                        </p>

                    </div>

                </div>


                <!-- CONTACT FORM -->
                <div
                    class="rounded-2xl border border-[#D9E5F4] bg-[#F5F8FC] p-5 shadow-sm sm:p-8"
                >

                    <?php if ($statusMessage !== ''): ?>

                        <div
                            class="mb-5 rounded-xl border px-4 py-3 text-sm
                            <?= $statusType === 'success'
                                ? 'border-[#B8D5F3] bg-[#EAF2FC] text-[#14457F]'
                                : 'border-red-200 text-red-700'
                            ?>"
                            role="status"
                        >
                            <?= h($statusMessage) ?>
                        </div>

                    <?php endif; ?>


                    <form
                        action="./contact.php"
                        method="post"
                        id="contact-form"
                        novalidate
                        class="space-y-5"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= h($csrf) ?>"
                        >


                        <!-- HONEYPOT -->
                        <div
                            class="absolute h-px w-px overflow-hidden opacity-0"
                            aria-hidden="true"
                        >

                            <label for="website">
                                Website
                            </label>

                            <input
                                type="text"
                                id="website"
                                name="website"
                                tabindex="-1"
                                autocomplete="off"
                            >

                        </div>


                        <!-- EMAIL -->
                        <div>

                            <label
                                class="mb-2 block text-xs font-bold text-zinc-700"
                                for="email"
                            >
                                Your email
                            </label>

                            <input
                                class="form-input"
                                type="email"
                                id="email"
                                name="email"
                                maxlength="254"
                                autocomplete="email"
                                aria-describedby="email-error"
                                required
                                placeholder="example@email.com"
                            >

                            <p
                                class="field-error"
                                id="email-error"
                                data-error-for="email"
                                aria-live="polite"
                            ></p>

                        </div>


                        <!-- SUBJECT -->
                        <div>

                            <label
                                class="mb-2 block text-xs font-bold text-zinc-700"
                                for="subject"
                            >
                                Subject
                            </label>

                            <input
                                class="form-input"
                                type="text"
                                id="subject"
                                name="subject"
                                maxlength="150"
                                aria-describedby="subject-error"
                                required
                                placeholder="What would you like to build?"
                            >

                            <p
                                class="field-error"
                                id="subject-error"
                                data-error-for="subject"
                                aria-live="polite"
                            ></p>

                        </div>


                        <!-- MESSAGE -->
                        <div>

                            <label
                                class="mb-2 block text-xs font-bold text-zinc-700"
                                for="message"
                            >
                                Message
                            </label>

                            <textarea
                                class="form-input min-h-40 resize-y"
                                id="message"
                                name="message"
                                maxlength="5000"
                                aria-describedby="message-error"
                                required
                                placeholder="Tell us a little about your project..."
                            ></textarea>

                            <p
                                class="field-error"
                                id="message-error"
                                data-error-for="message"
                                aria-live="polite"
                            ></p>

                        </div>


                        <?php if ($turnstileEnabled && $turnstileSiteKey !== ''): ?>

                            <div class="pt-1">

                                <div
                                    class="cf-turnstile"
                                    data-sitekey="<?= h($turnstileSiteKey) ?>"
                                ></div>

                            </div>

                        <?php endif; ?>


                        <button
                            type="submit"
                            class="inline-flex min-h-12 w-full items-center justify-center rounded-full bg-[#1B59A6] px-6 font-bold text-white shadow-[0_12px_30px_rgba(27,89,166,.18)] transition hover:bg-[#14457F] focus:outline-none focus:ring-4 focus:ring-[#B8D5F3] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            Send message
                        </button>

                    </form>

                </div>

            </div>

        </section>

    </main>


    <!-- FOOTER -->
    <footer class="border-t border-[#D9E5F4] bg-[#F5F8FC] py-12">

    <div class="mx-auto w-[min(1180px,calc(100%-32px))]">

        <div class="flex flex-col items-start justify-between gap-8 pb-12 sm:flex-row sm:items-end">

            <div>

                <a href="./" class="flex items-center gap-3">

                    <span class="w-10 h-10 block rounded-full">
                        <img src="./assets/img/logo.jpg" alt="Logo of Devnotation">
                    </span>

                    <span class="text-sm font-bold tracking-[-0.02em] text-[#14457F]">
                        Devnotation
                    </span>

                </a>


                <p class="mt-4 text-sm text-[#7893B2]">
                    Building useful things with technology.
                </p>

            </div>


            <a class="text-sm font-semibold text-zinc-600 transition hover:text-lime-700" href="#contact">
                Let’s talk
                <span class="text-lime-600">↗</span>
            </a>

        </div>


        <div class="flex flex-col justify-between gap-2 border-t border-[#D9E5F4] pt-5 text-[11px] text-[#7893B2] sm:flex-row">

            <span>
                © 2026 Devnotation. All rights reserved.
            </span>

            <span>
                Independent technology studio
            </span>

        </div>

    </div>

</footer>


    <?php if ($turnstileEnabled && $turnstileSiteKey !== ''): ?>

        <script
            src="https://challenges.cloudflare.com/turnstile/v0/api.js"
            defer
        ></script>

    <?php endif; ?>


    <script
        src="./assets/js/app.js?v=1.0.0"
        defer
    ></script>

</body>

</html>