<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Post a Job - S.I.K.A.P. Hub</title>
    <style>
        :root {
            --primary: #0056b3;
            --secondary: #6c757d;
            --danger: #e74c3c;
            --background: #f4f7f6;
            --border: #ced4da;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background);
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* --- GLOBAL NAVIGATION BAR --- */
        .global-nav {
            background: #ffffff;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .global-nav .brand {
            font-size: 1.2em;
            font-weight: bold;
            color: #0056b3;
        }

        .global-nav .nav-links a {
            margin-left: 20px;
            text-decoration: none;
            color: #2c3e50;
            font-weight: bold;
        }

        .global-nav .nav-links a:hover {
            color: #0056b3;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
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
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 86, 179, 0.2);
        }

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
            background: var(--primary);
            color: #fff;
        }

        /* Dynamic Skill Tagging UI */
        .skill-selector-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
            background: #f8f9fc;
            padding: 15px;
            border-radius: 6px;
            border: 1px dashed var(--border);
        }

        #selected-skills-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .skill-tag {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            border: 1px solid var(--primary);
            padding: 10px 15px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .skill-tag span {
            font-weight: bold;
            color: var(--primary);
            flex: 1;
        }

        .skill-tag select {
            width: auto;
            padding: 5px;
            margin-right: 15px;
            border-color: var(--border);
        }

        .btn-remove-skill {
            background: transparent;
            color: var(--danger);
            border: none;
            font-weight: bold;
            cursor: pointer;
            font-size: 1.2em;
        }
    </style>
</head>

<body>

    <nav class="global-nav">
        <div class="brand">S.I.K.A.P. Hub</div>
        <div class="nav-links">
            <a href="/sikaphub_v2/employer/dashboard">Dashboard</a>
            <a href="/sikaphub_v2/build-profile">Company Profile</a>
            <a href="/sikaphub_v2/logout" style="color: var(--danger);">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="header-block">
            <h2>Post a New Job Opportunity</h2>
            <p>Define your requirements. The AI Matrix will use these parameters to mathematically rank candidates.</p>
        </div>

        <form method="POST" action="" class="ai-trigger-form"
            data-loader-msg="Analyzing Matrix and Ranking Active Candidates... Please wait.">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <div class="form-section">
                <h3>1. Role Details</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Job Title</label>
                        <input type="text" name="job_title" placeholder="e.g., Senior Web Developer" required>
                    </div>
                    <div class="form-group">
                        <label>Employment Type</label>
                        <select name="employment_type" required>
                            <option value="Full-time">Full-time</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Required Experience Level</label>
                        <input type="text" name="required_experience" placeholder="e.g., 2-3 Years" required>
                    </div>
                    <div class="form-group">
                        <label>Salary Range (Optional)</label>
                        <input type="text" name="salary_range" placeholder="e.g., 20,000 - 30,000">
                    </div>
                </div>
                <div class="form-group">
                    <label>Job Location (Target Municipality)</label>
                    <select name="municipality_id" required>
                        <option value="">Select Municipality...</option>
                        <?php foreach ($municipalities as $m): ?>
                            <option value="<?php echo $m['municipality_id']; ?>">
                                <?php echo htmlspecialchars($m['municipality_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Job Description</label>
                    <textarea name="job_description" rows="6" placeholder="Describe the day-to-day responsibilities..."
                        required></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3>2. AI Skills Matrix Builder</h3>
                <p style="font-size: 0.9em; color: var(--secondary); margin-bottom: 20px;">
                    Select the skills required for this role. Mark critical skills as "Mandatory" to heavily weight them
                    in the AI computation.
                </p>

                <div class="skill-selector-wrapper">
                    <select id="master-skill-dropdown" style="flex: 1;">
                        <option value="">-- Select a skill from the dictionary --</option>
                        <?php
                        $currentCategory = '';
                        foreach ($skills as $skill):
                            if ($currentCategory !== $skill['category_name']):
                                if ($currentCategory !== '')
                                    echo '</optgroup>';
                                $currentCategory = $skill['category_name'];
                                $safeCategory = htmlspecialchars($currentCategory ?? 'Uncategorized');
                                echo "<optgroup label=\"{$safeCategory}\">";
                            endif;
                            ?>
                            <option value="<?php echo $skill['skill_id']; ?>">
                                <?php echo htmlspecialchars($skill['skill_name']); ?></option>
                        <?php endforeach;
                        if ($currentCategory !== '')
                            echo '</optgroup>';
                        ?>
                    </select>
                    <button type="button" id="btn-add-skill" class="btn btn-secondary">+ Add Skill</button>
                </div>

                <div id="selected-skills-container">
                    <p id="empty-skill-msg" class="empty-state" style="text-align: center; margin: 10px 0;">No skills
                        added yet. Select a skill from the dropdown.</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Publish Job & Trigger AI Matcher</button>
        </form>
    </div>

    <?php
    if (file_exists(BASE_PATH . 'app/views/components/loader.php')) {
        require BASE_PATH . 'app/views/components/loader.php';
    }
    ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dropdown = document.getElementById('master-skill-dropdown');
            const addBtn = document.getElementById('btn-add-skill');
            const container = document.getElementById('selected-skills-container');
            const emptyMsg = document.getElementById('empty-skill-msg');

            // Track added skills to prevent duplicates
            let addedSkills = new Set();

            addBtn.addEventListener('click', function () {
                const skillId = dropdown.value;
                const skillName = dropdown.options[dropdown.selectedIndex].text;

                if (!skillId) {
                    alert("Please select a skill from the dropdown first.");
                    return;
                }

                if (addedSkills.has(skillId)) {
                    alert("You have already added this skill.");
                    return;
                }

                // Hide empty message
                if (emptyMsg) emptyMsg.style.display = 'none';

                // Mark as added
                addedSkills.add(skillId);

                // Build the DOM element
                const tagHtml = `
                    <div class="skill-tag" data-id="${skillId}">
                        <span>${skillName}</span>
                        <select name="requirement_type[${skillId}]">
                            <option value="Mandatory">Mandatory (Required)</option>
                            <option value="Optional">Optional (Nice-to-have)</option>
                        </select>
                        <input type="hidden" name="skills[]" value="${skillId}">
                        <button type="button" class="btn-remove-skill" title="Remove">&times;</button>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', tagHtml);
                dropdown.value = ""; // Reset dropdown
            });

            // Event delegation for removal
            container.addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-remove-skill')) {
                    const tag = e.target.closest('.skill-tag');
                    const skillId = tag.getAttribute('data-id');

                    addedSkills.delete(skillId);
                    tag.remove();

                    if (addedSkills.size === 0 && emptyMsg) {
                        emptyMsg.style.display = 'block';
                    }
                }
            });
        });
    </script>
</body>

</html>