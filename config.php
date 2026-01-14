<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection parameters
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sport_management');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    error_log("Database Connection Failed: " . $conn->connect_error);
    die("Unable to connect to database. Please try again later.");
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Helper function for escaping HTML
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Sanitization function
function sanitize_input($data) {
    global $conn;
    if (!isset($conn)) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $conn->real_escape_string($data);
}

// Email validation
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Date validation
function validate_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}


?>