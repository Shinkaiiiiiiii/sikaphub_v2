<?php
// Ensure this view receives $master_skills, $barangays, and $existing_profile from the Controller
$p = $existing_profile ?? [];

// Pre-extract core preferences safely
$desiredJobType = htmlspecialchars($p['desired_job_type'] ?? '');
$prefIndustry = htmlspecialchars($p['industry'] ?? '');
$expectedSalary = isset($p['expected_salary']) ? htmlspecialchars($p['expected_salary']) : '';
$workSetup = htmlspecialchars($p['preferred_work_setup'] ?? 'On-site');
$prefBarangay = isset($p['preferred_barangay_id']) ? (int) $p['preferred_barangay_id'] : '';

// Pre-extract file paths safely
$profilePhoto = htmlspecialchars($p['profile_photo'] ?? '');
$resumeFile = htmlspecialchars($p['resume_file'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Seeker Profile Builder - S.I.K.A.P. Hub</title>
    <style>
        :root {
            --primary: #0056b3;
            --secondary: #6c757d;
            --danger: #e74c3c;
            --background: #f4f7f6;
            --border: #ced4da;
            --readonly-bg: #e9ecef;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background);
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header-block {
            margin-bottom: 30px;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
        }

        /* Card Architecture */
        .form-section {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .form-section h3 {
            margin-top: 0;
            color: var(--primary);
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 1em;
            width: 100%;
            box-sizing: border-box;
        }

        .form-group input[readonly] {
            background-color: var(--readonly-bg);
            cursor: not-allowed;
            color: var(--secondary);
        }

        .form-group input:focus:not([readonly]),
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 86, 179, 0.2);
        }

        /* Grid Layouts for standard fields */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Button Polish */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 0.95em;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            width: 100%;
            padding: 15px;
            font-size: 1.1em;
        }

        .btn-secondary {
            background: var(--secondary);
            color: #fff;
            margin-top: 10px;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
            padding: 8px 12px;
            margin-top: 10px;
            align-self: flex-start;
        }

        /* Dynamic Row Styling */
        .repeater-row {
            border: 1px dashed var(--border);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            background: #fafafa;
            position: relative;
        }

        .skill-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        /* File Upload Styling */
        .file-upload-wrapper {
            border: 1px dashed var(--border);
            padding: 15px;
            border-radius: 4px;
            background: #fafafa;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header-block">
            <h2>Dynamic Profile Builder</h2>
            <p>Complete your profile. S.I.K.A.P. Hub's AI will use this data to match you with top employers.</p>
        </div>

        <form method="POST" action="/sikaphub_v2/build-profile" enctype="multipart/form-data" class="ai-trigger-form">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <div class="form-section">
                <h3>1. Profile Assets</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Profile Picture</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="profile_photo" accept="image/png, image/jpeg, image/webp"
                                style="border: none; background: transparent;">
                            <br><small style="color: var(--secondary);">Recommended: Square PNG/JPG, max 2MB. Leave
                                blank to keep existing.</small>
                            <?php if ($profilePhoto): ?>
                                <br><small style="color: var(--primary);">Current: <?php echo $profilePhoto; ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Professional Resume / CV</label>
                        <div class="file-upload-wrapper">
                            <input type="file" name="resume_file" accept="application/pdf"
                                style="border: none; background: transparent;">
                            <br><small style="color: var(--secondary);">Required: PDF format, max 5MB. Leave blank to
                                keep existing.</small>
                            <?php if ($resumeFile): ?>
                                <br><small style="color: var(--primary);">Current: <?php echo $resumeFile; ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>2. Job Preferences</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Desired Job Title</label>
                        <input type="text" name="desired_job_type" required placeholder="e.g., Software Engineer"
                            value="<?php echo $desiredJobType; ?>">
                    </div>
                    <div class="form-group">
                        <label>Preferred Industry</label>
                        <input type="text" name="industry" required placeholder="e.g., Information Technology"
                            value="<?php echo $prefIndustry; ?>">
                    </div>
                    <div class="form-group">
                        <label>Expected Salary (PHP/Month)</label>
                        <input type="number" name="expected_salary" step="0.01" placeholder="e.g., 30000"
                            value="<?php echo $expectedSalary; ?>">
                    </div>
                    <div class="form-group">
                        <label>Work Setup</label>
                        <select name="preferred_work_setup" required>
                            <option value="On-site" <?php echo $workSetup === 'On-site' ? 'selected' : ''; ?>>On-site
                            </option>
                            <option value="Remote" <?php echo $workSetup === 'Remote' ? 'selected' : ''; ?>>Remote
                            </option>
                            <option value="Hybrid" <?php echo $workSetup === 'Hybrid' ? 'selected' : ''; ?>>Hybrid
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Preferred Target Location (Barangay)</label>
                    <select name="preferred_barangay_id">
                        <option value="">Anywhere / Flexible</option>
                        <?php if (!empty($barangays)):
                            foreach ($barangays as $b): ?>
                                <option value="<?php echo $b['barangay_id']; ?>" <?php echo $prefBarangay === (int) $b['barangay_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b['barangay_name']); ?>
                                </option>
                            <?php endforeach; endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-section" id="education-wrapper">
                <h3>3. Education History</h3>
                <div id="education-container">
                    <div class="repeater-row edu-row">
                        <div class="grid-2">
                            <div class="form-group">
                                <label>Degree / Attainment Level</label>
                                <input type="text" name="education[0][degree_level]"
                                    placeholder="e.g., BS Information Technology">
                            </div>
                            <div class="form-group">
                                <label>Institution Name</label>
                                <input type="text" name="education[0][school_name]"
                                    placeholder="e.g., Guimba National High School">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Year Graduated</label>
                            <input type="number" name="education[0][year_graduated]" placeholder="YYYY">
                        </div>
                    </div>
                </div>
                <button type="button" id="btn-add-edu" class="btn btn-secondary">+ Add Another Degree</button>
            </div>

            <div class="form-section" id="experience-wrapper">
                <h3>4. Work Experience</h3>
                <div id="experience-container">
                    <div class="repeater-row exp-row">
                        <div class="grid-2">
                            <div class="form-group">
                                <label>Job Title</label>
                                <input type="text" name="experience[0][job_title]"
                                    placeholder="e.g., Junior Web Developer">
                            </div>
                            <div class="form-group">
                                <label>Company Name</label>
                                <input type="text" name="experience[0][company_name]" placeholder="Company LLC">
                            </div>
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="experience[0][start_date]">
                            </div>
                            <div class="form-group">
                                <label>End Date (Leave blank if present)</label>
                                <input type="date" name="experience[0][end_date]">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Job Description & Achievements</label>
                            <textarea name="experience[0][job_description]" rows="3"
                                placeholder="Describe your responsibilities..."></textarea>
                        </div>
                    </div>
                </div>
                <button type="button" id="btn-add-exp" class="btn btn-secondary">+ Add Another Experience</button>
            </div>

            <div class="form-section">
                <h3>5. AI Skill Matrix</h3>
                <p style="font-size: 0.9em; color: var(--secondary);">Select verified skills from the dictionary to
                    optimize your AI match scores.</p>

                <div class="skill-grid">
                    <?php if (!empty($master_skills)):
                        foreach ($master_skills as $skill): ?>
                            <label style="display: flex; align-items: center; gap: 5px; font-size: 0.9em;">
                                <input type="checkbox" name="skills[]" value="<?php echo $skill['skill_id']; ?>">
                                <?php echo htmlspecialchars($skill['skill_name']); ?>
                            </label>
                        <?php endforeach; endif; ?>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label>Custom Skills (Not listed above?)</label>
                    <input type="text" name="custom_skills"
                        placeholder="e.g., Figma, React, Cybersecurity (Separate with commas)">
                    <small style="color: var(--secondary);">Custom skills are staged for PESO Admin verification before
                        entering the main dictionary.</small>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Complete Profile & Run AI Matrix</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // Track the current index to ensure array keys remain unique and sequential
            let eduIndex = 1;
            let expIndex = 1;

            // --- Education Repeater ---
            document.getElementById('btn-add-edu').addEventListener('click', function () {
                const container = document.getElementById('education-container');
                const htmlTemplate = `
                <div class="repeater-row edu-row">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Degree / Attainment Level</label>
                            <input type="text" name="education[${eduIndex}][degree_level]" placeholder="e.g., BS Information Technology">
                        </div>
                        <div class="form-group">
                            <label>Institution Name</label>
                            <input type="text" name="education[${eduIndex}][school_name]" placeholder="e.g., Guimba National High School">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Year Graduated</label>
                        <input type="number" name="education[${eduIndex}][year_graduated]" placeholder="YYYY">
                    </div>
                    <button type="button" class="btn btn-danger remove-edu">Remove Row</button>
                </div>
            `;
                container.insertAdjacentHTML('beforeend', htmlTemplate);
                eduIndex++;
            });

            // --- Work Experience Repeater ---
            document.getElementById('btn-add-exp').addEventListener('click', function () {
                const container = document.getElementById('experience-container');
                const htmlTemplate = `
                <div class="repeater-row exp-row">
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Job Title</label>
                            <input type="text" name="experience[${expIndex}][job_title]" placeholder="e.g., Junior Web Developer">
                        </div>
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="experience[${expIndex}][company_name]" placeholder="Company LLC">
                        </div>
                        <div class="form-group">
                            <label>Start Date</label>
                            <input type="date" name="experience[${expIndex}][start_date]">
                        </div>
                        <div class="form-group">
                            <label>End Date (Leave blank if present)</label>
                            <input type="date" name="experience[${expIndex}][end_date]">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Job Description & Achievements</label>
                        <textarea name="experience[${expIndex}][job_description]" rows="3" placeholder="Describe your responsibilities..."></textarea>
                    </div>
                    <button type="button" class="btn btn-danger remove-exp">Remove Row</button>
                </div>
            `;
                container.insertAdjacentHTML('beforeend', htmlTemplate);
                expIndex++;
            });

            // --- Event Delegation for Dynamic Removal ---
            document.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-edu')) {
                    e.target.closest('.edu-row').remove();
                }
                if (e.target && e.target.classList.contains('remove-exp')) {
                    e.target.closest('.exp-row').remove();
                }
            });

        });
    </script>

</body>

</html>