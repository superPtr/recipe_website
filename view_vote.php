<?php
require('db.php');
session_start();

// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

//$user_id = $_SESSION['user_id'];
$user_id = 1;
$competition_id = isset($_GET['comp_id']) ? (int)$_GET['comp_id'] : 0;

// Verify competition exists and is ongoing
$comp_query = "SELECT * FROM competitions WHERE competition_id = $competition_id AND status = 'ongoing'";
$comp_result = mysqli_query($con, $comp_query);

if (mysqli_num_rows($comp_result) == 0) {
    header("Location: competition.php?error=invalid_competition");
    exit();
}

$competition = mysqli_fetch_assoc($comp_result);

// Get user's current vote
$vote_query = "SELECT recipe_id FROM votes WHERE user_id = $user_id AND competition_id = $competition_id";
$vote_result = mysqli_query($con, $vote_query);
$current_vote = mysqli_num_rows($vote_result) > 0 ? mysqli_fetch_assoc($vote_result)['recipe_id'] : null;

// Get total recipes count for pagination
$count_query = "SELECT COUNT(*) as total FROM competition_recipes WHERE competition_id = $competition_id";
$count_result = mysqli_query($con, $count_query);
$total_recipes = mysqli_fetch_assoc($count_result)['total'];

// Pagination
$recipes_per_page = 6;
$total_pages = ceil($total_recipes / $recipes_per_page);
$current_page = isset($_GET['page']) ? max(1, min($total_pages, (int)$_GET['page'])) : 1;
$offset = ($current_page - 1) * $recipes_per_page;

// Get recipes for current page with vote counts
$recipes_query = "SELECT cr.*, 
                    (SELECT COUNT(*) FROM votes v WHERE v.recipe_id = cr.compRecipe_id) as vote_count
                 FROM competition_recipes cr
                 WHERE cr.competition_id = $competition_id
                 ORDER BY vote_count DESC, cr.submitted_at ASC
                 LIMIT $offset, $recipes_per_page";
$recipes_result = mysqli_query($con, $recipes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote for Recipes - <?php echo htmlspecialchars($competition['competition_name']); ?></title>
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

        .header-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: linear-gradient(to top right, #f5f5f5 49%, transparent 51%);
        }

        .competition-info {
            max-width: 800px;
            margin: 0 auto 20px;
            text-align: center;
        }

        .competition-name {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .competition-time {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: -20px auto 40px;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .recipes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .recipe-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .recipe-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .recipe-title {
            font-size: 1.4em;
            color: #1e3c72;
            margin-bottom: 15px;
        }

        .recipe-content {
            margin-bottom: 20px;
        }

        .recipe-section {
            margin-bottom: 15px;
        }

        .recipe-section h4 {
            color: #2a5298;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .recipe-text {
            color: #666;
            line-height: 1.6;
        }

        .vote-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .vote-count {
            color: #1e3c72;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .vote-btn {
            padding: 8px 20px;
            border: 2px solid #1e3c72;
            background: white;
            color: #1e3c72;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .vote-btn:hover:not(:disabled) {
            background: #1e3c72;
            color: white;
        }

        .vote-btn.voted {
            background: #1e3c72;
            color: white;
        }

        .vote-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: #999;
            color: #999;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }

        .pagination a, .pagination span {
            padding: 8px 16px;
            border: 1px solid #1e3c72;
            color: #1e3c72;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #1e3c72;
            color: white;
        }

        .pagination .current {
            background: #1e3c72;
            color: white;
        }

        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            z-index: 1000;
            display: none;
            animation: slideIn 0.5s ease;
        }

        #notification.success {
            background: #28a745;
        }

        #notification.error {
            background: #dc3545;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .loading {
            pointer-events: none;
            opacity: 0.7;
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

        .view-recipe-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #2a5298;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .view-recipe-btn:hover {
            background: #1e3c72;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .recipes-grid {
                grid-template-columns: 1fr;
            }

            .competition-name {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="competition-info">
            <h1 class="competition-name"><?php echo htmlspecialchars($competition['competition_name']); ?></h1>
            <p class="competition-time">
                Voting ends: <?php echo date('F j, Y, g:i a', strtotime($competition['end_time'])); ?>
            </p>
        </div>
    </div>

    <div class="container">
        <a href="competition.php" class="back-btn">
            <i class='bx bx-arrow-back'></i>
            Back to Competitions
        </a>
        <div class="recipes-grid">
            <?php while ($recipe = mysqli_fetch_assoc($recipes_result)): ?>
                <div class="recipe-card">
                    <h3 class="recipe-title"><?php echo htmlspecialchars($recipe['recipe_name']); ?></h3>
                    
                    <div class="recipe-content">
                        <div class="recipe-section">
                            <h4><i class='bx bx-message-square-detail'></i> Description</h4>
                            <p class="recipe-text"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
                        </div>
                    </div>
                    <a href="view_recipe_details.php?comp_id=<?php echo $competition_id; ?>&recipe_id=<?php echo $recipe['compRecipe_id']; ?>" class="view-recipe-btn">
                        <i class='bx bx-food-menu'></i>
                        View Full Recipe
                    </a>

                    <div class="vote-section">
                        <div class="vote-count">
                            <i class='bx bx-star'></i>
                            <span id="vote-count-<?php echo $recipe['compRecipe_id']; ?>">
                                <?php echo $recipe['vote_count']; ?> votes
                            </span>
                        </div>
                        <button class="vote-btn <?php echo ($current_vote == $recipe['compRecipe_id']) ? 'voted' : ''; ?>"
                                onclick="toggleVote(this, <?php echo $recipe['compRecipe_id']; ?>)"
                                <?php echo ($current_vote && $current_vote != $recipe['compRecipe_id']) ? 'disabled' : ''; ?>>
                            <i class='bx bx-star'></i>
                            <span>
                                <?php echo ($current_vote == $recipe['compRecipe_id']) ? 'Voted' : 'Vote'; ?>
                            </span>
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i == $current_page): ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?comp_id=<?php echo $competition_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>

    <div id="notification"></div>

    <script>
        function toggleVote(button, recipeId) {
            button.classList.add('loading');
            
            fetch('toggle_vote.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `recipe_id=${recipeId}&competition_id=<?php echo $competition_id; ?>`
            })
            .then(response => response.json())
            .then(data => {
                button.classList.remove('loading');
                
                if (data.success) {
                    // Update vote count
                    document.getElementById(`vote-count-${recipeId}`).textContent = `${data.vote_count} votes`;
                    
                    // Update button states
                    const allVoteButtons = document.querySelectorAll('.vote-btn');
                    allVoteButtons.forEach(btn => {
                        if (data.voted && btn !== button) {
                            btn.disabled = true;
                        } else {
                            btn.disabled = false;
                        }
                    });
                    
                    // Toggle voted state
                    if (data.voted) {
                        button.classList.add('voted');
                        button.querySelector('span').textContent = 'Voted';
                    } else {
                        button.classList.remove('voted');
                        button.querySelector('span').textContent = 'Vote';
                    }
                    
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                button.classList.remove('loading');
                showNotification('Error processing vote. Please try again.', 'error');
            });
        }

        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = type;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>