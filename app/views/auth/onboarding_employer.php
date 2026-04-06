<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employer Registration - S.I.K.A.P. Hub</title>
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

        .upload-box {
            border: 2px dashed #0056b3;
            padding: 15px;
            background: #f8f9fa;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #0056b3;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>Register Your Company</h2>
    <p>Complete your profile to start hiring. You must upload a valid Business Permit for verification.</p>

    <form method="POST" action="" enctype="multipart/form-data">
        <?php echo CSRF::csrfField(); ?>

        <div class="form-group">
            <label>Company Name</label>
            <input type="text" name="company_name" required>
        </div>
        <div class="form-group">
            <label>Contact Person (HR/Owner)</label>
            <input type="text" name="contact_person" required>
        </div>
        <div class="form-group">
            <label>Company Email</label>
            <input type="email" name="company_email" required>
        </div>
        <div class="form-group">
            <label>Company Phone</label>
            <input type="text" name="company_phone" required>
        </div>
        <div class="form-group">
            <label>HQ Street Address</label>
            <input type="text" name="street_address" placeholder="Bldg No. & Street" required>
        </div>
        <div class="form-group">
            <label>Barangay</label>
            <select name="barangay_id" required>
                <option value="">Select Barangay...</option>
                <?php foreach ($barangays as $b): ?>
                    <option value="<?php echo $b['barangay_id']; ?>">
                        <?php echo htmlspecialchars($b['barangay_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group upload-box">
            <label>Upload Business Permit (PDF/JPG/PNG max 5MB)</label>
            <input type="file" name="business_permit" accept=".pdf, .jpg, .jpeg, .png" required>
        </div>

        <button type="submit">Submit Registration</button>
    </form>
</body>

</html>