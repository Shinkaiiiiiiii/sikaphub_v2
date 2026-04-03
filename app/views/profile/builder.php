<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Skill Builder - S.I.K.A.P. Hub</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
            max-width: 500px;
            margin: auto;
        }

        .skill-list {
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
            background: #0056b3;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h2>Define Your Skill Profile</h2>
    <form method="POST" action="">
        <div class="skill-list">
            <p>Select your competencies:</p>
            <?php foreach ($skills as $skill): ?>
                <label>
                    <input type="checkbox" name="skills[]" value="<?php echo $skill['skill_id']; ?>">
                    <?php echo htmlspecialchars($skill['skill_name']); ?>
                </label><br>
            <?php endforeach; ?>
        </div>
        <button type="submit">Save Profile & Trigger AI</button>
    </form>
</body>

</html>