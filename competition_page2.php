<?php
require('db.php');

// Check if comp_id is set and is a number
if (isset($_GET['comp_id']) && is_numeric($_GET['comp_id'])) {
    $comp_id = $_GET['comp_id'];

    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM competition WHERE competition_id = ?";
    $stmt = mysqli_prepare($con, $query);

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "i", $comp_id);  // "i" indicates integer

    // Execute the query
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if a row was found
    if ($row = mysqli_fetch_assoc($result)) {
        $competition_name = htmlspecialchars($row['competition_name']); //Sanitize output
        $description = htmlspecialchars($row['description']); //Sanitize output
        $start_date = htmlspecialchars($row['start_date']); //Sanitize output
        $end_date = htmlspecialchars($row['end_date']); //Sanitize output
    } else {
        // Handle the case where no competition is found with that ID
        echo "Competition not found.";
        exit;
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    // Handle the case where comp_id is missing or invalid
    echo "Invalid competition ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $competition_name; ?></title>
</head>
<body>
    <div>
        <h2><?php echo $competition_name; ?></h2>
        <p>Start at: <?php echo $start_date; ?> End at: <?php echo $end_date; ?></p>
        <p><?php echo $description; ?></p>
    </div>
</body>
</html>