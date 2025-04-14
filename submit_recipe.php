<?php
require('db.php');
session_start();

// // Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Check if form data is received
if (!isset($_POST['recipe_id']) || !isset($_POST['competition_id'])) {
    header("Location: competition6.php?error=invalid_submission");
    exit();
}

//$user_id = $_SESSION['user_id'];
$user_id =2;
$recipe_id = (int)$_POST['recipe_id'];
$competition_id = (int)$_POST['competition_id'];

// Validate competition exists and is upcoming
$comp_check = mysqli_query($con, "SELECT * FROM competitions WHERE competition_id = $competition_id AND status = 'upcoming'");
if (mysqli_num_rows($comp_check) == 0) {
    header("Location: competition6.php?error=invalid_competition");
    exit();
}

// Check if recipe belongs to the user
$recipe_query = "SELECT * FROM recipes WHERE recipe_id = $recipe_id AND user_id = $user_id";
$recipe_result = mysqli_query($con, $recipe_query);

if (mysqli_num_rows($recipe_result) == 0) {
    header("Location: competition6.php?error=invalid_recipe");
    exit();
}

// Check if user has already submitted a recipe for this competition
$submission_check = mysqli_query($con, "SELECT * FROM competition_recipes WHERE competition_id = $competition_id AND user_id = $user_id");
if (mysqli_num_rows($submission_check) > 0) {
    header("Location: competition6.php?error=already_submitted");
    exit();
}

// Get recipe details
$recipe = mysqli_fetch_assoc($recipe_result);

// Insert into competition_recipes
$current_time = date('Y-m-d H:i:s');
$recipe_name = mysqli_real_escape_string($con, $recipe['recipe_name']);
$ingredients = mysqli_real_escape_string($con, $recipe['ingredients']);
$steps = mysqli_real_escape_string($con, $recipe['steps']);
$description = mysqli_real_escape_string($con, $recipe['description']);

$insert_query = "INSERT INTO competition_recipes 
                (user_id, recipe_name, ingredients, steps, description, submitted_at, competition_id) 
                VALUES 
                ($user_id, '$recipe_name', '$ingredients', '$steps', '$description', '$current_time', $competition_id)";

if (mysqli_query($con, $insert_query)) {
    header("Location: competition6.php?success=submission_complete");
    exit();
} else {
    header("Location: competition6.php?error=submission_failed");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitting Recipe...</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .loading-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .loading-icon {
            font-size: 3em;
            color: #1e3c72;
            margin-bottom: 20px;
            animation: spin 1s infinite linear;
        }

        .loading-text {
            color: #333;
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .loading-subtext {
            color: #666;
            font-size: 0.9em;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <i class='bx bx-loader-alt loading-icon'></i>
        <div class="loading-text">Submitting your recipe...</div>
        <div class="loading-subtext">Please wait while we process your submission</div>
    </div>

    <script>
        // Auto-redirect if the page is shown
        setTimeout(() => {
            window.location.href = 'competition.php';
        }, 2000);
    </script>
</body>
</html>