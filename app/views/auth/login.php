<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — SIKAPHU B</title>
    <meta name="description" content="Sign in to SIKAPHU B — Connecting elite talent with verified municipal enterprises.">
    <script src="https://cdn.tailwindcss.com"></script>
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .input-field:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.5;
        }
    </style>
</head>

<body class="bg-white">

    <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">

        <!-- ===== LEFT COLUMN: FORM ===== -->
        <div class="flex flex-col justify-center px-8 sm:px-16 md:px-24 lg:px-16 xl:px-24 relative py-16">

            <!-- Back to Home -->
            <a href="/sikaphub_v2/"
               class="absolute top-8 left-8 flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-900 transition-colors font-medium group">
                <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>

            <!-- Logo Wordmark -->
            <div class="flex items-center gap-2 mb-12">
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

            <!-- Page Header -->
            <h2 class="text-3xl font-extrabold text-slate-900">Welcome back.</h2>
            <p class="text-slate-500 mt-2">Please enter your details to sign in.</p>

            <!-- Error Display -->
            <?php if (isset($data['error'])): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-lg text-sm font-semibold mb-6 mt-6 flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <?php echo htmlspecialchars($data['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="/sikaphub_v2/login" method="POST">
                <?php echo CSRF::csrfField(); ?>

                <div>
                    <label for="username" class="block text-sm font-semibold text-slate-700 mb-2 mt-4">
                        Username
                    </label>
                    <input
                        id="username"
                        type="text"
                        name="username"
                        required
                        autocomplete="username"
                        placeholder="Enter your username"
                        value="<?php echo isset($data['username']) ? htmlspecialchars($data['username']) : ''; ?>"
                        class="input-field w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-slate-50 focus:bg-white text-slate-900 placeholder-slate-400"
                    >
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2 mt-4">
                        <label for="password" class="block text-sm font-semibold text-slate-700">
                            Password
                        </label>
                    </div>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="input-field w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none bg-slate-50 focus:bg-white text-slate-900 placeholder-slate-400"
                    >
                </div>

                <button
                    type="submit"
                    id="login-submit-btn"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 rounded-xl transition-colors mt-8 text-sm tracking-wide"
                >
                    Sign In
                </button>
            </form>

            <!-- Register Link -->
            <p class="text-sm text-slate-500 mt-6 text-center">
                Don't have an account?
                <a href="/sikaphub_v2/register" class="text-primary font-bold hover:underline">Register</a>
            </p>

        </div>

        <!-- ===== RIGHT COLUMN: BRANDING ===== -->
        <div class="hidden lg:flex relative bg-slate-900 overflow-hidden items-center justify-center">

            <!-- Mesh Gradient Blobs -->
            <div class="blob w-96 h-96 bg-primary top-[-80px] left-[-60px]"></div>
            <div class="blob w-80 h-80 bg-secondary bottom-[-60px] right-[-40px]"></div>
            <div class="blob w-64 h-64 bg-primary bottom-[20%] left-[10%]" style="opacity: 0.25;"></div>
            <div class="blob w-48 h-48 bg-secondary top-[30%] right-[5%]" style="opacity: 0.2;"></div>

            <!-- Glassmorphism Card -->
            <div class="backdrop-blur-md bg-white/10 border border-white/20 p-12 rounded-3xl max-w-md relative z-10 text-white shadow-2xl mx-8">

                <!-- Quote Icon -->
                <div class="mb-6">
                    <svg class="w-10 h-10 text-white/40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                </div>

                <p class="text-xl font-semibold leading-relaxed text-white/90 mb-8">
                    Connecting elite talent with verified municipal enterprises. Build your future here.
                </p>

                <!-- Divider -->
                <div class="border-t border-white/20 pt-8">
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-2">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary to-secondary border-2 border-white/20 flex items-center justify-center text-xs font-bold">A</div>
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 border-2 border-white/20 flex items-center justify-center text-xs font-bold">B</div>
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 border-2 border-white/20 flex items-center justify-center text-xs font-bold">C</div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Join thousands of professionals</p>
                            <p class="text-xs text-white/50">Verified municipal network</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>

</html>