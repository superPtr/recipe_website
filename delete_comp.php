<?php
session_start();
require('db.php');

// Check if user is not logged in as admin
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: admin_login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity timestamp

// validate
if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header("Location: view_comp.php");
    exit();
}

if (!isset($_GET['comp_id']) || empty($_GET['comp_id'])) {
    $_SESSION['error'] = "Invalid competition ID";
    header("Location: view_comp.php");
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

header("Location: view_comp.php");
exit();
?>