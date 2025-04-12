<?php
session_start();

// Clear only this admin's session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page with a message
header("Location: admin_login.php?logout=success");
exit();
?>