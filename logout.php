<?php
require_once 'config.php';

// Log activity if user is logged in
if (isLoggedIn()) {
    logActivity($_SESSION['user']['id'], 'logout', 'User logged out');
}

// Destroy session
session_destroy();

// Redirect to home page
redirect('index.php');
?>
