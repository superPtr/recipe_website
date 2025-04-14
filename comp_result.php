<?php
require('db.php');
session_start();

// For testing purposes
$user_id = 1;
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

$user_id = $_SESSION['user_id'];
$competition_id = isset($_GET['comp_id']) ? (int)$_GET['comp_id'] : 0;

// Get competition details
$comp_query = "SELECT * FROM competitions WHERE competition_id = $competition_id";
$comp_result = mysqli_query($con, $comp_query);

if (mysqli_num_rows($comp_result) == 0) {
    header("Location: competition.php?error=competition_not_found");
    exit();
}

$competition = mysqli_fetch_assoc($comp_result);

// Get total participants count
$count_query = "SELECT COUNT(*) as total FROM competition_recipes WHERE competition_id = $competition_id";
$count_result = mysqli_query($con, $count_query);
$total_participants = mysqli_fetch_assoc($count_result)['total'];

// Determine how many winners to show
$winners_to_show = $total_participants >= 15 ? 10 : 3;

// Get ranked recipes with vote counts
$recipes_query = "SELECT cr.*, 
                    (SELECT COUNT(*) FROM votes v WHERE v.recipe_id = cr.compRecipe_id) as vote_count
                 FROM competition_recipes cr
                 WHERE cr.competition_id = $competition_id
                 ORDER BY vote_count DESC";
$recipes_result = mysqli_query($con, $recipes_query);

$all_recipes = [];
while ($row = mysqli_fetch_assoc($recipes_result)) {
    $all_recipes[] = $row;
}

// Initially show only top winners
$show_all = isset($_GET['show_all']) && $_GET['show_all'] == 1;
$display_recipes = $show_all ? $all_recipes : array_slice($all_recipes, 0, $winners_to_show);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition Results - <?php echo htmlspecialchars($competition['competition_name']); ?></title>
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
        }

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

        .header-content {
            position: relative;
            z-index: 1;
        }

        .competition-title {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .competition-date {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .container {
            max-width: 1000px;
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

        .winner-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .recipe-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .recipe-card:hover {
            transform: translateY(-5px);
        }

        .winner-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2em;
        }

        .winner-1 { background: #FFD700; }
        .winner-2 { background: #C0C0C0; }
        .winner-3 { background: #CD7F32; }

        .recipe-name {
            font-size: 1.3em;
            color: #1e3c72;
            margin-bottom: 15px;
            padding-right: 45px;
        }

        .vote-count {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #1e3c72;
            font-weight: 600;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .vote-count i {
            color: #ffc107;
        }

        .show-more-btn {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 30px auto;
            padding: 12px 24px;
            background: #2a5298;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .show-more-btn:hover {
            background: #1e3c72;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .total-participants {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 1.1em;
        }

        @media (max-width: 768px) {
            .competition-title {
                font-size: 2em;
            }

            .winner-section {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="header-content">
            <h1 class="competition-title"><?php echo htmlspecialchars($competition['competition_name']); ?></h1>
            <p class="competition-date">
                Competition ended: <?php echo date('F j, Y', strtotime($competition['end_time'])); ?>
            </p>
        </div>
    </div>

    <div class="container">
        <a href="competition.php" class="back-btn">
            <i class='bx bx-arrow-back'></i>
            Back to Competitions
        </a>

        <p class="total-participants">
            <i class='bx bx-group'></i>
            Total Participants: <?php echo $total_participants; ?>
        </p>

        <div class="winner-section">
            <?php foreach ($display_recipes as $index => $recipe): ?>
                <div class="recipe-card">
                    <?php if ($index < 3): ?>
                        <div class="winner-badge winner-<?php echo $index + 1; ?>">
                            <?php echo $index + 1; ?>
                        </div>
                    <?php endif; ?>

                    <h3 class="recipe-name"><?php echo htmlspecialchars($recipe['recipe_name']); ?></h3>
                    
                    <a href="view_recipe_details.php?comp_id=<?php echo $competition_id; ?>&recipe_id=<?php echo $recipe['compRecipe_id']; ?>" 
                       class="view-recipe-btn"
                       style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f8f9fa; color: #1e3c72; text-decoration: none; border-radius: 6px; margin-top: 10px; transition: all 0.3s ease;">
                        <i class='bx bx-food-menu'></i>
                        View Recipe
                    </a>

                    <div class="vote-count">
                        <i class='bx bxs-star'></i>
                        <?php echo $recipe['vote_count']; ?> votes
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$show_all && count($all_recipes) > $winners_to_show): ?>
            <a href="?comp_id=<?php echo $competition_id; ?>&show_all=1" class="show-more-btn">
                <i class='bx bx-chevron-down'></i>
                Show All Participants (<?php echo count($all_recipes); ?>)
            </a>
        <?php endif; ?>
    </div>

</body>
</html>