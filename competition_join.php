<?php
require('db.php');
session_start();

// // Check if user is logged in
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Get competition_id from URL
$competition_id = isset($_GET['comp_id']) ? (int)$_GET['comp_id'] : 0;

// Validate competition exists and is upcoming
$comp_check = mysqli_query($con, "SELECT * FROM competitions WHERE competition_id = $competition_id AND status = 'upcoming'");
if (mysqli_num_rows($comp_check) == 0) {
    header("Location: competition.php");
    exit();
}

// $user_id = $_SESSION['user_id'];
$user_id =2 ;

// Get user's recipes
$retrieve_recipe = "SELECT recipe_id, recipe_name FROM recipes WHERE user_id = $user_id";
$retrieve_recipe_result = mysqli_query($con, $retrieve_recipe);
$recipe_amount = mysqli_num_rows($retrieve_recipe_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Competition</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: #1e3c72;
        }

        .header h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .recipe-selection {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            font-size: 1.1em;
        }

        select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            color: #333;
            background-color: white;
            transition: all 0.3s ease;
        }

        select:hover, select:focus,
        textarea:hover, textarea:focus {
            border-color: #1e3c72;
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .recipe-details {
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        .recipe-details h3 {
            color: #1e3c72;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        .submit-btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            font-size: 1.1em;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3);
        }

        .error-message {
            color: #dc3545;
            background-color: #ffe0e3;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Join Competition</h1>
            <p>Select your recipe to enter the competition</p>
        </div>

        <?php if ($recipe_amount == 0): ?>
            <div class="error-message">
                <i class='bx bx-error-circle'></i>
                You don't have any recipes yet. <a href="create_recipe.php" style="color: #1e3c72;">Create a recipe first!</a>
            </div>
        <?php else: ?>
            <form action="submit_recipe.php" method="post" id="joinForm">
                <input type="hidden" name="competition_id" value="<?php echo $competition_id; ?>">
                
                <div class="recipe-selection">
                    <div class="form-group">
                        <label for="recipes">
                            <i class='bx bx-food-menu'></i> Choose your recipe:
                        </label>
                        <select name="recipe_id" id="recipes" required>
                            <option value="">Select a recipe</option>
                            <?php while ($row = mysqli_fetch_assoc($retrieve_recipe_result)): ?>
                                <option value="<?php echo $row['recipe_id']; ?>">
                                    <?php echo htmlspecialchars($row['recipe_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div id="recipeDetails" class="recipe-details" style="display: none;"></div>

                <button type="submit" class="submit-btn" id="submitBtn" disabled>
                    <i class='bx bx-check-circle'></i> Submit Recipe
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script>
    $(document).ready(function() {
        $('#recipes').change(function() {
            var recipeId = $(this).val();
            var submitBtn = $('#submitBtn');
            
            if (recipeId) {
                $('#recipeDetails').show();
                submitBtn.prop('disabled', true);
                
                $.ajax({
                    url: 'get_recipe_details.php',
                    type: 'GET',
                    data: { recipe_id: recipeId },
                    success: function(data) {
                        $('#recipeDetails').html(data);
                        submitBtn.prop('disabled', false);
                    },
                    error: function() {
                        $('#recipeDetails').html('<div class="error-message">Error loading recipe details. Please try again.</div>');
                        submitBtn.prop('disabled', true);
                    }
                });
            } else {
                $('#recipeDetails').hide();
                submitBtn.prop('disabled', true);
            }
        });
    });
    </script>
</body>
</html>