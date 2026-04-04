<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Complete Your Profile - S.I.K.A.P. Hub</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            max-width: 500px;
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

        input,
        select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>Step 1: Complete Your Basic Profile</h2>
    <p>Welcome! We need a few more details before you can apply for jobs in Guimba.</p>

    <form method="POST" action="">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" required>
        </div>
        <div class="form-group">
            <label>Gender</label>
            <select name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="form-group">
            <label>Birthdate</label>
            <input type="date" name="birthdate" required>
        </div>
        <div class="form-group">
            <label>Street Address</label>
            <input type="text" name="street_address" placeholder="House No. & Street" required>
        </div>
        <div class="form-group">
            <label>Barangay</label>
            <select name="barangay_id" required>
                <option value="">Select Barangay...</option>
                <?php foreach ($barangays as $b): ?>
                    <option value="<?php echo $b['barangay_id']; ?>"><?php echo htmlspecialchars($b['barangay_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="contact_number" required>
        </div>
        <button type="submit">Complete Profile & Activate Account</button>
    </form>
</body>

</html>