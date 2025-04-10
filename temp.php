<?php
session_start();
require('db.php');

// Initialize response array
$response = array(
    'status' => 'error',
    'message' => '',
    'redirect' => ''
);

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required fields
        $required_fields = ['recipe_id', 'recipe_name', 'ingredient', 'steps', 'description'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        $user_id = 2; // In production, this should come from session
        
        // Sanitize and prepare data
        $recipe_id = mysqli_real_escape_string($con, $_POST['recipe_id']);
        $recipe_name = mysqli_real_escape_string($con, $_POST['recipe_name']);
        $ingredient = mysqli_real_escape_string($con, $_POST['ingredient']);
        $steps = mysqli_real_escape_string($con, $_POST['steps']);
        $description = mysqli_real_escape_string($con, $_POST['description']);

        // Prepare SQL using prepared statements
        $stmt = $con->prepare("INSERT INTO competition_recipes (user_id, recipe_name, ingredients, steps, description) VALUES (?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $con->error);
        }

        $stmt->bind_param("issss", $user_id, $recipe_name, $ingredient, $steps, $description);
        
        // Execute the statement
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Recipe submitted successfully!';
            $response['redirect'] = 'competition.php';
        } else {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $stmt->close();

    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
        // Log the error for administrators
        error_log("Recipe submission error: " . $e->getMessage());
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>