<?php
// Ensure this view receives $existing_profile from the Controller
$p = $existing_profile ?? [];

// Pre-extract values safely to keep the HTML clean
$companyName = htmlspecialchars($p['company_name'] ?? '');
$address = htmlspecialchars($p['street_address'] ?? ''); // Adjusted to match standard schema
$companyPhone = htmlspecialchars($p['company_phone'] ?? ''); // <-- FIXED VARIABLE
$industry = htmlspecialchars($p['industry'] ?? '');
$companySize = htmlspecialchars($p['company_size'] ?? '');
$companyDesc = htmlspecialchars($p['company_description'] ?? '');
$websiteUrl = htmlspecialchars($p['website_url'] ?? '');
$facebookUrl = htmlspecialchars($p['facebook_url'] ?? '');
$linkedinUrl = htmlspecialchars($p['linkedin_url'] ?? '');
$twitterUrl = htmlspecialchars($p['twitter_url'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Profile Builder - S.I.K.A.P. Hub</title>
    <style>
        :root {
            --primary: #0056b3;
            --secondary: #6c757d;
            --danger: #e74c3c;
            --background: #f4f7f6;
            --border: #ced4da;
            --readonly-bg: #e9ecef;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background);
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header-block {
            margin-bottom: 30px;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
        }

        /* Card Architecture */
        .form-section {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .form-section h3 {
            margin-top: 0;
            color: var(--primary);
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 1em;
            width: 100%;
            box-sizing: border-box;
            font-family: inherit;
        }

        .form-group input[readonly] {
            background-color: var(--readonly-bg);
            cursor: not-allowed;
            color: var(--secondary);
        }

        .form-group input:focus:not([readonly]),
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(0, 86, 179, 0.2);
        }

        /* Grid Layouts */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Button Polish */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 0.95em;
            transition: opacity 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            width: 100%;
            padding: 15px;
            font-size: 1.1em;
        }

        /* File Upload Styling */
        .file-upload-wrapper {
            border: 1px dashed var(--border);
            padding: 15px;
            border-radius: 4px;
            background: #fafafa;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header-block">
            <h2>Company Profile Setup</h2>
            <p>Complete your employer brand. A detailed profile increases your application rate from top-tier
                candidates.</p>
        </div>

        <form method="POST" action="/sikaphub_v2/build-profile" enctype="multipart/form-data" class="ai-trigger-form"
            data-loader-msg="Saving Company Profile...">

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

            <div class="form-section">
                <h3>1. Core Identity & Contact</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Company Name (Locked)</label>
                        <input type="text" name="company_name" value="<?php echo $companyName; ?>" readonly
                            title="Configured during onboarding. Contact PESO Admin to change.">
                    </div>
                    <div class="form-group">
                        <label>Registered Address (Locked)</label>
                        <input type="text" name="address" value="<?php echo $address; ?>" readonly
                            title="Configured during onboarding.">
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="company_phone" value="<?php echo $companyPhone; ?>"
                            placeholder="e.g., 09123456789">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 10px;">
                    <label>Company Logo</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="company_logo" accept="image/png, image/jpeg, image/webp"
                            style="border: none; background: transparent;">
                        <br><small style="color: var(--secondary);">Recommended: Square PNG/JPG, max 2MB. Leave blank to
                            keep existing logo.</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>2. Company Overview</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Industry</label>
                        <select name="industry" required>
                            <option value="">Select Industry...</option>
                            <option value="Information Technology" <?php if ($industry == 'Information Technology')
                                echo 'selected'; ?>>Information Technology</option>
                            <option value="Finance & Banking" <?php if ($industry == 'Finance & Banking')
                                echo 'selected'; ?>>Finance & Banking</option>
                            <option value="Healthcare" <?php if ($industry == 'Healthcare')
                                echo 'selected'; ?>>Healthcare
                            </option>
                            <option value="Retail & E-Commerce" <?php if ($industry == 'Retail & E-Commerce')
                                echo 'selected'; ?>>Retail & E-Commerce</option>
                            <option value="Manufacturing" <?php if ($industry == 'Manufacturing')
                                echo 'selected'; ?>>
                                Manufacturing</option>
                            <option value="Education" <?php if ($industry == 'Education')
                                echo 'selected'; ?>>Education
                            </option>
                            <option value="Other" <?php if ($industry == 'Other')
                                echo 'selected'; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Company Size</label>
                        <select name="company_size" required>
                            <option value="">Select Size...</option>
                            <option value="1-10 Employees" <?php if ($companySize == '1-10 Employees')
                                echo 'selected'; ?>>1-10 Employees (Startup)</option>
                            <option value="11-50 Employees" <?php if ($companySize == '11-50 Employees')
                                echo 'selected'; ?>>11-50 Employees (Small)</option>
                            <option value="51-200 Employees" <?php if ($companySize == '51-200 Employees')
                                echo 'selected'; ?>>51-200 Employees (Medium)</option>
                            <option value="201-500 Employees" <?php if ($companySize == '201-500 Employees')
                                echo 'selected'; ?>>201-500 Employees (Large)</option>
                            <option value="500+ Employees" <?php if ($companySize == '500+ Employees')
                                echo 'selected'; ?>>500+ Employees (Enterprise)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Company Description & Culture</label>
                    <textarea name="company_description" rows="6"
                        placeholder="Describe your mission, vision, and workplace culture..."
                        required><?php echo $companyDesc; ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h3>3. Digital Presence</h3>
                <div class="grid-2">
                    <div class="form-group">
                        <label>Website URL</label>
                        <input type="url" name="website_url" value="<?php echo $websiteUrl; ?>"
                            placeholder="https://www.yourcompany.com">
                    </div>
                    <div class="form-group">
                        <label>LinkedIn Page</label>
                        <input type="url" name="linkedin_url" value="<?php echo $linkedinUrl; ?>"
                            placeholder="https://linkedin.com/company/...">
                    </div>
                    <div class="form-group">
                        <label>Facebook Page</label>
                        <input type="url" name="facebook_url" value="<?php echo $facebookUrl; ?>"
                            placeholder="https://facebook.com/...">
                    </div>
                    <div class="form-group">
                        <label>Twitter / X Profile</label>
                        <input type="url" name="twitter_url" value="<?php echo $twitterUrl; ?>"
                            placeholder="https://twitter.com/...">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Save Complete Company Profile</button>
        </form>
    </div>

    <?php
    if (file_exists(BASE_PATH . 'app/views/components/loader.php')) {
        require BASE_PATH . 'app/views/components/loader.php';
    }
    ?>

</body>

</html>