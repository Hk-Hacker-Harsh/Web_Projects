<?php
session_start();

// Check if the user is logged in AND if they are an admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If not an admin, kick them out to the admin login page
    header("Location: index.php");
    exit();
}
?>