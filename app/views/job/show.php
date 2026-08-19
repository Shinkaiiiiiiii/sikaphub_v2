<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($job['job_title'] ?? 'Job Details'); ?> - S.I.K.A.P. Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col">
    <!-- Navigation Bar -->
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="/sikaphub_v2/dashboard" class="text-xl font-bold text-indigo-600">S.I.K.A.P. Hub</a>
            <a href="/sikaphub_v2/dashboard" class="text-sm text-slate-600 hover:text-indigo-600">← Back to Dashboard</a>
        </div>
    </nav>

    <!-- Job Details Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-grow w-full">
        <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900"><?php echo htmlspecialchars($job['job_title'] ?? ''); ?></h1>
                    <p class="text-indigo-600 font-medium"><?php echo htmlspecialchars($job['company_name'] ?? ''); ?></p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <?php echo htmlspecialchars($job['job_status'] ?? 'Open'); ?>
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-8 p-4 bg-slate-50 rounded-xl border border-slate-100 text-sm">
                <div>
                    <span class="text-slate-400 block text-xs">Location</span>
                    <span class="font-semibold text-slate-700"><?php echo htmlspecialchars($job['municipality_name'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-xs">Job Type</span>
                    <span class="font-semibold text-slate-700"><?php echo htmlspecialchars($job['employment_type'] ?? 'N/A'); ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-xs">Salary Range</span>
                    <span class="font-semibold text-slate-700">₱<?php echo htmlspecialchars($job['salary_range'] ?? 'N/A'); ?></span>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 mb-2">Job Description</h2>
                    <p class="text-slate-600 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($job['job_description'] ?? 'No description provided.'); ?></p>
                </div>

                <?php if (!empty($job['company_description'])): ?>
                <div class="border-t border-slate-100 pt-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-2">About the Company</h2>
                    <p class="text-slate-600 leading-relaxed"><?php echo htmlspecialchars($job['company_description']); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <div class="border-t border-slate-100 mt-8 pt-6 flex justify-between items-center">
                <a href="/sikaphub_v2/dashboard" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Back</a>
                <form method="POST" action="/sikaphub_v2/apply">
                    <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($job['job_id'] ?? 0); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl transition-colors shadow-sm">
                        1-Click Apply
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>