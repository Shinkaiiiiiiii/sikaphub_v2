<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Registration - S.I.K.A.P. Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
        }
        .step-hidden { display: none; }
        .step-active { display: block; animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-3xl glass-panel rounded-2xl p-8">
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-indigo-900">Build Your Company Profile</h2>
            <p class="text-slate-500 mt-2">Complete these steps to access the municipal talent network.</p>
        </div>

        <!-- Wizard Progress Bar -->
        <div class="flex justify-between mb-8 relative">
            <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-200 -z-10 transform -translate-y-1/2"></div>
            <div id="progress-bar" class="absolute top-1/2 left-0 h-1 bg-indigo-600 -z-10 transform -translate-y-1/2 transition-all duration-300" style="width: 0%;"></div>

            <div class="step-indicator w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold border-4 border-white">1</div>
            <div class="step-indicator w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold border-4 border-white">2</div>
            <div class="step-indicator w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold border-4 border-white">3</div>
        </div>

        <form id="employerWizard" method="POST" action="" enctype="multipart/form-data">
            <?php echo CSRF::csrfField(); ?>

            <!-- STEP 1: Core Identity -->
            <div class="step-section step-active" data-step="1">
                <h3 class="text-xl font-bold text-slate-800 mb-4">1. Core Identity &amp; Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Company Name *</label>
                        <input type="text" name="company_name" required
                               class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Contact Person (HR/Owner) *</label>
                        <input type="text" name="contact_person" required
                               class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Company Email *</label>
                        <input type="email" name="company_email" required
                               class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Company Phone *</label>
                        <input type="text" name="company_phone" required
                               class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="btn-next bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                        Next: Location &amp; Legal
                    </button>
                </div>
            </div>

            <!-- STEP 2: Location & Legal -->
            <div class="step-section step-hidden" data-step="2">
                <h3 class="text-xl font-bold text-slate-800 mb-4">2. Location &amp; Business Verification</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-1">Specific Street Address *</label>
                        <input type="text" name="street_address" placeholder="Bldg No., Street, Subdivision, Barangay" required
                               class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Municipality / City *</label>
                        <select name="municipality_id" required
                                class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                            <option value="">Select Municipality / City...</option>
                            <?php foreach ($municipalities as $m): ?>
                                <option value="<?php echo $m['municipality_id']; ?>">
                                    <?php echo htmlspecialchars($m['municipality_name'] . ', ' . $m['province_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Postal Code *</label>
                        <input type="text" name="postal_code" required
                               class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                </div>
                <div class="mb-6 p-6 border-2 border-dashed border-indigo-300 bg-indigo-50 rounded-lg text-center">
                    <label class="block text-sm font-bold text-indigo-900 mb-2">Upload Business Permit (Required) *</label>
                    <input type="file" name="business_permit" accept=".pdf, .jpg, .jpeg, .png" required
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200">
                    <p class="text-xs text-slate-500 mt-2">Max size: 5MB. Must be valid for current year.</p>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="btn-prev bg-slate-200 text-slate-700 px-6 py-2 rounded-lg font-bold hover:bg-slate-300 transition">Back</button>
                    <button type="button" class="btn-next bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">
                        Next: Brand Polish
                    </button>
                </div>
            </div>

            <!-- STEP 3: Brand Polish -->
            <div class="step-section step-hidden" data-step="3">
                <h3 class="text-xl font-bold text-slate-800 mb-4">3. Brand Polish <span class="text-slate-400 font-normal text-base">(Optional but Recommended)</span></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Industry</label>
                        <input type="text" name="industry" placeholder="e.g., Information Technology"
                               class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Company Size</label>
                        <select name="company_size"
                                class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                            <option value="">Select Size...</option>
                            <option value="1-10 Employees">1-10 Employees</option>
                            <option value="11-50 Employees">11-50 Employees</option>
                            <option value="51-200 Employees">51-200 Employees</option>
                            <option value="200+ Employees">200+ Employees</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-1">Company Description</label>
                        <textarea name="company_description" rows="3"
                                  class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-1">Company Logo</label>
                        <input type="file" name="company_logo" accept=".jpg, .jpeg, .png, .webp"
                               class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" class="btn-prev bg-slate-200 text-slate-700 px-6 py-2 rounded-lg font-bold hover:bg-slate-300 transition">Back</button>
                    <button type="submit" class="bg-green-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-green-700 transition shadow-lg">
                        Complete Registration
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sections    = document.querySelectorAll('.step-section');
            const indicators  = document.querySelectorAll('.step-indicator');
            const progressBar = document.getElementById('progress-bar');
            let currentStep   = 0;

            function updateUI() {
                sections.forEach((sec, index) => {
                    if (index === currentStep) {
                        sec.classList.remove('step-hidden');
                        sec.classList.add('step-active');
                    } else {
                        sec.classList.add('step-hidden');
                        sec.classList.remove('step-active');
                    }
                });

                indicators.forEach((ind, index) => {
                    if (index <= currentStep) {
                        ind.classList.remove('bg-slate-200', 'text-slate-500');
                        ind.classList.add('bg-indigo-600', 'text-white');
                    } else {
                        ind.classList.remove('bg-indigo-600', 'text-white');
                        ind.classList.add('bg-slate-200', 'text-slate-500');
                    }
                });

                // Progress bar: 0% on step 0, 50% on step 1, 100% on step 2
                progressBar.style.width = (currentStep * 50) + '%';
            }

            document.querySelectorAll('.btn-next').forEach(btn => {
                btn.addEventListener('click', () => {
                    // Strict Client-Side Validation Gate
                    const inputs  = sections[currentStep].querySelectorAll('input[required], select[required]');
                    let isValid   = true;

                    inputs.forEach(input => {
                        if (!input.checkValidity()) {
                            input.reportValidity();
                            isValid = false;
                        }
                    });

                    if (isValid && currentStep < sections.length - 1) {
                        currentStep++;
                        updateUI();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            });

            document.querySelectorAll('.btn-prev').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (currentStep > 0) {
                        currentStep--;
                        updateUI();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                });
            });
        });
    </script>
</body>
</html>