<?php
session_start();
require('db.php');

// check if user is logged in (authentication)

// validate
if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header("Location: view_comp5.php");
    exit();
}

if (!isset($_GET['comp_id']) || empty($_GET['comp_id'])) {
    $_SESSION['error'] = "Invalid competition ID";
    header("Location: view_comp5.php");
    exit();
}


$comp_id = $_GET['comp_id'];
$query = "DELETE FROM competitions WHERE competition_id = $comp_id";

try{
    if(mysqli_query($con, $query)){
        $_SESSION['del_success'] = "Competition deleted successfully.";
    } else{
        $_SESSION['del_error'] = "Error deleting competition.";
    }
} catch(Exception $e){
    $_SESSION['del_error'] = "Database error occurred";
}

header("Location: view_comp5.php");
exit();
?>