<?php
// Ensure this view receives $master_skills, $municipalities, and $existing_profile from the Controller
$p = $existing_profile ?? [];

// Pre-extract core preferences safely (Standardizing to lowercase for strict comparisons)
$firstName = htmlspecialchars($p['first_name'] ?? '');
$lastName = htmlspecialchars($p['last_name'] ?? '');
$desiredJobType = htmlspecialchars($p['desired_job_type'] ?? '');
$expectedSalary = isset($p['expected_salary']) ? htmlspecialchars($p['expected_salary']) : '';
$workSetup = htmlspecialchars($p['preferred_work_setup'] ?? 'On-site');
$homeMunicipalityId = isset($p['home_municipality_id']) ? (int) $p['home_municipality_id'] : '';
$visibility = strtolower($p['profile_visibility'] ?? 'public');
$prefLocs = $p['preferred_municipality_ids'] ?? [];

// Dynamic Arrays
$experiences = $p['experience'] ?? [];
if (empty($experiences)) {
    $experiences = [['job_title' => '', 'company_name' => '', 'start_date' => '', 'end_date' => '']];
}

$educations = $p['education'] ?? [];
if (empty($educations)) {
    $educations = [['degree_level' => '', 'school_name' => '', 'year_graduated' => '']];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Builder – S.I.K.A.P. Hub</title>
    <meta name="description" content="Complete your Job Seeker profile on S.I.K.A.P. Hub and let our AI match you with top employers in Nueva Ecija.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#7C3AED',
                        slate: { 50: '#F8FAFC', 100: '#F1F5F9', 200: '#E2E8F0', 300: '#CBD5E1', 400: '#94A3B8', 500: '#64748B', 600: '#475569', 700: '#334155', 800: '#1E293B', 900: '#0F172A' }
                    },
                    animation: { 'fade-in': 'fadeIn .4s ease forwards' },
                    keyframes: { fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } } }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(18px); }
        .bg-gradient-mesh { background: linear-gradient(135deg, #EEF2FF 0%, #F5F3FF 35%, #EDE9FE 65%, #E0E7FF 100%); background-size: 400% 400%; animation: gradientShift 12s ease infinite; }
        @keyframes gradientShift { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .step-panel { animation: fadeIn .35s ease forwards; }
        
        .radio-card input[type="radio"] { display: none; }
        .radio-card input[type="radio"]+label { display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.125rem; border: 2px solid #E2E8F0; border-radius: 0.875rem; cursor: pointer; transition: all .2s; background: #fff; }
        .radio-card input[type="radio"]:checked+label { border-color: #4F46E5; background: #EEF2FF; color: #4F46E5; font-weight: 600; }
        
        .step-connector { flex: 1; height: 2px; background: #E2E8F0; transition: background .4s; }
        .step-connector.active { background: linear-gradient(90deg, #4F46E5, #7C3AED); }
        
        .field-base { width: 100%; padding: 0.75rem 1rem; border: 2px solid #E2E8F0; border-radius: 0.75rem; font-size: 0.95rem; transition: border-color .2s, box-shadow .2s; background: #fff; outline: none; }
        .field-base:focus { border-color: #4F46E5; box-shadow: 0 0 0 4px rgba(79, 70, 229, .12); }
        .btn-primary-grad { background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); transition: transform .2s, box-shadow .2s; }
        .btn-primary-grad:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(79, 70, 229, .40); }
        
        select.field-base { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%2364748b'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.85rem center; background-size: 1.25rem; padding-right: 2.75rem; }
        .ts-control { padding: 0.75rem 1rem !important; border: 2px solid #E2E8F0 !important; border-radius: 0.75rem !important; font-size: 0.95rem !important; box-shadow: none !important; transition: border-color .2s !important; }
        .ts-control.focus { border-color: #4F46E5 !important; box-shadow: 0 0 0 4px rgba(79, 70, 229, .12) !important; }
        .ts-dropdown { border-radius: 0.75rem !important; border: 1px solid #E2E8F0 !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
    </style>
</head>
<body class="bg-gradient-mesh min-h-screen">

    <div class="max-w-3xl mx-auto my-10 px-4 sm:px-6 animate-fade-in">
        <div class="flex items-center gap-3 mb-8">
            <a href="/sikaphub_v2" class="flex items-center gap-2 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1 1 .03 2.713-1.31 2.569l-4.94-.85a.75.75 0 00-.304 0l-4.94.85c-1.34.144-2.31-1.569-1.31-2.569L5 14.5" /></svg>
                </div>
                <span class="text-slate-800 font-bold text-lg tracking-tight">S.I.K.A.P. <span class="text-primary">Hub</span></span>
            </a>
            <div class="ml-auto text-sm text-slate-500 font-medium">Profile Onboarding</div>
        </div>

        <div class="glass-card rounded-3xl shadow-2xl border border-slate-100 p-8 lg:p-12">
            <div class="mb-8">
                <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900">Build Your Profile</h1>
                <p class="text-slate-500 mt-1 text-sm">Let our AI match you with the best opportunities in Nueva Ecija.</p>
            </div>

            <!-- Progress Bar -->
            <div class="flex items-center mb-10" id="progress-bar">
                <div class="flex flex-col items-center" id="prog-step-1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-md bg-gradient-to-br from-primary to-secondary text-white ring-4 ring-indigo-100">
                        <span class="step-num">1</span><svg class="w-5 h-5 hidden check-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </div>
                    <span class="text-xs font-semibold text-primary mt-1.5">Identity</span>
                </div>
                <div class="step-connector mx-2" id="connector-1-2"></div>
                <div class="flex flex-col items-center" id="prog-step-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow bg-slate-200 text-slate-400">
                        <span class="step-num">2</span><svg class="w-5 h-5 hidden check-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-1.5">Experience</span>
                </div>
                <div class="step-connector mx-2" id="connector-2-3"></div>
                <div class="flex flex-col items-center" id="prog-step-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow bg-slate-200 text-slate-400">
                        <span class="step-num">3</span><svg class="w-5 h-5 hidden check-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-1.5">Skills &amp; Finish</span>
                </div>
            </div>

            <form method="POST" action="/sikaphub_v2/build-profile" enctype="multipart/form-data" id="profile-wizard-form" novalidate>
                <?php echo CSRF::csrfField(); ?>

                <!-- STEP 1 – IDENTITY -->
                <div id="step-1" class="step-panel">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center"><svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg></div>
                        <div><h2 class="text-base font-bold text-slate-800">Identity &amp; Privacy</h2></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required value="<?php echo $firstName; ?>" class="field-base">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required value="<?php echo $lastName; ?>" class="field-base">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Home Location *</label>
                        <select name="home_municipality_id" required class="field-base bg-white">
                            <option value="">— Select Home Municipality/City —</option>
                            <?php
                            $currentProvince = '';
                            foreach ($municipalities as $mun) {
                                if ($currentProvince !== $mun['province_name']) {
                                    if ($currentProvince !== '') echo '</optgroup>';
                                    $currentProvince = $mun['province_name'];
                                    echo '<optgroup label="' . htmlspecialchars($currentProvince) . '">';
                                }
                                $selected = ($mun['municipality_id'] == $homeMunicipalityId) ? 'selected' : '';
                                echo '<option value="' . $mun['municipality_id'] . '" ' . $selected . '>' . htmlspecialchars($mun['municipality_name']) . '</option>';
                            }
                            if ($currentProvince !== '') echo '</optgroup>';
                            ?>
                        </select>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Profile Visibility</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="radio-card">
                                <input type="radio" id="vis_public" name="profile_visibility" value="public" <?php echo ($visibility === 'public' || $visibility === '') ? 'checked' : ''; ?>>
                                <label for="vis_public"><span class="text-lg">🌐</span><div><div class="text-sm font-semibold">Public</div><div class="text-xs text-slate-400">All employers</div></div></label>
                            </div>
                            <div class="radio-card">
                                <input type="radio" id="vis_limited" name="profile_visibility" value="limited" <?php echo $visibility === 'limited' ? 'checked' : ''; ?>>
                                <label for="vis_limited"><span class="text-lg">🔒</span><div><div class="text-sm font-semibold">Limited</div><div class="text-xs text-slate-400">Verified only</div></div></label>
                            </div>
                            <div class="radio-card">
                                <input type="radio" id="vis_hidden" name="profile_visibility" value="hidden" <?php echo $visibility === 'hidden' ? 'checked' : ''; ?>>
                                <label for="vis_hidden"><span class="text-lg">🫥</span><div><div class="text-sm font-semibold">Hidden</div><div class="text-xs text-slate-400">Only me</div></div></label>
                            </div>
                        </div>
                    </div>

                    <button type="button" onclick="goToStep(2)" class="btn-primary-grad w-full py-3.5 rounded-2xl text-white font-bold text-base flex items-center justify-center gap-2 shadow-lg">Next: Experience</button>
                </div>

                <!-- STEP 2 – EXPERIENCE & EDUCATION -->
                <div id="step-2" class="hidden step-panel">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center"><svg class="w-4 h-4 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/></svg></div>
                        <div><h3 class="text-2xl font-bold text-slate-800">Experience &amp; Education</h3></div>
                    </div>

                    <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 mb-6">
                        <div><p class="text-sm font-semibold text-slate-700">I have work experience</p></div>
                        <label for="experience-toggle" class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="experience-toggle" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-primary peer-checked:to-secondary"></div>
                        </label>
                    </div>

                    <div id="experience-container">
                        <div class="flex items-center gap-2 mb-3"><span class="text-sm font-semibold text-slate-700">Work Experience</span></div>
                        
                        <?php foreach ($experiences as $index => $exp): ?>
                        <div class="experience-row bg-white border border-slate-200 rounded-2xl p-5 mb-4 relative">
                            <?php if ($index > 0): ?>
                            <div class="flex justify-end mb-2">
                                <button type="button" class="text-xs text-red-500 font-semibold hover:text-red-700 transition-colors flex items-center gap-1 btn-remove-row">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Remove
                                </button>
                            </div>
                            <?php endif; ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Job Title</label>
                                    <input type="text" name="experience[<?php echo $index; ?>][job_title]" value="<?php echo htmlspecialchars($exp['job_title'] ?? ''); ?>" class="field-base">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Company</label>
                                    <input type="text" name="experience[<?php echo $index; ?>][company]" value="<?php echo htmlspecialchars($exp['company_name'] ?? ''); ?>" class="field-base">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Start Date</label>
                                    <input type="month" name="experience[<?php echo $index; ?>][start_date]" value="<?php echo htmlspecialchars($exp['start_date'] ?? ''); ?>" class="field-base">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">End Date</label>
                                    <input type="month" name="experience[<?php echo $index; ?>][end_date]" value="<?php echo htmlspecialchars($exp['end_date'] ?? ''); ?>" class="field-base">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <button type="button" id="add-experience-btn" class="w-full py-2.5 rounded-xl border-2 border-dashed border-primary text-primary text-sm font-semibold hover:bg-indigo-50 transition-colors flex items-center justify-center gap-2 mb-2">
                            Add another role
                        </button>
                    </div>

                    <div id="education-container" class="mt-8">
                        <div class="flex items-center gap-2 mb-3"><span class="text-sm font-semibold text-slate-700">Education</span></div>
                        
                        <?php foreach ($educations as $index => $edu): ?>
                        <div class="education-row bg-white border border-slate-200 rounded-2xl p-5 mb-4 relative">
                            <?php if ($index > 0): ?>
                            <div class="flex justify-end mb-2">
                                <button type="button" class="text-xs text-red-500 font-semibold hover:text-red-700 transition-colors flex items-center gap-1 btn-remove-row">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Remove
                                </button>
                            </div>
                            <?php endif; ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Degree / Level</label>
                                    <select name="education[<?php echo $index; ?>][degree_level]" class="field-base ts-degree">
                                        <option value="">— Select level —</option>
                                        <?php
                                        $levels = ['Elementary', 'High School', 'Vocational / TESDA', "Bachelor's Degree", "Master's Degree", 'Doctorate'];
                                        foreach ($levels as $lvl) {
                                            $sel = (($edu['degree_level'] ?? '') === $lvl) ? 'selected' : '';
                                            echo "<option value=\"$lvl\" $sel>$lvl</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">School / Institution</label>
                                    <input type="text" name="education[<?php echo $index; ?>][institution]" value="<?php echo htmlspecialchars($edu['school_name'] ?? ''); ?>" class="field-base ts-school">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Year Graduated</label>
                                    <input type="number" name="education[<?php echo $index; ?>][year_graduated]" value="<?php echo htmlspecialchars($edu['year_graduated'] ?? ''); ?>" class="field-base">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <button type="button" id="add-education-btn" class="w-full py-2.5 rounded-xl border-2 border-dashed border-secondary text-secondary text-sm font-semibold hover:bg-violet-50 transition-colors flex items-center justify-center gap-2 mb-2">
                            Add education
                        </button>
                    </div>

                    <div class="flex items-center gap-3 mt-10">
                        <button type="button" onclick="goToStep(1)" class="flex-1 py-3.5 rounded-2xl border-2 border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 hover:bg-slate-50 transition-all flex items-center justify-center gap-2">Back</button>
                        <button type="button" onclick="goToStep(3)" class="btn-primary-grad flex-[2] py-3.5 rounded-2xl text-white font-bold text-sm flex items-center justify-center gap-2 shadow-lg">Next: Skills</button>
                    </div>
                </div>

                <!-- STEP 3 – SKILLS & PREFERENCES -->
                <div id="step-3" class="hidden step-panel">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center"><svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5zM12 6.75a5.25 5.25 0 100 10.5 5.25 5.25 0 000-10.5zM12 11.25a.75.75 0 110 1.5.75.75 0 010-1.5z"/></svg></div>
                        <div><h3 class="text-2xl font-bold text-slate-800">Skills &amp; Preferences</h3></div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Core Skills *</label>
                        <input type="text" id="skills" name="skills" value="<?php echo htmlspecialchars(implode(', ', $p['custom_skills'] ?? [])); ?>" class="field-base">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Desired Job Type</label>
                            <select id="desired_job_type" name="desired_job_type" class="field-base">
                                <option value="" disabled <?php echo empty($desiredJobType) ? 'selected' : ''; ?>>— Select type —</option>
                                <?php foreach (['Full-Time', 'Part-Time', 'Contract', 'Freelance'] as $jt): ?>
                                    <option value="<?php echo $jt; ?>" <?php echo $desiredJobType === $jt ? 'selected' : ''; ?>><?php echo $jt; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Preferred Work Setup</label>
                            <select id="preferred_work_setup" name="preferred_work_setup" class="field-base">
                                <option value="" disabled <?php echo empty($workSetup) ? 'selected' : ''; ?>>— Select setup —</option>
                                <?php foreach (['On-site', 'Hybrid', 'Remote'] as $ws): ?>
                                    <option value="<?php echo $ws; ?>" <?php echo $workSetup === $ws ? 'selected' : ''; ?>><?php echo $ws; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Expected Monthly Salary</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><span class="text-sm font-bold text-slate-400">PHP</span></div>
                                <input type="number" name="expected_salary" value="<?php echo $expectedSalary; ?>" min="0" class="field-base pl-14">
                            </div>
                        </div>

                        <div class="col-span-1 md:col-span-2 mt-4 mb-6">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Preferred Work Locations *</label>
                            <select id="preferred_locations" name="preferred_municipality_ids[]" multiple required class="field-base">
                                <?php
                                $currentProvincePref = '';
                                foreach ($municipalities as $mun) {
                                    if ($currentProvincePref !== $mun['province_name']) {
                                        if ($currentProvincePref !== '') echo '</optgroup>';
                                        $currentProvincePref = $mun['province_name'];
                                        echo '<optgroup label="' . htmlspecialchars($currentProvincePref) . '">';
                                    }
                                    $selectedPref = in_array($mun['municipality_id'], $prefLocs) ? 'selected' : '';
                                    echo '<option value="' . $mun['municipality_id'] . '" ' . $selectedPref . '>' . htmlspecialchars($mun['municipality_name']) . '</option>';
                                }
                                if ($currentProvincePref !== '') echo '</optgroup>';
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-12 pt-6 border-t border-slate-100">
                        <button type="button" onclick="goToStep(2)" class="py-3 px-6 rounded-2xl border-2 border-slate-200 text-slate-600 font-bold text-sm hover:border-slate-300 hover:bg-slate-50 transition-all flex items-center gap-2">Back</button>
                        <button type="submit" class="btn-primary-grad flex-1 ml-4 py-4 rounded-2xl text-white font-extrabold text-base flex items-center justify-center gap-2.5 shadow-xl">Complete Profile &amp; Activate Account</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        'use strict';
        const TOTAL_STEPS = 3;
        let currentStep  = 1;
        const panels = { 1: document.getElementById('step-1'), 2: document.getElementById('step-2'), 3: document.getElementById('step-3') };
        const progSteps = { 1: document.getElementById('prog-step-1'), 2: document.getElementById('prog-step-2'), 3: document.getElementById('prog-step-3') };
        const connectors = { '1-2': document.getElementById('connector-1-2'), '2-3': document.getElementById('connector-2-3') };

        function setStepBubbleState(stepNum, state) {
            const bubble  = progSteps[stepNum].querySelector('div');
            const numSpan = bubble.querySelector('.step-num');
            const checkIcon = bubble.querySelector('.check-icon');
            bubble.className = bubble.className.replace(/bg-\S+|text-\S+|ring-\S+|from-\S+|to-\S+/g, '').trim();

            if (state === 'active') {
                bubble.classList.add('bg-gradient-to-br', 'from-primary', 'to-secondary', 'text-white', 'ring-4', 'ring-indigo-100');
                numSpan.classList.remove('hidden'); checkIcon.classList.add('hidden');
                progSteps[stepNum].querySelector('span').classList.replace('text-slate-400', 'text-primary');
                progSteps[stepNum].querySelector('span').classList.replace('font-medium', 'font-semibold');
            } else if (state === 'done') {
                bubble.classList.add('bg-gradient-to-br', 'from-primary', 'to-secondary', 'text-white');
                numSpan.classList.add('hidden'); checkIcon.classList.remove('hidden');
                progSteps[stepNum].querySelector('span').classList.replace('text-slate-400', 'text-primary');
            } else {
                bubble.classList.add('bg-slate-200', 'text-slate-400');
                numSpan.classList.remove('hidden'); checkIcon.classList.add('hidden');
                progSteps[stepNum].querySelector('span').classList.replace('text-primary', 'text-slate-400');
                progSteps[stepNum].querySelector('span').classList.replace('font-semibold', 'font-medium');
            }
        }

        function updateProgressBar(targetStep) {
            for (let s = 1; s <= TOTAL_STEPS; s++) {
                if (s < targetStep) setStepBubbleState(s, 'done');
                else if (s === targetStep) setStepBubbleState(s, 'active');
                else setStepBubbleState(s, 'idle');
            }
            connectors['1-2'].classList.toggle('active', targetStep > 1);
            connectors['2-3'].classList.toggle('active', targetStep > 2);
        }

        window.goToStep = function (targetStep) {
            if (targetStep < 1 || targetStep > TOTAL_STEPS) return;
            if (currentStep === 1 && targetStep > 1) {
                const firstName = document.getElementById('first_name');
                const lastName  = document.getElementById('last_name');
                let valid = true;
                [firstName, lastName].forEach(function (el) {
                    if (!el.value.trim()) {
                        el.classList.add('border-red-400');
                        el.classList.remove('border-slate-200');
                        valid = false;
                    } else el.classList.remove('border-red-400');
                });
                if (!valid) return;
            }
            panels[currentStep].classList.add('hidden');
            panels[currentStep].classList.remove('step-panel');
            panels[targetStep].classList.remove('hidden');
            panels[targetStep].classList.add('step-panel');
            currentStep = targetStep;
            updateProgressBar(currentStep);
            document.getElementById('progress-bar').scrollIntoView({ behavior: 'smooth', block: 'start' });
        };

        ['first_name', 'last_name'].forEach(function (id) {
            const el = document.getElementById(id);
            if (el) el.addEventListener('input', function () { el.classList.remove('border-red-400'); });
        });

        updateProgressBar(1);

        // Experience Toggle Logic
        const expToggle = document.getElementById('experience-toggle');
        const expContainer = document.getElementById('experience-container');
        if (expToggle && expContainer) {
            expToggle.addEventListener('change', function () {
                if (this.checked) expContainer.classList.remove('hidden');
                else {
                    expContainer.classList.add('hidden');
                    expContainer.querySelectorAll('input').forEach(inp => inp.value = '');
                }
            });
        }

        // Event Delegation for dynamically removing rows
        ['experience-container', 'education-container'].forEach(containerId => {
            const container = document.getElementById(containerId);
            if(container) {
                container.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.btn-remove-row');
                    if (removeBtn) removeBtn.closest('div[class*="-row"]').remove();
                });
            }
        });

        // 🚨 SURGICAL FIX: STRICT DOM EXTRACTION 🚨
        function registerDynamicRows(btnId, containerId, rowSelector, arrayName) {
            const btn = document.getElementById(btnId);
            const container = document.getElementById(containerId);
            if (!btn || !container) return;

            btn.addEventListener('click', function () {
                const existingRows = container.querySelectorAll(rowSelector);
                const newIndex = existingRows.length;
                const template = existingRows[0];
                const clone = template.cloneNode(true);

                // 1. Extract and restore original inputs BEFORE destroying wrappers
                clone.querySelectorAll('.tomselected').forEach(input => {
                    const wrapper = input.closest('.ts-wrapper');
                    
                    // Break the input out of the wrapper if Tom Select trapped it inside
                    if (wrapper && wrapper.parentNode) {
                        wrapper.parentNode.insertBefore(input, wrapper);
                    }

                    // Strip Tom Select injected attributes
                    input.classList.remove('tomselected', 'hidden');
                    input.removeAttribute('hidden');
                    input.removeAttribute('tabindex');
                    input.style.display = '';
                    
                    // Clear the cloned data
                    if(input.tagName === 'SELECT') input.selectedIndex = 0;
                    else input.value = '';
                });

                // 2. Now it is safe to purge the artifacts
                clone.querySelectorAll('.ts-wrapper').forEach(wrapper => wrapper.remove());

                // 3. Re-index the multidimensional array keys for PHP mapping
                clone.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(new RegExp('\\[0\\]', 'g'), '[' + newIndex + ']');
                    if (el.tagName === 'SELECT') el.selectedIndex = 0;
                    else el.value = '';
                });

                // 4. Inject Remove button (if cloning index 0 which lacks it)
                if (!clone.querySelector('.btn-remove-row')) {
                    const removeDiv = document.createElement('div');
                    removeDiv.className = 'flex justify-end mb-2';
                    removeDiv.innerHTML = '<button type="button" class="text-xs text-red-500 font-semibold hover:text-red-700 transition-colors flex items-center gap-1 btn-remove-row"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Remove</button>';
                    clone.insertBefore(removeDiv, clone.firstChild);
                }

                container.insertBefore(clone, btn);
                if (containerId === 'education-container') initEducationTaggers(clone);
            });
        }

        registerDynamicRows('add-experience-btn', 'experience-container', '.experience-row', 'experience');
        registerDynamicRows('add-education-btn',  'education-container',  '.education-row',  'education');

        function initEducationTaggers(container) {
            container.querySelectorAll('.ts-degree').forEach(el => {
                if (!el.classList.contains('tomselected')) new TomSelect(el, { placeholder: "— Select level —" });
            });
            container.querySelectorAll('.ts-school').forEach(el => {
                if (!el.classList.contains('tomselected')) new TomSelect(el, { create: true, createOnBlur: true, placeholder: "e.g., Our Lady of the Sacred Heart College" });
            });
        }
        initEducationTaggers(document.getElementById('education-container'));

        if (document.getElementById('skills')) {
            new TomSelect("#skills", { plugins: ['remove_button'], create: true, createOnBlur: true, persist: false, placeholder: 'Type a skill and press Enter...', render: { item: function(data, escape) { return '<div class="bg-indigo-100 text-indigo-800 rounded-md px-2 py-1 m-1 text-sm font-medium border border-indigo-200">' + escape(data.text) + '</div>'; } } });
        }
        if (document.getElementById('preferred_locations')) {
            new TomSelect("#preferred_locations", { plugins: ['remove_button'], placeholder: 'Search and select preferred locations...', closeAfterSelect: false, render: { item: function(data, escape) { return '<div class="bg-violet-100 text-violet-800 rounded-md px-2 py-1 m-1 text-sm font-medium border border-violet-200">' + escape(data.text) + '</div>'; } } });
        }
    })();
    </script>
</body>
</html>