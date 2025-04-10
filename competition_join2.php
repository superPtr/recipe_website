<?php
require('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Competition</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>

<?php
$user_id = 2;

$retrieve_recipe = "SELECT recipe_id, recipe_name FROM recipes WHERE user_id = $user_id";
$retrieve_recipe_result = mysqli_query($con, $retrieve_recipe);
$recipe_amount = mysqli_num_rows($retrieve_recipe_result);
?>

<div>
    <label for="recipes">Choose a recipe:</label>
    <select name="recipes" id="recipes">
        <option value="">Select a recipe</option>
        <?php
        if($recipe_amount > 0){
            while ($row = mysqli_fetch_assoc($retrieve_recipe_result)) {
                $recipe_id = $row['recipe_id'];
                $recipe_name = $row['recipe_name'];
                echo "<option value='$recipe_id'>$recipe_name</option>";
            }
        } else{
            echo "<option value=''>No recipes found!</option>";
        }
        ?>
    </select>
</div>


<form action="submit_recipe.php" method="post">
    <div id="recipeDetails"></div>
    <!-- Recipe details will be loaded here -->
    <button type="submit">Submit</button>
</form>

<script>
$(document).ready(function() {
    $('#recipes').change(function() {
        var recipeId = $(this).val();
        if (recipeId) {
            $.ajax({
                url: 'get_recipe_details.php', // Create this file
                type: 'GET',
                data: {recipe_id: recipeId},
                success: function(data) {
                    $('#recipeDetails').html(data);
                }
            });
        } else {
            $('#recipeDetails').html(''); // Clear details if no recipe is selected
        }
    });
});
</script>

</body>
</html>

