<?php
    // Start the session
    session_start();

    // Clear all session data
    session_unset();

    // Destroy the session
    session_destroy();

    // Redirect to the home page or any other desired page after logout
    header("Location: home.php");
    exit;
?>
