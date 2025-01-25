<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'Vazha';

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Get form data
$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

// Validate required fields
if (!$email || !$password) {
    die("Both email and password are required!");
}

// Prepare the SQL statement to fetch the password by email
$stmt = $conn->prepare("SELECT password FROM Register WHERE email = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("s", $email);

// Execute the statement
$stmt->execute();

// Bind result variables
$stmt->bind_result($stored_password);

// Check if a user is found
if ($stmt->fetch()) {
    // Directly compare passwords (plain-text passwords stored)
    if ($password === $stored_password) {
        // Start a session and store user email
        session_start();
        $_SESSION['email'] = $email;

        // Redirect to the dashboard
        header('Location: byers.html');
        exit();
    } else {
        die("Incorrect password!");
    }
} else {
    die("No user found with this email!");
}

// Close the statement and connection
$stmt->close();
$conn->close();
