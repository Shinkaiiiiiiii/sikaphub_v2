<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Dashboard - S.I.K.A.P. Hub</title>
    <style>
        :root {
            --primary: #0056b3;
            --secondary: #6c757d;
            --danger: #e74c3c;
            --background: #f4f7f6;
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-block {
            background: #2c3e50;
            color: white;
            padding: 20px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header-block h2 {
            margin: 0 0 5px 0;
        }

        .header-block p {
            margin: 0;
            opacity: 0.8;
        }

        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .nav-tabs a {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background 0.2s;
        }

        .tab-active {
            background: var(--primary);
            color: white;
        }

        .tab-inactive {
            background: #e9ecef;
            color: #555;
        }

        .tab-inactive:hover {
            background: #dde2e6;
        }

        .job-card {
            background: white;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid transparent;
            transition: transform 0.2s;
        }

        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .job-info h3 {
            margin: 0 0 8px 0;
            color: var(--primary);
            font-size: 1.3em;
        }

        .job-info p {
            margin: 4px 0;
            color: #555;
            font-size: 0.95em;
        }

        .job-info .company {
            font-weight: bold;
            color: #333;
            font-size: 1.05em;
        }

        .action-area {
            text-align: center;
            min-width: 130px;
        }

        .match-circle {
            color: white;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.3em;
            margin: 0 auto 15px auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Dynamic AI Score Colors */
        .match-high {
            background: #28a745;
        }

        .match-med {
            background: #f39c12;
        }

        .match-low {
            background: #6c757d;
        }

        .btn-apply {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            transition: background 0.2s;
        }

        .btn-apply:hover {
            background: #004494;
        }

        .empty-state {
            text-align: center;
            color: #7f8c8d;
            padding: 40px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            font-style: italic;
        }

        /* --- GLOBAL TOAST NOTIFICATION CSS --- */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            background: #333;
            color: #fff;
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            opacity: 0;
            transform: translateX(100%);
            animation: slideIn 0.3s forwards, fadeOut 0.3s forwards 4.7s;
        }

        .toast.success {
            border-left: 5px solid #28a745;
        }

        .toast.error {
            border-left: 5px solid var(--danger);
        }

        .toast span {
            margin-left: 10px;
            font-weight: bold;
        }

        @keyframes slideIn {
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
            }
        }
    </style>
</head>

<body>

    <nav class="global-nav">
        <div class="brand">S.I.K.A.P. Hub</div>
        <div class="nav-links">
            <a href="/sikaphub_v2/dashboard">Smart Feed</a>
            <a href="/sikaphub_v2/build-profile">My Profile</a>
            <a href="/sikaphub_v2/logout" style="color: var(--danger);">Logout</a>
        </div>
    </nav>

    <div id="toast-container"></div>

    <div class="container">
        <div class="header-block">
            <h2>Job Seeker Portal</h2>
            <p>Top Matches for Your AI Matrix Profile</p>
        </div>

        <div class="nav-tabs">
            <a href="/sikaphub_v2/dashboard" class="tab-active">Smart Feed</a>
            <a href="/sikaphub_v2/my-applications" class="tab-inactive">Application Tracker</a>
        </div>

        <div class="feed">
            <?php if (empty($jobs)): ?>
                <div class="empty-state">
                    <p>No open jobs available at the moment. Please check back later!</p>
                </div>
            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                    <?php
                    // Dynamic Color Logic based on AI Match Percentage
                    $scoreColorClass = 'match-low';
                    if ($job['match_percentage'] >= 75)
                        $scoreColorClass = 'match-high';
                    elseif ($job['match_percentage'] >= 40)
                        $scoreColorClass = 'match-med';
                    ?>
                    <div class="job-card">
                        <div class="job-info">
                            <h3><?php echo htmlspecialchars($job['job_title']); ?></h3>
                            <p class="company"><?php echo htmlspecialchars($job['company_name']); ?></p>
                            <p>📍 <?php echo htmlspecialchars($job['barangay_name']); ?> | 💼
                                <?php echo htmlspecialchars($job['employment_type']); ?></p>
                            <p>💰 <?php echo htmlspecialchars($job['salary_range'] ?: 'Salary not specified'); ?></p>
                        </div>

                        <div class="action-area">
                            <div class="match-circle <?php echo $scoreColorClass; ?>">
                                <?php echo $job['match_percentage']; ?>%
                            </div>
                            <form method="POST" action="/sikaphub_v2/apply" class="ai-trigger-form"
                                data-loader-msg="Locking final AI Match Score & Submitting Application...">

                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                                <input type="hidden" name="match_score" value="<?php echo $job['match_score']; ?>">

                                <button type="submit" class="btn-apply">Apply Now</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php
    if (file_exists(BASE_PATH . 'app/views/components/loader.php')) {
        require BASE_PATH . 'app/views/components/loader.php';
    }
    ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            let message = '';
            let type = 'success';

            if (urlParams.has('profile_updated')) {
                message = "Your AI Profile was successfully updated!";
            } else if (urlParams.has('error')) {
                type = 'error';
                const errorType = urlParams.get('error');
                if (errorType === 'already_applied') {
                    message = "Application Failed: You have already applied for this position.";
                } else if (errorType === 'unauthorized') {
                    message = "Access Denied.";
                } else {
                    message = "An error occurred: " + errorType.replace(/_/g, ' ');
                }
            }

            if (message !== '') {
                const container = document.getElementById('toast-container');
                const toastHTML = `
                    <div class="toast ${type}">
                        <span>${type === 'success' ? '✅' : '⚠️'}</span>
                        <span>${message}</span>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', toastHTML);

                // Clean URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>

</html>