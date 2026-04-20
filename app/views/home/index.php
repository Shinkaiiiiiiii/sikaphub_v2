<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SikapHub — Guimba's AI-Powered Local Job Platform</title>
    <meta name="description" content="SikapHub connects Guimba's top professionals with verified enterprise opportunities through AI-powered skill matching, strict verification, and real-time application tracking.">
    <meta property="og:title" content="SikapHub — Guimba's AI-Powered Local Job Platform">
    <meta property="og:description" content="Intelligent matching platform elevating local talent with AI.">
    <meta property="og:type" content="website">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#7C3AED',
                        slate: {
                            900: '#0F172A',
                            500: '#64748B'
                        },
                        pearl: '#F8FAFC'
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <style type="text/tailwindcss">
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-20px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        /* Body font override */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Smooth scroll */
        html { scroll-behavior: smooth; }

        /* Subtle grid pattern for hero */
        .hero-grid-bg {
            background-image:
                linear-gradient(rgba(79,70,229,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79,70,229,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        /* Radial fade over grid */
        .hero-radial-fade {
            background: radial-gradient(ellipse 80% 60% at 50% 0%, transparent 40%, #F8FAFC 100%);
        }

        /* Feature card icon ring */
        .feature-icon-ring {
            background: linear-gradient(135deg, #EEF2FF 0%, #F5F3FF 100%);
            border: 1.5px solid #E0E7FF;
        }

        /* Animate bars on scroll */
        @keyframes barGrow {
            from { transform: scaleX(0); }
            to   { transform: scaleX(1); }
        }
        .bar-animate {
            transform-origin: left;
            animation: barGrow 1.2s cubic-bezier(.22,1,.36,1) forwards;
        }

        /* Fade-up scroll reveal */
        .reveal {
            opacity: 0;
            transform: translateY(32px);
            transition: opacity 0.65s cubic-bezier(.22,1,.36,1), transform 0.65s cubic-bezier(.22,1,.36,1);
        }
        .reveal.delay-1 { transition-delay: 0.08s; }
        .reveal.delay-2 { transition-delay: 0.16s; }
        .reveal.delay-3 { transition-delay: 0.24s; }
        .reveal.delay-4 { transition-delay: 0.32s; }
        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Shine effect on CTA button */
        @keyframes shine {
            from { left: -100%; }
            to   { left: 200%; }
        }
        .btn-shine::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            animation: shine 2.8s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-pearl antialiased overflow-x-hidden">

<!-- ═══════════════════════════════════════════════════════════════
     NAVBAR
════════════════════════════════════════════════════════════════ -->
<header class="fixed w-full top-0 backdrop-blur-md bg-white/70 border-b border-slate-200 z-50 px-8 py-5 flex items-center justify-between">

    <!-- Logo Wordmark -->
    <div class="flex items-center gap-2">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 3C8.8203 3 3 8.8203 3 16C3 23.1797 8.8203 29 16 29C23.1797 29 29 23.1797 29 16" stroke="url(#logo-grad)" stroke-width="3" stroke-linecap="round"/>
            <path d="M16 3V16L27.2583 22.5" stroke="url(#logo-grad)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="16" cy="16" r="3.5" fill="#4F46E5"/>
            <defs>
                <linearGradient id="logo-grad" x1="3" y1="3" x2="29" y2="29" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4F46E5"/>
                    <stop offset="1" stop-color="#7C3AED"/>
                </linearGradient>
            </defs>
        </svg>
        <span style="font-family: 'Plus Jakarta Sans', sans-serif;" class="font-extrabold text-xl tracking-tight text-slate-900">
            SIKAP<span class="text-primary">HUB</span>
        </span>
    </div>

    <!-- Nav Links -->
    <nav class="flex items-center">
        <a href="/sikaphub_v2/login"
           class="text-slate-600 font-semibold hover:text-slate-900 mr-6 transition-colors duration-200">
            Log in
        </a>
        <a href="/sikaphub_v2/register"
           class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-2.5 rounded-full font-semibold transition-colors duration-200 text-sm">
            Get Started
        </a>
    </nav>
</header>


<!-- ═══════════════════════════════════════════════════════════════
     HERO SECTION
════════════════════════════════════════════════════════════════ -->
<div class="relative hero-grid-bg">
    <!-- Radial mask to fade grid out at bottom -->
    <div class="absolute inset-0 hero-radial-fade pointer-events-none"></div>
    <!-- Indigo aurora blob top-right -->
    <div class="absolute top-0 right-0 w-[700px] h-[700px] rounded-full bg-gradient-to-br from-primary/10 via-secondary/5 to-transparent blur-3xl pointer-events-none -translate-y-1/4 translate-x-1/4"></div>

    <div class="relative max-w-7xl mx-auto px-8 pt-40 pb-32 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

        <!-- LEFT: Copy -->
        <div>
            <!-- Eyebrow badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 border border-indigo-200 mb-6 reveal">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span class="text-xs font-semibold text-primary tracking-wide uppercase">AI-Powered · Guimba, Nueva Ecija</span>
            </div>

            <!-- Headline -->
            <h1 class="text-6xl font-extrabold text-slate-900 leading-tight mb-6 reveal delay-1">
                Elevating Local<br>
                Talent with
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary"> AI.</span>
            </h1>

            <!-- Subtext -->
            <p class="text-xl text-slate-500 mb-10 max-w-xl leading-relaxed reveal delay-2">
                The intelligent matching platform connecting Guimba's top professionals with verified enterprise opportunities.
            </p>

            <!-- CTA Buttons -->
            <div class="flex gap-4 flex-wrap reveal delay-3">
                <a href="/sikaphub_v2/register"
                   class="bg-primary hover:bg-indigo-700 text-white px-8 py-4 rounded-full font-semibold shadow-lg shadow-indigo-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-indigo-300 text-sm">
                    Find Opportunities
                </a>
                <a href="/sikaphub_v2/register"
                   class="border-2 border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50 px-8 py-4 rounded-full font-semibold transition-all duration-200 hover:-translate-y-0.5 text-sm">
                    Hire Talent
                </a>
            </div>

            <!-- Trust micro-line -->
            <p class="mt-8 text-xs text-slate-400 font-medium reveal delay-4">
                ✦ Free to join &nbsp;·&nbsp; PESO-verified &nbsp;·&nbsp; No spam, ever.
            </p>
        </div>

        <!-- RIGHT: Dashboard Mockup -->
        <div class="relative reveal delay-2">
            <!-- Glow halo behind image -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/10 blur-2xl scale-95 -z-10"></div>

            <!-- Floating dashboard image -->
            <img
                src="/sikaphub_v2/public/assets/images/dashboard-mockup.jpg"
                alt="Platform Dashboard"
                class="w-full rounded-2xl shadow-2xl animate-float border border-slate-100"
                onerror="this.onerror=null; this.src=''; this.outerHTML=`
                <div class='w-full aspect-[4/3] rounded-2xl border border-slate-100 shadow-2xl animate-float bg-white overflow-hidden'>
                    <div class='bg-slate-50 border-b border-slate-100 px-5 py-3 flex items-center gap-2'>
                        <span class='w-3 h-3 rounded-full bg-red-300'></span>
                        <span class='w-3 h-3 rounded-full bg-yellow-300'></span>
                        <span class='w-3 h-3 rounded-full bg-green-300'></span>
                        <span class='flex-1 mx-4 h-5 rounded-full bg-slate-100'></span>
                    </div>
                    <div class='p-6 grid grid-cols-3 gap-4'>
                        <div class='col-span-3 flex gap-4'>
                            <div class='h-20 rounded-xl bg-indigo-50 border border-indigo-100 flex-1 p-4'>
                                <div class='text-xs text-slate-400 mb-1'>AI Match Score</div>
                                <div class='text-2xl font-extrabold text-primary'>94%</div>
                            </div>
                            <div class='h-20 rounded-xl bg-violet-50 border border-violet-100 flex-1 p-4'>
                                <div class='text-xs text-slate-400 mb-1'>Applications</div>
                                <div class='text-2xl font-extrabold text-secondary'>312</div>
                            </div>
                            <div class='h-20 rounded-xl bg-emerald-50 border border-emerald-100 flex-1 p-4'>
                                <div class='text-xs text-slate-400 mb-1'>Verified Jobs</div>
                                <div class='text-2xl font-extrabold text-emerald-600'>178</div>
                            </div>
                        </div>
                        <div class='col-span-2 rounded-xl bg-slate-50 border border-slate-100 p-4 space-y-2'>
                            <div class='text-xs font-semibold text-slate-500 mb-2'>Recent Matches</div>
                            <div class='flex items-center gap-3'><div class='w-7 h-7 rounded-full bg-indigo-200'></div><div class='flex-1 h-3 rounded bg-slate-200'></div><span class='text-xs text-indigo-600 font-bold'>98%</span></div>
                            <div class='flex items-center gap-3'><div class='w-7 h-7 rounded-full bg-violet-200'></div><div class='flex-1 h-3 rounded bg-slate-200'></div><span class='text-xs text-violet-600 font-bold'>91%</span></div>
                            <div class='flex items-center gap-3'><div class='w-7 h-7 rounded-full bg-emerald-200'></div><div class='flex-1 h-3 rounded bg-slate-200'></div><span class='text-xs text-emerald-600 font-bold'>87%</span></div>
                        </div>
                        <div class='rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 p-4 flex flex-col justify-between'>
                            <div class='text-white text-xs font-semibold opacity-80'>Profile Score</div>
                            <div class='text-white text-3xl font-extrabold'>A+</div>
                            <div class='w-full bg-white/20 rounded-full h-1.5'><div class='bg-white h-1.5 rounded-full' style='width:87%'></div></div>
                        </div>
                    </div>
                </div>`;"
            >

            <!-- Floating badge: AI match -->
            <div class="absolute -bottom-4 -left-6 bg-white rounded-2xl shadow-xl border border-slate-100 px-4 py-3 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a10 10 0 1 0 10 10h-2"/><path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-medium">AI Match Score</div>
                    <div class="text-sm font-extrabold text-slate-900">94% Accuracy</div>
                </div>
            </div>

            <!-- Floating badge: users -->
            <div class="absolute -top-4 -right-4 bg-white rounded-2xl shadow-xl border border-slate-100 px-4 py-3 flex items-center gap-3">
                <div class="flex -space-x-2">
                    <div class="w-7 h-7 rounded-full bg-indigo-400 border-2 border-white"></div>
                    <div class="w-7 h-7 rounded-full bg-violet-400 border-2 border-white"></div>
                    <div class="w-7 h-7 rounded-full bg-emerald-400 border-2 border-white"></div>
                </div>
                <div>
                    <div class="text-xs text-slate-400 font-medium">Active seekers</div>
                    <div class="text-sm font-extrabold text-slate-900">2,400+</div>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════
     SOCIAL PROOF BAR
════════════════════════════════════════════════════════════════ -->
<section class="border-y border-slate-200 bg-white/50 py-14">
    <div class="max-w-5xl mx-auto px-8">

        <p class="text-center text-sm font-semibold text-slate-400 uppercase tracking-widest mb-10 reveal">
            Trusted by top local enterprises.
        </p>

        <!-- Logo Row -->
        <div class="flex flex-wrap items-center justify-center gap-10 md:gap-16">

            <!-- Logo 1: Municipal Gov -->
            <div class="opacity-40 hover:opacity-70 transition-opacity duration-300 reveal delay-1">
                <div class="flex items-center gap-2.5">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <rect width="28" height="28" rx="6" fill="#0F172A"/>
                        <path d="M14 6l7 4v8l-7 4-7-4V10z" stroke="#fff" stroke-width="1.5" fill="none"/>
                    </svg>
                    <span class="text-slate-700 font-bold text-sm tracking-tight">MunGov Guimba</span>
                </div>
            </div>

            <!-- Logo 2: PESO -->
            <div class="opacity-40 hover:opacity-70 transition-opacity duration-300 reveal delay-1">
                <div class="flex items-center gap-2.5">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <circle cx="14" cy="14" r="13" stroke="#0F172A" stroke-width="1.8"/>
                        <path d="M10 9h5a3 3 0 0 1 0 6h-5V9z" stroke="#0F172A" stroke-width="1.5" fill="none"/>
                        <path d="M10 15h6" stroke="#0F172A" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span class="text-slate-700 font-bold text-sm tracking-tight">PESO Office</span>
                </div>
            </div>

            <!-- Logo 3: NE Agri Corp -->
            <div class="opacity-40 hover:opacity-70 transition-opacity duration-300 reveal delay-2">
                <div class="flex items-center gap-2.5">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <rect width="28" height="28" rx="8" fill="#0F172A"/>
                        <path d="M7 21c2-6 8-10 14-8" stroke="#fff" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="18" cy="10" r="3" stroke="#fff" stroke-width="1.5"/>
                    </svg>
                    <span class="text-slate-700 font-bold text-sm tracking-tight">NE AgriCorp</span>
                </div>
            </div>

            <!-- Logo 4: Guimba Tech -->
            <div class="opacity-40 hover:opacity-70 transition-opacity duration-300 reveal delay-2">
                <div class="flex items-center gap-2.5">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <rect width="28" height="28" rx="8" fill="#0F172A"/>
                        <polyline points="8,18 12,10 16,15 20,8" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-slate-700 font-bold text-sm tracking-tight">Guimba TechHub</span>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     FEATURES BENTO GRID
════════════════════════════════════════════════════════════════ -->
<section class="py-32 max-w-7xl mx-auto px-8">

    <!-- Section Header -->
    <div class="text-center max-w-2xl mx-auto mb-4 reveal">
        <span class="text-xs font-bold uppercase tracking-widest text-primary mb-4 block">Platform Features</span>
        <h2 class="text-4xl font-extrabold text-slate-900 mb-4 leading-tight">
            The Engine Driving Connection.
        </h2>
        <p class="text-slate-500 text-lg leading-relaxed">
            Three pillars that make SikapHub the standard for fair, efficient, and intelligent local hiring.
        </p>
    </div>

    <!-- 3-Column Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">

        <!-- Card 1: AI Skill Matrix -->
        <div class="bg-white p-12 rounded-3xl shadow-[0_20px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:-translate-y-2 transition-transform duration-300 reveal delay-1 group">
            <!-- Icon -->
            <div class="w-14 h-14 rounded-2xl feature-icon-ring flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.663 17h4.673M12 3v1m6.364 1.636-.707.707M21 12h-1M17.636 17.636l-.707-.707M12 20v1M6.343 17.657l-.707.707M4 12H3M6.343 6.343l-.707-.707"/>
                    <circle cx="12" cy="12" r="4"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-3">AI Skill Matrix</h3>
            <p class="text-slate-500 leading-relaxed">
                Our proprietary AI engine analyzes over 50 skill dimensions and automatically scores each candidate against live job requirements — eliminating guesswork and surface-level screening.
            </p>
            <!-- Mini visual: score bars -->
            <div class="mt-8 space-y-3">
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 w-20 shrink-0">Technical</span>
                    <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary to-secondary bar-animate" style="width:88%"></div>
                    </div>
                    <span class="text-xs font-bold text-slate-600">88%</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 w-20 shrink-0">Soft Skills</span>
                    <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary to-secondary bar-animate" style="width:74%"></div>
                    </div>
                    <span class="text-xs font-bold text-slate-600">74%</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 w-20 shrink-0">Experience</span>
                    <div class="flex-1 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-primary to-secondary bar-animate" style="width:92%"></div>
                    </div>
                    <span class="text-xs font-bold text-slate-600">92%</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Strict Verification -->
        <div class="bg-white p-12 rounded-3xl shadow-[0_20px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:-translate-y-2 transition-transform duration-300 reveal delay-2 group">
            <!-- Icon -->
            <div class="w-14 h-14 rounded-2xl feature-icon-ring flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-3">Strict Verification</h3>
            <p class="text-slate-500 leading-relaxed">
                Every employer undergoes a mandatory multi-stage PESO Admin review. Only accounts with valid business permits and DTI registration gain posting access — no exceptions, no shortcuts.
            </p>
            <!-- Verification steps -->
            <div class="mt-8 space-y-3">
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="#10b981" stroke-width="1.8"><polyline points="2,5 4,7 8,3"/></svg>
                    </div>
                    <span class="text-slate-600 font-medium">DTI / SEC Registration</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="#10b981" stroke-width="1.8"><polyline points="2,5 4,7 8,3"/></svg>
                    </div>
                    <span class="text-slate-600 font-medium">Business Permit Scan</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="#10b981" stroke-width="1.8"><polyline points="2,5 4,7 8,3"/></svg>
                    </div>
                    <span class="text-slate-600 font-medium">PESO Admin Approval</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-5 h-5 rounded-full bg-violet-100 flex items-center justify-center shrink-0">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="#7C3AED" stroke-width="1.8"><path d="M5 2v3l2 2"/><circle cx="5" cy="5" r="3.5" stroke="#7C3AED"/></svg>
                    </div>
                    <span class="text-slate-400 font-medium">Periodic Re-validation</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Real-Time ATS -->
        <div class="bg-white p-12 rounded-3xl shadow-[0_20px_40px_-10px_rgba(0,0,0,0.05)] border border-slate-100 hover:-translate-y-2 transition-transform duration-300 reveal delay-3 group">
            <!-- Icon -->
            <div class="w-14 h-14 rounded-2xl feature-icon-ring flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-3">Real-Time ATS</h3>
            <p class="text-slate-500 leading-relaxed">
                Track every application status the moment it changes — from submission to final decision. Candidates and employers share a single, live pipeline with zero latency updates.
            </p>
            <!-- Pipeline indicator -->
            <div class="mt-8 flex items-center gap-1.5">
                <div class="flex-1 text-center">
                    <div class="h-2 rounded-full bg-primary mb-2"></div>
                    <span class="text-xs text-slate-400">Applied</span>
                </div>
                <div class="w-4 h-px bg-slate-200"></div>
                <div class="flex-1 text-center">
                    <div class="h-2 rounded-full bg-primary/60 mb-2"></div>
                    <span class="text-xs text-slate-400">Review</span>
                </div>
                <div class="w-4 h-px bg-slate-200"></div>
                <div class="flex-1 text-center">
                    <div class="h-2 rounded-full bg-primary/30 mb-2"></div>
                    <span class="text-xs text-slate-400">Interview</span>
                </div>
                <div class="w-4 h-px bg-slate-200"></div>
                <div class="flex-1 text-center">
                    <div class="h-2 rounded-full bg-slate-100 mb-2"></div>
                    <span class="text-xs text-slate-400">Offer</span>
                </div>
            </div>
            <div class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-full px-3 py-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Live updates enabled
            </div>
        </div>

    </div>
</section>


<!-- ═══════════════════════════════════════════════════════════════
     STATS ROW
════════════════════════════════════════════════════════════════ -->
<div class="max-w-7xl mx-auto px-8 py-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-0 border border-slate-200 rounded-3xl overflow-hidden divide-x divide-y md:divide-y-0 divide-slate-200">
        <div class="bg-white px-10 py-10 text-center reveal">
            <span class="block text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary mb-2">2,400+</span>
            <span class="text-sm text-slate-400 font-medium">Active Job Seekers</span>
        </div>
        <div class="bg-white px-10 py-10 text-center reveal delay-1">
            <span class="block text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary mb-2">180+</span>
            <span class="text-sm text-slate-400 font-medium">Verified Employers</span>
        </div>
        <div class="bg-white px-10 py-10 text-center reveal delay-2">
            <span class="block text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary mb-2">94%</span>
            <span class="text-sm text-slate-400 font-medium">Match Accuracy</span>
        </div>
        <div class="bg-white px-10 py-10 text-center reveal delay-3">
            <span class="block text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary mb-2">&lt;48h</span>
            <span class="text-sm text-slate-400 font-medium">Avg. First Response</span>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════════
     CTA FOOTER
════════════════════════════════════════════════════════════════ -->
<section class="bg-gradient-to-br from-slate-900 to-primary py-32 text-center text-white overflow-hidden relative mt-20">

    <!-- Decorative orbs -->
    <div class="absolute top-0 left-1/4 w-80 h-80 rounded-full bg-white/5 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 rounded-full bg-secondary/20 blur-3xl pointer-events-none"></div>
    <!-- Grid pattern overlay -->
    <div class="absolute inset-0 opacity-5" style="background-image:linear-gradient(rgba(255,255,255,.15) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.15) 1px,transparent 1px);background-size:48px 48px;"></div>

    <div class="relative max-w-4xl mx-auto px-8">
        <!-- Eyebrow -->
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 mb-8 reveal">
            <span class="w-2 h-2 rounded-full bg-indigo-300 animate-pulse"></span>
            <span class="text-xs font-semibold text-indigo-200 tracking-wide uppercase">Join the Platform</span>
        </div>

        <h2 class="text-5xl font-extrabold mb-6 reveal delay-1 leading-tight">
            Ready to elevate your career<br class="hidden md:block"> or find top talent?
        </h2>
        <p class="text-xl text-indigo-200 mb-10 max-w-xl mx-auto leading-relaxed reveal delay-2">
            Join thousands of professionals and enterprises already building Guimba's future — one hire at a time.
        </p>

        <div class="flex flex-wrap gap-4 justify-center reveal delay-3">
            <a href="/sikaphub_v2/register"
               class="relative overflow-hidden btn-shine bg-white text-slate-900 hover:bg-slate-50 px-10 py-5 rounded-full font-bold text-lg shadow-2xl transition-all duration-200 hover:-translate-y-0.5 hover:shadow-white/20 inline-block">
                Create an Account
            </a>
            <a href="/sikaphub_v2/login"
               class="border-2 border-white/30 text-white hover:border-white/60 hover:bg-white/10 px-10 py-5 rounded-full font-bold text-lg transition-all duration-200 hover:-translate-y-0.5 inline-block">
                Sign In
            </a>
        </div>

        <!-- Footer note -->
        <p class="mt-10 text-sm text-indigo-300/70 reveal delay-4">
            Free to register &nbsp;·&nbsp; No credit card required &nbsp;·&nbsp; Powered by PESO Guimba
        </p>
    </div>
</section>

<!-- Minimal Footer -->
<footer class="bg-slate-900 border-t border-white/5 px-8 py-8 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-2">
        <svg width="22" height="22" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 3C8.8203 3 3 8.8203 3 16C3 23.1797 8.8203 29 16 29C23.1797 29 29 23.1797 29 16" stroke="url(#logo-grad-f)" stroke-width="3" stroke-linecap="round"/>
            <path d="M16 3V16L27.2583 22.5" stroke="url(#logo-grad-f)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="16" cy="16" r="3.5" fill="#4F46E5"/>
            <defs>
                <linearGradient id="logo-grad-f" x1="3" y1="3" x2="29" y2="29" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4F46E5"/>
                    <stop offset="1" stop-color="#7C3AED"/>
                </linearGradient>
            </defs>
        </svg>
        <span class="text-white font-extrabold text-sm tracking-tight">SIKAP<span class="text-primary">HUB</span></span>
    </div>
    <p class="text-slate-500 text-xs">
        &copy; <?php echo date('Y'); ?> SikapHub v2 &mdash; Municipal Employment Platform, Guimba, Nueva Ecija.
    </p>
    <div class="flex gap-6">
        <a href="/sikaphub_v2/login"    class="text-slate-500 hover:text-slate-300 text-xs transition-colors">Login</a>
        <a href="/sikaphub_v2/register" class="text-slate-500 hover:text-slate-300 text-xs transition-colors">Register</a>
    </div>
</footer>


<!-- ═══════════════════════════════════════════════════════════════
     JAVASCRIPT: Scroll Reveal
════════════════════════════════════════════════════════════════ -->
<script>
    (function () {
        'use strict';

        // Immediately show hero elements
        document.querySelectorAll('.hero .reveal, .hero ~ .reveal').forEach(el => {
            el.classList.add('is-visible');
        });

        // Intersection Observer for scroll-triggered reveals
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    })();
</script>

</body>
</html>
