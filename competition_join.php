<?php
require('db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Competitition</title>
</head>
<body>
    <?php
        $com_id = $_GET['comp_id'];
        $user_id = 2;

        $retrive_recipe = "SELECT * FROM recipes WHERE user_id = $user_id";
        $retrieve_recipe_result = mysqli($con, $retrieve_recipe);
        $recipe_amount = mysqli_num_rows($retrieve_recipe);

    ?>
    
    <div>
        <label for="recipes">Choose a recipe:</label>
            <select name="recipes" id="recipes">
                <?php
                    if($recipe_amount > 0){
                        $count = 0;
                        while ($row = mysqli_fetch_assoc($retrieve_recipe_result)) {
                            $recipe_id = $row['recipe_id'];
                            $recipe_name = $row['recipe_name'];
                            echo "<option value='$recipe_id'>$count - $recipe_name</option>";
                        }
                    } else{
                        echo"<option value=''>No recipes found!</option>"
                    }
                ?>
            </select>
            
        <form action="" >
            
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>