<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - S.I.K.A.P. Hub</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            max-width: 400px;
            margin: auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
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
            background: #0056b3;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h2>Create an Account</h2>
    <form method="POST" action="">
        <?php echo CSRF::csrfField(); ?>

        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>I am a...</label>
            <select name="role" required>
                <option value="jobseeker">Job Seeker</option>
                <option value="employer">Employer</option>
            </select>
        </div>
        <button type="submit">Register</button>
    </form>
    <p style="text-align: center;"><a href="login">Already have an account? Login here.</a></p>
</body>

</html>