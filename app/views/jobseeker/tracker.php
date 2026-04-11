<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Application Tracker - S.I.K.A.P. Hub</title>
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

        /* Card and Table Styling */
        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .card h3 {
            margin-top: 0;
            color: var(--primary);
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fc;
            color: #2c3e50;
            font-size: 0.95em;
        }

        tr:hover {
            background-color: #fafafa;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
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

        .empty-state {
            text-align: center;
            color: #7f8c8d;
            padding: 30px;
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
            <p>Monitor your active applications and employer feedback.</p>
        </div>

        <div class="nav-tabs">
            <a href="/sikaphub_v2/dashboard" class="tab-inactive">Smart Feed</a>
            <a href="/sikaphub_v2/my-applications" class="tab-active">Application Tracker</a>
        </div>

        <div class="card">
            <h3>My Applications</h3>

            <?php if (empty($applications)): ?>
                <p class="empty-state">You have not applied to any jobs yet.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Date Applied</th>
                        <th>Role</th>
                        <th>Company</th>
                        <th>Locked Match Score</th>
                        <th>Current Status</th>
                    </tr>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td style="color: #6c757d;">
                                <?php echo date('M d, Y', strtotime($app['application_date'])); ?>
                            </td>
                            <td>
                                <strong
                                    style="color: var(--primary);"><?php echo htmlspecialchars($app['job_title']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($app['company_name']); ?>
                            </td>
                            <td>
                                <strong><?php echo $app['match_percentage']; ?>%</strong>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo htmlspecialchars($app['application_status']); ?>">
                                    <?php echo htmlspecialchars($app['application_status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const urlParams = new URLSearchParams(window.location.search);
            let message = '';
            let type = 'success';

            if (urlParams.has('success') && urlParams.get('success') === 'applied') {
                message = "Application submitted successfully! Your AI match score is locked.";
            }

            if (message !== '') {
                const container = document.getElementById('toast-container');
                const toastHTML = `
                    <div class="toast ${type}">
                        <span>✅</span>
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