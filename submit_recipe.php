<?php
require('db.php');

// initialize response array
$response = array(
    'status' => 'error',
    'message' => '',
    'redirect' => ''
);

// check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $user_id = 2;
    $recipe_id = $_POST['recipe_id'];
    $recipe_name = $_POST['recipe_name'];
    $ingredient = $_POST['ingredient'];
    $steps = $_POST['steps'];
    $description = $_POST['description'];

    $query = "INSERT INTO competition_recipes (user_id, recipe_name, ingredients, steps, description)
                VALUES ($user_id, $recipe_name, $ingredient, $steps, $description)";

    if(mysqli_query($con, $query)) {
        $response['status'] = 'success';
        $response['message'] = 'Recipe submitted successfully!';
        $response['redirect'] = 'competition_page'
    } else {
        echo "Error submitting recipe: " . mysqli_error($con);
    }
}




?>