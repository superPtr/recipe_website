<?php
require('db.php');

$user_id = 2;
$recipe_id = $_POST['recipe_id'];
$recipe_name = $_POST['recipe_name'];
$ingredient = $_POST['ingredient'];
$steps = $_POST['steps'];
$description = $_POST['description'];

$query = "INSERT INTO competition_recipes (user_id, recipe_name, ingredients, steps, description)
            VALUES ($user_id, $recipe_name, $ingredient, $steps, $description)";

if (mysqli_query($con, $query)) {
    echo "Recipe submitted successfully!";
} else {
    echo "Error submitting recipe: " . mysqli_error($con);
}
?>