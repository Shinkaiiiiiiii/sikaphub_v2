<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Dashboard - S.I.K.A.P. Hub</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .header {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .job-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .job-info h3 {
            margin: 0 0 5px 0;
            color: #0056b3;
        }

        .job-info p {
            margin: 2px 0;
            color: #555;
            font-size: 0.9em;
        }

        .match-circle {
            background: #28a745;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .action-area {
            text-align: center;
        }

        .btn-apply {
            background: #0056b3;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 3px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }

        .btn-apply:hover {
            background: #004494;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Job Seeker Portal</h2>
        <p>Top Matches for Your Skills</p>
    </div>

    <div class="nav-tabs" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <a href="/sikaphub_v2/dashboard"
            style="padding: 10px 15px; text-decoration: none; border-radius: 3px; font-weight: bold; background: #0056b3; color: white;">Smart
            Feed</a>
        <a href="/sikaphub_v2/my-applications"
            style="padding: 10px 15px; text-decoration: none; border-radius: 3px; font-weight: bold; background: #e0e0e0; color: #333;">Application
            Tracker</a>
    </div>

    <div class="feed">
        <?php if (empty($jobs)): ?>
            <p>No open jobs available at the moment.</p>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <div class="job-info">
                        <h3>
                            <?php echo htmlspecialchars($job['job_title']); ?>
                        </h3>
                        <p><strong>
                                <?php echo htmlspecialchars($job['company_name']); ?>
                            </strong></p>
                        <p>📍
                            <?php echo htmlspecialchars($job['barangay_name']); ?> | 💼
                            <?php echo htmlspecialchars($job['employment_type']); ?>
                        </p>
                        <p>💰
                            <?php echo htmlspecialchars($job['salary_range'] ?: 'Not specified'); ?>
                        </p>
                    </div>

                    <div class="action-area">
                        <div class="match-circle">
                            <?php echo $job['match_percentage']; ?>%
                        </div>
                        <form method="POST" action="/sikaphub_v2/apply">
                            <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                            <input type="hidden" name="match_score" value="<?php echo $job['match_score']; ?>">
                            <button type="submit" class="btn-apply">Apply Now</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>