<?php
session_start(); // Start session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['teacher_id'])) {
        $_SESSION['selectTeac'] = $_POST['teacher_id']; // Set session dynamically
        echo "success"; // Send response to AJAX
    } else {
        echo "error"; // Send error if teacher_id is missing
    }
    exit;
}

