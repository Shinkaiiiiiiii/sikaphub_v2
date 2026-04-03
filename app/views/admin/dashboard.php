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

</body>

</html>