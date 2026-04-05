<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Review Candidate - S.I.K.A.P. Hub</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .score-box {
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
        }

        .action-engine {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .action-engine select {
            padding: 8px;
            font-size: 1em;
        }

        .btn-update {
            background: #0056b3;
            color: white;
            border: none;
            padding: 8px 15px;
            cursor: pointer;
            font-weight: bold;
            border-radius: 3px;
        }

        .history-block {
            margin-bottom: 15px;
            padding-left: 15px;
            border-left: 3px solid #bdc3c7;
        }

        .alert {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <a href="/sikaphub_v2/employer/dashboard" style="text-decoration: none; color: #0056b3;">&larr; Back to
            Dashboard</a>
        <br><br>

        <?php if ($success): ?>
            <div class="alert">Application status successfully updated!</div>
        <?php endif; ?>

        <div class="header-section">
            <div>
                <h2>
                    <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?>
                </h2>
                <p>Applying for: <strong>
                        <?php echo htmlspecialchars($app['job_title']); ?>
                    </strong></p>
                <p>Contact:
                    <?php echo htmlspecialchars($app['contact_number']); ?>
                </p>
                <p>Location:
                    <?php echo htmlspecialchars($app['street_address'] . ', ' . $app['barangay_name']); ?>
                </p>
            </div>
            <div class="score-box">
                AI Match Score<br>
                <?php echo round((float) $app['ai_match_score'] * 100); ?>%
            </div>
        </div>

        <div class="action-engine">
            <strong>Current Status:</strong> <span style="color: #0056b3; font-weight: bold;">
                <?php echo htmlspecialchars($app['application_status']); ?>
            </span>
            <form method="POST" action="" style="margin-left: auto;">
                <select name="status">
                    <option value="Pending" <?php if ($app['application_status'] == 'Pending')
                        echo 'selected'; ?>
                        >Pending</option>
                    <option value="Reviewed" <?php if ($app['application_status'] == 'Reviewed')
                        echo 'selected'; ?>
                        >Reviewed</option>
                    <option value="Accepted" <?php if ($app['application_status'] == 'Accepted')
                        echo 'selected'; ?>
                        >Accepted</option>
                    <option value="Rejected" <?php if ($app['application_status'] == 'Rejected')
                        echo 'selected'; ?>
                        >Rejected</option>
                </select>
                <button type="submit" class="btn-update">Update Status</button>
            </form>
        </div>

        <h3>Work Experience</h3>
        <?php if (empty($experience)): ?>
            <p style="color: #7f8c8d;">No work experience provided.</p>
        <?php else: ?>
            <?php foreach ($experience as $exp): ?>
                <div class="history-block">
                    <h4>
                        <?php echo htmlspecialchars($exp['job_title']); ?> at
                        <?php echo htmlspecialchars($exp['company_name']); ?>
                    </h4>
                    <p><small>
                            <?php echo htmlspecialchars($exp['start_date']); ?> to
                            <?php echo htmlspecialchars($exp['end_date'] ?: 'Present'); ?>
                        </small></p>
                    <p>
                        <?php echo htmlspecialchars($exp['job_description']); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h3>Education</h3>
        <?php if (empty($education)): ?>
            <p style="color: #7f8c8d;">No education history provided.</p>
        <?php else: ?>
            <?php foreach ($education as $edu): ?>
                <div class="history-block">
                    <h4>
                        <?php echo htmlspecialchars($edu['degree_level']); ?>
                    </h4>
                    <p>
                        <?php echo htmlspecialchars($edu['school_name']); ?> (Class of
                        <?php echo htmlspecialchars($edu['year_graduated']); ?>)
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>

</html>