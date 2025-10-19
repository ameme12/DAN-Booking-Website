<?php
session_start();

//Author: Neelab Wafasharefe

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login2.html");
    exit();
}
?>
