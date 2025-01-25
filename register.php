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
$aadhar_num = isset($_POST['aadhar_num']) ? $_POST['aadhar_num'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;
$adress = isset($_POST['adress']) ? $_POST['adress'] : null;
$phone = isset($_POST['phone']) ? $_POST['phone'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;

// Validate required fields
if (!$aadhar_num || !$name || !$password || !$confirm_password || !$adress || !$phone || !$email) {
    die("All fields are required!");
}

// Validate Aadhaar number (maximum 10 characters, numeric only)
if (strlen($aadhar_num) > 10 || !ctype_digit($aadhar_num)) {
    die("Invalid Aadhaar number! It must be up to 10 digits only.");
}

// Validate phone number (10 digits, numeric only)
if (strlen($phone) != 10 || !ctype_digit($phone)) {
    die("Invalid phone number! It must be exactly 10 digits.");
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format!");
}

// Check if passwords match
if ($password !== $confirm_password) {
    die("Passwords do not match!");
}

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO Register (aadhar_num, name, password, adress, phone, email) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("ssssss", $aadhar_num, $name, $password, $adress, $phone, $email); // Store plain password

// Execute the statement
if ($stmt->execute()) {
    // Registration successful, redirect to login page
    header('Location: login.html');
    exit(); // Ensure the script ends here
} else {
    die("Error: " . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$conn->close();
