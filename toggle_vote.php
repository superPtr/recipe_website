<?php
require('db.php');
session_start();

header('Content-Type: application/json');

// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(['success' => false, 'message' => 'Please login to vote']);
//     exit();
// }

if (!isset($_POST['recipe_id']) || !isset($_POST['competition_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

//$user_id = $_SESSION['user_id'];
$user_id = 1;
$recipe_id = (int)$_POST['recipe_id'];
$competition_id = (int)$_POST['competition_id'];

// Check if competition is ongoing
$comp_check = mysqli_query($con, "SELECT status FROM competitions WHERE competition_id = $competition_id");
if (!$comp_check || mysqli_fetch_assoc($comp_check)['status'] !== 'ongoing') {
    echo json_encode(['success' => false, 'message' => 'Voting is not available for this competition']);
    exit();
}

// Check if user has already voted
$vote_check = mysqli_query($con, "SELECT * FROM votes WHERE user_id = $user_id AND competition_id = $competition_id");
$has_voted = mysqli_num_rows($vote_check) > 0;
$current_vote = $has_voted ? mysqli_fetch_assoc($vote_check)['recipe_id'] : null;

if ($has_voted && $current_vote == $recipe_id) {
    // Remove vote
    mysqli_query($con, "DELETE FROM votes WHERE user_id = $user_id AND competition_id = $competition_id");
    $message = 'Vote removed successfully';
    $voted = false;
} else if (!$has_voted) {
    // Add new vote
    mysqli_query($con, "INSERT INTO votes (user_id, recipe_id, competition_id) VALUES ($user_id, $recipe_id, $competition_id)");
    $message = 'Vote recorded successfully';
    $voted = true;
} else {
    echo json_encode(['success' => false, 'message' => 'You can only vote for one recipe']);
    exit();
}

// Get updated vote count
$count_query = mysqli_query($con, "SELECT COUNT(*) as count FROM votes WHERE recipe_id = $recipe_id");
$vote_count = mysqli_fetch_assoc($count_query)['count'];

echo json_encode([
    'success' => true,
    'message' => $message,
    'voted' => $voted,
    'vote_count' => $vote_count
]);