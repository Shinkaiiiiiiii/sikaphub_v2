<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Post a Job - S.I.K.A.P. Hub</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .skill-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            background: #f8f9fa;
            padding: 15px;
            border: 1px solid #ddd;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <h2>Post a New Job Opportunity</h2>
    <p>Define your requirements. The AI engine will use these parameters to rank candidates.</p>

    <form method="POST" action="" class="ai-trigger-form"
        data-loader-msg="Analyzing Matrix and Ranking Active Candidates... Please wait.">
        <?php echo CSRF::csrfField(); ?>

        <div class="form-group">
            <label>Job Title</label>
            <input type="text" name="job_title" placeholder="e.g., Senior Web Developer" required>
        </div>

        <div class="form-group">
            <label>Job Description</label>
            <textarea name="job_description" rows="5" required></textarea>
        </div>

        <div class="form-group">
            <label>Required Experience Level</label>
            <input type="text" name="required_experience" placeholder="e.g., 2-3 Years" required>
        </div>

        <div class="form-group">
            <label>Salary Range (Optional)</label>
            <input type="text" name="salary_range" placeholder="e.g., Php 20,000 - 30,000">
        </div>

        <div class="form-group">
            <label>Employment Type</label>
            <select name="employment_type" required>
                <option value="Full-time">Full-time</option>
                <option value="Part-time">Part-time</option>
                <option value="Contract">Contract</option>
                <option value="Temporary">Temporary</option>
                <option value="Internship">Internship</option>
            </select>
        </div>

        <div class="form-group">
            <label>Job Location (Barangay)</label>
            <select name="barangay_id" required>
                <option value="">Select Barangay...</option>
                <?php foreach ($barangays as $b): ?>
                    <option value="<?php echo $b['barangay_id']; ?>">
                        <?php echo htmlspecialchars($b['barangay_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <h3>Required AI Skills (Select all that apply)</h3>
        <div class="skill-grid">
            <?php foreach ($skills as $skill): ?>
                <label>
                    <input type="checkbox" name="skills[]" value="<?php echo $skill['skill_id']; ?>">
                    <?php echo htmlspecialchars($skill['skill_name']); ?>
                </label>
            <?php endforeach; ?>
        </div>

        <button type="submit">Publish Job</button>
    </form>

    <?php require BASE_PATH . 'app/views/components/loader.php'; ?>
</body>

</html>