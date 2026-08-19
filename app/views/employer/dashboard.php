<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer ATS Dashboard – S.I.K.A.P. Hub</title>
    <meta name="description" content="Manage your job postings and review AI-ranked candidates on the S.I.K.A.P. Hub Employer ATS Dashboard.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* ── Toast animations ── */
        #toast-container { position: fixed; top: 1.25rem; right: 1.25rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.625rem; }
        .toast {
            display: flex; align-items: center; gap: 0.625rem;
            padding: 0.875rem 1.25rem; border-radius: 0.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,.15);
            color: #fff; font-weight: 600; font-size: 0.875rem;
            opacity: 0; transform: translateX(120%);
            animation: slideIn 0.35s cubic-bezier(.22,1,.36,1) forwards,
                       fadeOut 0.35s ease forwards 4.65s;
        }
        .toast.success { background: #1e293b; border-left: 5px solid #10b981; }
        .toast.error   { background: #1e293b; border-left: 5px solid #f43f5e; }
        @keyframes slideIn { to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeOut { to { opacity: 0; transform: translateX(120%); } }

        /* ── Subtle card hover lift ── */
        .job-card { transition: box-shadow 0.2s ease, transform 0.2s ease; }
        .job-card:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,.10); }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">

    <!-- ═══════════════════════════════════════════════════ NAVBAR -->
    <nav class="bg-white border-b border-slate-100 shadow-sm sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
            <a href="/sikaphub_v2/employer/dashboard" class="flex items-center gap-2">
                <span class="text-indigo-600 font-extrabold text-xl tracking-tight">S.I.K.A.P.</span>
                <span class="text-slate-500 font-medium text-sm">Hub</span>
            </a>
            <div class="flex items-center gap-6">
                <a href="/sikaphub_v2/employer/dashboard"
                   class="text-sm font-semibold text-indigo-600 border-b-2 border-indigo-600 pb-0.5">
                   Dashboard
                </a>
                <a href="/sikaphub_v2/build-profile"
                   class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">
                   Company Profile
                </a>
                <a href="/sikaphub_v2/logout"
                   class="text-sm font-medium text-rose-500 hover:text-rose-700 transition-colors">
                   Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- ═══════════════════════════════════════════════════ TOAST CONTAINER -->
    <div id="toast-container"></div>

    <main class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        <!-- ═══════════════════════════════════════════════ VERIFICATION BANNER -->
        <?php if (($verified_status ?? 'Pending') !== 'Verified'): ?>
        <div role="alert"
             class="bg-amber-50 border-l-4 border-amber-500 text-amber-700 p-4 rounded-r-xl flex items-start gap-3 shadow-sm">
            <span class="text-xl mt-0.5 flex-shrink-0">⚠️</span>
            <div class="text-sm leading-relaxed">
                <p class="font-bold text-amber-800 mb-0.5">Account Under Review</p>
                <p>Your business permit is currently pending verification by PESO.
                   You cannot post jobs until your account is approved.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- ═══════════════════════════════════════════════ ATS HEADER CARD -->
        <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-violet-600 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-lg">
            <div class="text-white">
                <h1 class="text-2xl font-extrabold tracking-tight mb-1">Employer ATS Dashboard</h1>
                <p class="text-indigo-200 text-sm">Manage your job postings and review AI-ranked candidates.</p>
            </div>

            <?php if (($verified_status ?? 'Pending') === 'Verified'): ?>
                <a href="/sikaphub_v2/post-job"
                   id="btn-post-job"
                   class="inline-flex items-center gap-2 bg-white text-indigo-700 font-bold text-sm px-5 py-2.5 rounded-xl shadow hover:bg-indigo-50 active:scale-95 transition-all whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Post New Job
                </a>
            <?php else: ?>
                <span id="btn-post-job"
                      title="Account pending verification"
                      class="inline-flex items-center gap-2 bg-slate-300 text-slate-500 font-bold text-sm px-5 py-2.5 rounded-xl cursor-not-allowed pointer-events-none whitespace-nowrap select-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Post New Job
                </span>
            <?php endif; ?>
        </div>

        <!-- ═══════════════════════════════════════════════ JOB CARDS FEED -->
        <section class="space-y-5">

            <?php if (empty($jobs)): ?>
                <!-- Empty State -->
                <div class="job-card bg-white border border-dashed border-slate-200 rounded-2xl p-12 flex flex-col items-center justify-center text-center gap-3">
                    <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-400 text-2xl">📋</div>
                    <p class="font-semibold text-slate-600">No Job Postings Yet</p>
                    <p class="text-sm text-slate-400">Post your first opportunity to start receiving AI-ranked applications.</p>
                </div>

            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                <div class="job-card bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">

                    <!-- Job Card Header -->
                    <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h2 class="text-base font-bold text-slate-800">
                                <?php echo htmlspecialchars($job['job_title']); ?>
                            </h2>
                            <?php
                                $statusColor = match(strtolower($job['job_status'] ?? '')) {
                                    'open'   => 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
                                    'closed' => 'bg-rose-50 text-rose-600 ring-1 ring-rose-200',
                                    default  => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
                                };
                            ?>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $statusColor; ?>">
                                <?php echo htmlspecialchars($job['job_status']); ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 font-medium shrink-0">
                            Posted: <?php echo date('M d, Y', strtotime($job['date_posted'])); ?>
                        </p>
                    </div>

                    <!-- Applicants Section -->
                    <div class="px-6 py-5">
                        <?php if (empty($job['applicants'])): ?>
                            <p class="text-sm text-slate-400 italic py-4 text-center">No applications received yet.</p>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-sm">
                                    <thead>
                                        <tr class="text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                            <th class="pb-3 pr-4">Candidate</th>
                                            <th class="pb-3 pr-4">Contact</th>
                                            <th class="pb-3 pr-4">AI Match</th>
                                            <th class="pb-3 pr-4">Status</th>
                                            <th class="pb-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        <?php foreach ($job['applicants'] as $applicant): ?>
                                            <?php
                                                // ── Score colour-coding ──────────────────────────────
                                                $pct = $applicant['match_percentage'];
                                                if ($pct >= 75) {
                                                    $scoreClass = 'text-emerald-600 font-bold bg-emerald-50 px-2 py-1 rounded';
                                                } elseif ($pct >= 40) {
                                                    $scoreClass = 'text-amber-600 font-bold bg-amber-50 px-2 py-1 rounded';
                                                } else {
                                                    $scoreClass = 'text-rose-600 font-bold bg-rose-50 px-2 py-1 rounded';
                                                }

                                                // ── Application-status pill ──────────────────────────
                                                $appStatusColor = match(strtolower($applicant['application_status'] ?? '')) {
                                                    'accepted' => 'bg-emerald-50 text-emerald-700',
                                                    'rejected' => 'bg-rose-50 text-rose-600',
                                                    'reviewed' => 'bg-blue-50 text-blue-600',
                                                    default    => 'bg-slate-100 text-slate-500',
                                                };
                                            ?>
                                            <tr class="hover:bg-slate-50/70 transition-colors">
                                                <td class="py-3.5 pr-4 font-medium text-slate-800">
                                                    <?php echo htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']); ?>
                                                </td>
                                                <td class="py-3.5 pr-4 text-slate-500">
                                                    <?php echo htmlspecialchars($applicant['contact_number'] ?? 'Not provided'); ?>
                                                </td>
                                                <td class="py-3.5 pr-4">
                                                    <span class="<?php echo $scoreClass; ?> text-xs">
                                                        <?php echo $pct; ?>%
                                                    </span>
                                                </td>
                                                <td class="py-3.5 pr-4">
                                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?php echo $appStatusColor; ?>">
                                                        <?php echo htmlspecialchars($applicant['application_status']); ?>
                                                    </span>
                                                </td>
                                                <td class="py-3.5">
                                                    <a href="/sikaphub_v2/employer/review-candidate?app_id=<?php echo (int) $applicant['application_id']; ?>"
                                                       class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-xs font-semibold px-3.5 py-2 rounded-lg transition-all shadow-sm">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Review Profile
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </section>
    </main>

    <!-- ═══════════════════════════════════════════════════ TOAST SCRIPT -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            let message = '';
            let type    = 'success';

            if (urlParams.has('job_posted')) {
                message = "✅  Job Opportunity successfully posted!";
            } else if (urlParams.has('profile_updated')) {
                message = "✅  Company Profile updated successfully!";
            } else if (urlParams.has('error')) {
                type    = 'error';
                message = "⚠️  An error occurred: " + urlParams.get('error').replace(/_/g, ' ');
            }

            if (message !== '') {
                const container = document.getElementById('toast-container');
                const div       = document.createElement('div');
                div.className   = `toast ${type}`;
                div.textContent = message;
                container.appendChild(div);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

</body>
</html>