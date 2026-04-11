<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Review Candidate - S.I.K.A.P. Hub</title>
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
            color: var(--primary);
        }

        .global-nav .nav-links a {
            margin-left: 20px;
            text-decoration: none;
            color: #2c3e50;
            font-weight: bold;
        }

        .global-nav .nav-links a:hover {
            color: var(--primary);
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 40px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: var(--primary);
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* Header Section with Photo */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 25px;
            margin-bottom: 25px;
        }

        .profile-meta {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .profile-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ecf0f1;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .profile-photo-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
            font-size: 0.9em;
            font-weight: bold;
            border: 3px solid #ecf0f1;
        }

        .profile-info h2 {
            margin: 0 0 5px 0;
            color: #2c3e50;
            font-size: 1.8em;
        }

        .profile-info p {
            margin: 3px 0;
            color: #555;
        }

        .profile-info strong {
            color: var(--primary);
        }

        .score-box {
            background: #28a745;
            color: white;
            padding: 15px 25px;
            border-radius: 6px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
        }

        .score-box .score-label {
            font-size: 0.9em;
            font-weight: bold;
            opacity: 0.9;
            text-transform: uppercase;
        }

        .score-box .score-value {
            font-size: 2.2em;
            font-weight: bold;
            margin-top: 5px;
        }

        /* Action Engine */
        .action-engine {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #e3e6f0;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 1em;
        }

        .status-Pending {
            background: #f39c12;
            color: white;
        }

        .status-Reviewed {
            background: #3498db;
            color: white;
        }

        .status-Accepted {
            background: #28a745;
            color: white;
        }

        .status-Rejected {
            background: var(--danger);
            color: white;
        }

        .action-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .action-form select {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 1em;
            outline: none;
        }

        .btn-update {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-weight: bold;
            border-radius: 4px;
            font-size: 1em;
            transition: opacity 0.2s;
        }

        .btn-update:hover {
            opacity: 0.9;
        }

        /* Assets & History */
        h3 {
            color: var(--primary);
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 8px;
            margin-top: 30px;
        }

        .btn-resume {
            display: inline-block;
            background: #2c3e50;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-bottom: 20px;
            transition: background 0.2s;
        }

        .btn-resume:hover {
            background: #1a252f;
        }

        .history-block {
            margin-bottom: 20px;
            padding-left: 20px;
            border-left: 4px solid var(--border);
        }

        .history-block h4 {
            margin: 0 0 5px 0;
            color: #2c3e50;
            font-size: 1.1em;
        }

        .history-block .dates {
            color: var(--secondary);
            font-size: 0.9em;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .history-block p {
            margin: 0;
            line-height: 1.5;
            color: #444;
        }

        .empty-state {
            color: var(--secondary);
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
            <a href="/sikaphub_v2/employer/dashboard">Dashboard</a>
            <a href="/sikaphub_v2/build-profile">Company Profile</a>
            <a href="/sikaphub_v2/logout" style="color: var(--danger);">Logout</a>
        </div>
    </nav>

    <div id="toast-container"></div>

    <div class="container">
        <a href="/sikaphub_v2/employer/dashboard" class="back-link">&larr; Back to Dashboard</a>

        <div class="header-section">
            <div class="profile-meta">
                <?php if (!empty($app['profile_photo'])): ?>
                    <img src="/sikaphub_v2/admin/view-document?type=photo&file=<?php echo htmlspecialchars($app['profile_photo']); ?>"
                        alt="Profile Photo" class="profile-photo">
                <?php else: ?>
                    <div class="profile-photo-placeholder">No Photo</div>
                <?php endif; ?>

                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></h2>
                    <p>Applying for: <strong><?php echo htmlspecialchars($app['job_title']); ?></strong></p>
                    <p>Contact: <?php echo htmlspecialchars($app['contact_number']); ?></p>
                    <p>Location: <?php echo htmlspecialchars($app['street_address'] . ', ' . $app['barangay_name']); ?>
                    </p>
                </div>
            </div>

            <div class="score-box">
                <div class="score-label">AI Match Score</div>
                <div class="score-value"><?php echo round((float) $app['ai_match_score'] * 100); ?>%</div>
            </div>
        </div>

        <div class="action-engine">
            <div>
                <span style="color: #555; margin-right: 10px;">Current Status:</span>
                <span class="status-badge status-<?php echo htmlspecialchars($app['application_status']); ?>">
                    <?php echo htmlspecialchars($app['application_status']); ?>
                </span>
            </div>

            <form method="POST" action="" class="action-form">
                <?php echo CSRF::csrfField(); ?>
                <select name="status">
                    <option value="Pending" <?php if ($app['application_status'] == 'Pending')
                        echo 'selected'; ?>>Pending
                    </option>
                    <option value="Reviewed" <?php if ($app['application_status'] == 'Reviewed')
                        echo 'selected'; ?>>
                        Reviewed</option>
                    <option value="Accepted" <?php if ($app['application_status'] == 'Accepted')
                        echo 'selected'; ?>>
                        Accepted</option>
                    <option value="Rejected" <?php if ($app['application_status'] == 'Rejected')
                        echo 'selected'; ?>>
                        Rejected</option>
                </select>
                <button type="submit" class="btn-update">Update Status</button>
            </form>
        </div>

        <h3>Candidate Assets</h3>
        <?php if (!empty($app['resume_file'])): ?>
            <a href="/sikaphub_v2/admin/view-document?type=resume&file=<?php echo htmlspecialchars($app['resume_file']); ?>"
                target="_blank" class="btn-resume">
                📄 View / Download Resume (PDF)
            </a>
        <?php else: ?>
            <p class="empty-state">No resume PDF attached to this profile.</p>
        <?php endif; ?>

        <h3>Work Experience</h3>
        <?php if (empty($experience)): ?>
            <p class="empty-state">No work experience provided.</p>
        <?php else: ?>
            <?php foreach ($experience as $exp): ?>
                <div class="history-block">
                    <h4><?php echo htmlspecialchars($exp['job_title']); ?> at
                        <?php echo htmlspecialchars($exp['company_name']); ?>
                    </h4>
                    <div class="dates">
                        <?php echo htmlspecialchars($exp['start_date']); ?> to
                        <?php echo htmlspecialchars($exp['end_date'] ?: 'Present'); ?>
                    </div>
                    <p><?php echo htmlspecialchars($exp['job_description']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h3>Education</h3>
        <?php if (empty($education)): ?>
            <p class="empty-state">No education history provided.</p>
        <?php else: ?>
            <?php foreach ($education as $edu): ?>
                <div class="history-block">
                    <h4><?php echo htmlspecialchars($edu['degree_level']); ?></h4>
                    <div class="dates">Class of <?php echo htmlspecialchars($edu['year_graduated']); ?></div>
                    <p><?php echo htmlspecialchars($edu['school_name']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            let message = '';
            let type = 'success';

            if (urlParams.has('status_updated')) {
                message = "Application status successfully updated!";
            } else if (urlParams.has('error')) {
                type = 'error';
                message = "Error: " + urlParams.get('error').replace(/_/g, ' ');
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