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
                window.location.href = "competition_join2.php?comp_id=" + comp_id;
            }
        }

        function goToVote(comp_id) {
            window.location.href = "vote.php?comp_id=" + comp_id; // Replace vote.php with your actual vote page
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
$allowRegister    = $row['allowRegister'];
$status           = $row['status']; // Get the competition status

// Check if the user is already joined (replace with your actual logic)
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
    if ($status == 'upcoming' && $allowRegister && !$already_joined) {
        echo '<button onclick="confirmJoin(' . $comp_id . ')">Join</button>';
    } elseif ($status == 'ongoing' && !$allowRegister) {
        // Decide whether to show a disabled "Join" button or not
        // Option 1: Show only the "Vote" button
        echo '<button onclick="goToVote(' . $comp_id . ')">Vote</button>';

        // Option 2: Show both a disabled "Join" button and the "Vote" button
        // $tooltip_message = "Registration is closed for this competition.";
        // echo '<button disabled title="' . htmlspecialchars($tooltip_message) . '">Join</button>';
        // echo '<button onclick="goToVote(' . $comp_id . ')">Vote</button>';
    } else {
        // Handle other cases (e.g., "completed" status)
        if ($status == 'completed') {
            $tooltip_message = "This competition is completed.";
        } elseif ($already_joined) {
            $tooltip_message = "You have already joined this competition.";
        } else {
            $tooltip_message = "Registration is not open yet."; // Or a more appropriate message
        }
        echo '<button disabled title="' . htmlspecialchars($tooltip_message) . '">Join</button>';
    }
    ?>
</div>

</body>
</html>