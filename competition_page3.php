<?php

require('db.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competition Page</title>
    <script>
        function confirmJoin(comp_id) {
            if (confirm("Are you sure you want to join this competition?")) {
                window.location.href = "competition_join.php?comp_id=" + comp_id;
            }
        }
    </script>
</head>
<body>

<?php
$comp_id = $_GET['comp_id'];
$query = "SELECT * FROM competitions WHERE competition_id = $comp_id";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

$competition_name = $row['competition_name'];
$description      = $row['description'];
$start_date_raw   = $row['start_date'];
$start_date       = date("Y-m-d", strtotime($start_date_raw));
$end_date_raw     = $row['end_date'];
$end_date         = date("Y-m-d", strtotime($end_date_raw));
$allowRegister    = $row['allowRegister']; // Assuming this field exists in your table

// Check if the user is already joined (replace with your actual logic)
// This is a placeholder, you'll need to adapt it to your database structure
$user_id = 2; // Replace with the actual user ID from your session or authentication
$check_entry_query = "SELECT entry_id FROM competition_entries WHERE competition_id = $comp_id AND user_id = $user_id";
$check_entry_result = mysqli_query($con, $check_entry_query);

$already_joined = mysqli_num_rows($check_entry_result) > 0;

$tooltip_message = ""; // Initialize the tooltip message

?>

<div>
    <h2><?php echo $competition_name ?></h2>
    <p>Start at: <?php echo $start_date ?> End at: <?php echo $end_date ?></p>
    <p><?php echo $description ?></p>

    <?php
    if ($allowRegister && !$already_joined) {
        echo '<button onclick="confirmJoin(' . $comp_id . ')">Join</button>';
    } else {
        if ($already_joined) {
            $tooltip_message = "You have already joined this competition.";
        } else {
            $tooltip_message = "Registration is currently disabled for this competition.";
        }
        echo '<button disabled title="' . htmlspecialchars($tooltip_message) . '">Join</button>';
    }
    ?>
</div>

</body>
</html>