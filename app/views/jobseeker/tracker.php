<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Application Tracker - S.I.K.A.P. Hub</title>
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

        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .nav-tabs a {
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 3px;
            font-weight: bold;
            background: #e0e0e0;
            color: #333;
        }

        .nav-tabs a.active {
            background: #0056b3;
            color: white;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #ecf0f1;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
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
            background: #e74c3c;
            color: white;
        }

        .empty-state {
            color: #7f8c8d;
            font-style: italic;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Job Seeker Portal</h2>
        <p>Monitor your active applications and employer feedback.</p>
    </div>

    <div class="nav-tabs">
        <a href="/sikaphub_v2/dashboard">Smart Feed</a>
        <a href="/sikaphub_v2/my-applications" class="active">Application Tracker</a>
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
                        <td>
                            <?php echo date('M d, Y', strtotime($app['application_date'])); ?>
                        </td>
                        <td><strong>
                                <?php echo htmlspecialchars($app['job_title']); ?>
                            </strong></td>
                        <td>
                            <?php echo htmlspecialchars($app['company_name']); ?>
                        </td>
                        <td>
                            <?php echo $app['match_percentage']; ?>%
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
</body>

</html>