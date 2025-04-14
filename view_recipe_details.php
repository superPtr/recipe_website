<?php
require('db.php');
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// $user_id = $_SESSION['user_id'];
$user_id = 1;
$competition_id = isset($_GET['comp_id']) ? (int)$_GET['comp_id'] : 0;
$recipe_id = isset($_GET['recipe_id']) ? (int)$_GET['recipe_id'] : 0;

// Get recipe details
$recipe_query = "SELECT cr.*, c.competition_name, c.status,
                 (SELECT COUNT(*) FROM votes v WHERE v.recipe_id = cr.compRecipe_id) as vote_count
                 FROM competition_recipes cr
                 JOIN competitions c ON cr.competition_id = c.competition_id
                 WHERE cr.compRecipe_id = $recipe_id AND cr.competition_id = $competition_id";
$recipe_result = mysqli_query($con, $recipe_query);

if (mysqli_num_rows($recipe_result) == 0) {
    header("Location: competition.php?error=recipe_not_found");
    exit();
}

$recipe = mysqli_fetch_assoc($recipe_result);

// Check if user has voted for this recipe
$vote_query = "SELECT * FROM votes WHERE user_id = $user_id AND recipe_id = $recipe_id";
$vote_result = mysqli_query($con, $vote_query);
$has_voted = mysqli_num_rows($vote_result) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['recipe_name']); ?> - Recipe Details</title>
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
        }

        .header-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 40px 20px;
            color: white;
            text-align: center;
            position: relative;
            margin-bottom: 40px;
            /* Add text shadow for better contrast */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Add overlay to darken background slightly */
        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.2);
            pointer-events: none;
        }

        .header-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(to top right, #f5f5f5 49%, transparent 51%);
        }

         /* Update header content styles */
        .header-content {
            position: relative;
            z-index: 1;
        }

        .container {
            max-width: 800px;
            margin: -20px auto 40px;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: white;
            color: #1e3c72;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid #1e3c72;
        }

        .back-btn:hover {
            background: #1e3c72;
            color: white;
            transform: translateX(-5px);
        }

        .back-btn i {
            font-size: 1.2em;
        }

        .recipe-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .recipe-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }

        .recipe-title {
            font-size: 2.5em;
            color: #ffffff;
            margin-bottom: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .competition-name {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.2em;
            font-weight: 500;
            background: rgba(0, 0, 0, 0.2);
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
        }

        .recipe-section {
            margin-bottom: 30px;
        }

        .section-title {
            color: #2a5298;
            font-size: 1.4em;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-content {
            color: #444;
            line-height: 1.8;
            font-size: 1.1em;
            white-space: pre-line;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .vote-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        .vote-count {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #1e3c72;
            font-size: 1.2em;
            font-weight: 600;
        }

        .vote-count i {
            color: #ffc107;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .recipe-card {
                padding: 20px;
            }

            .recipe-title {
                font-size: 1.8em;
            }

            .section-title {
                font-size: 1.2em;
            }

            .section-content {
                font-size: 1em;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="header-content">
            <h1 class="recipe-title"><?php echo htmlspecialchars($recipe['recipe_name']); ?></h1>
            <p class="competition-name"><?php echo htmlspecialchars($recipe['competition_name']); ?></p>
        </div>
    </div>

    <div class="container">
        <a href="view_vote.php?comp_id=<?php echo $competition_id; ?>" class="back-btn">
            <i class='bx bx-arrow-back'></i>
            Back to Voting
        </a>

        <div class="recipe-card">
            <div class="recipe-header">
                <div class="vote-count">
                    <i class='bx bxs-star'></i>
                    <span><?php echo $recipe['vote_count']; ?> votes</span>
                    <?php if ($has_voted): ?>
                        <span style="color: #28a745; margin-left: 10px;">
                            <i class='bx bx-check-circle'></i> You voted for this recipe
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="recipe-section">
                <h3 class="section-title">
                    <i class='bx bx-message-square-detail'></i>
                    Description
                </h3>
                <div class="section-content">
                    <?php echo nl2br(htmlspecialchars($recipe['description'])); ?>
                </div>
            </div>

            <div class="recipe-section">
                <h3 class="section-title">
                    <i class='bx bx-purchase-tag'></i>
                    Ingredients
                </h3>
                <div class="section-content">
                    <?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?>
                </div>
            </div>

            <div class="recipe-section">
                <h3 class="section-title">
                    <i class='bx bx-list-ol'></i>
                    Steps
                </h3>
                <div class="section-content">
                    <?php echo nl2br(htmlspecialchars($recipe['steps'])); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>