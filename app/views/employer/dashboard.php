<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employer ATS - S.I.K.A.P. Hub</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding: 0;
            /* Removed padding to allow full-width navbar */
        }

        /* --- GLOBAL NAVIGATION BAR --- */
        .global-nav {
            background: #ffffff;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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
            padding: 0 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-post {
            background: #28a745;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 3px;
            font-weight: bold;
        }

        .job-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .job-header {
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .job-header h3 {
            margin: 0;
            color: #2c3e50;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8em;
            font-weight: bold;
            background: #e8f4f8;
            color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #ecf0f1;
        }

        .score-high {
            color: #28a745;
            font-weight: bold;
        }

        .score-med {
            color: #f39c12;
            font-weight: bold;
        }

        .score-low {
            color: #e74c3c;
            font-weight: bold;
        }

        .empty-state {
            color: #7f8c8d;
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
            border-left: 5px solid #e74c3c;
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
        <div class="header">
            <div>
                <h2>Employer ATS Dashboard</h2>
                <p>Manage your job postings and review AI-ranked candidates.</p>
            </div>
            <a href="/sikaphub_v2/post-job" class="btn-post">+ Post New Job</a>
        </div>

        <div class="feed">
            <?php if (empty($jobs)): ?>
                <div class="job-card">
                    <p class="empty-state">You have not posted any jobs yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($jobs as $job): ?>
                    <div class="job-card">
                        <div class="job-header">
                            <h3>
                                <?php echo htmlspecialchars($job['job_title']); ?>
                            </h3>
                            <span class="status-badge">
                                <?php echo htmlspecialchars($job['job_status']); ?>
                            </span>
                            <small style="color: #7f8c8d; margin-left: 10px;">Posted:
                                <?php echo date('M d, Y', strtotime($job['date_posted'])); ?>
                            </small>
                        </div>

                        <?php if (empty($job['applicants'])): ?>
                            <p class="empty-state">No applications received yet.</p>
                        <?php else: ?>
                            <table>
                                <tr>
                                    <th>Candidate Name</th>
                                    <th>Contact</th>
                                    <th>AI Match Score</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                <?php foreach ($job['applicants'] as $applicant): ?>
                                    <?php
                                    // Determine color coding based on score thresholds
                                    $scoreClass = 'score-low';
                                    if ($applicant['match_percentage'] >= 75)
                                        $scoreClass = 'score-high';
                                    elseif ($applicant['match_percentage'] >= 40)
                                        $scoreClass = 'score-med';
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']); ?>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($applicant['contact_number']); ?>
                                        </td>
                                        <td class="<?php echo $scoreClass; ?>">
                                            <?php echo $applicant['match_percentage']; ?>%
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($applicant['application_status']); ?>
                                        </td>
                                        <td>
                                            <a href="/sikaphub_v2/employer/review-candidate?app_id=<?php echo $applicant['application_id']; ?>"
                                                style="background: #0056b3; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 0.9em;">Review
                                                Profile</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            let message = '';
            let type = 'success'; // default to success

            // Check URL parameters for flags sent by the Controller
            if (urlParams.has('job_posted')) {
                message = "Job Opportunity successfully posted!";
            } else if (urlParams.has('profile_updated')) {
                message = "Company Profile updated successfully!";
            } else if (urlParams.has('error')) {
                type = 'error';
                message = "An error occurred: " + urlParams.get('error').replace(/_/g, ' ');
            }

            // If a message exists, build and display the Toast
            if (message !== '') {
                const container = document.getElementById('toast-container');
                const toastHTML = `
                    <div class="toast ${type}">
                        <span>${type === 'success' ? '✅' : '⚠️'}</span>
                        <span>${message}</span>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', toastHTML);

                // Clean up the URL so the toast doesn't reappear on manual page refresh
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
</body>

</html>