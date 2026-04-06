<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PESO Admin Dashboard</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #2c3e50;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
        }

        .btn-export {
            background: #e74c3c;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 3px;
            font-weight: bold;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
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
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #ecf0f1;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>S.I.K.A.P. Hub | PESO Admin Portal</h2>
        <a href="/sikaphub_v2/admin/export" class="btn-export">Export to PDF</a>
    </div>

    <?php if (isset($success) && $success): ?>
        <div
            style="background: #d4edda; color: #155724; padding: 10px; border-radius: 3px; margin-top: 20px; font-weight: bold;">
            Employer verification status updated successfully.
        </div>
    <?php endif; ?>

    <div class="grid">
        <div class="card">
            <h3>Registered Seekers by Barangay</h3>
            <table>
                <tr>
                    <th>Barangay</th>
                    <th>Total Seekers</th>
                </tr>
                <?php foreach ($geography as $geo): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($geo['barangay_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($geo['seeker_count']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="card">
            <h3>Top 5 In-Demand Skills</h3>
            <table>
                <tr>
                    <th>Skill</th>
                    <th>Times Requested</th>
                </tr>
                <?php foreach ($top_skills as $skill): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($skill['skill_name']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($skill['demand_count']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="card" style="margin-top: 20px; border-left: 5px solid #f39c12;">
        <h3>Verification Queue: Pending Employers</h3>
        <p>Review business permits before allowing employers to interact with candidates.</p>

        <?php if (empty($pending_employers)): ?>
            <p style="color: #7f8c8d; font-style: italic;">No pending employers in the queue.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Company Name</th>
                    <th>Contact Info</th>
                    <th>Business Permit</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($pending_employers as $emp): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($emp['company_name']); ?></strong></td>
                        <td>
                            <?php echo htmlspecialchars($emp['contact_person']); ?><br>
                            <small><?php echo htmlspecialchars($emp['company_phone']); ?></small>
                        </td>
                        <td>
                            <a href="/sikaphub_v2/admin/view-document?file=<?php echo urlencode($emp['business_permit']); ?>"
                                target="_blank"
                                style="background: #3498db; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 0.9em;">
                                📄 View Document
                            </a>
                        </td>
                        <td>
                            <form method="POST" action="/sikaphub_v2/admin/verify-employer" style="display: flex; gap: 5px;">
                                <input type="hidden" name="employer_id" value="<?php echo $emp['employer_id']; ?>">
                                <button type="submit" name="status" value="Verified"
                                    style="background: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; font-weight: bold;">Approve</button>
                                <button type="submit" name="status" value="Rejected"
                                    style="background: #e74c3c; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; font-weight: bold;">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

</body>

</html>