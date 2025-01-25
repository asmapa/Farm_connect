<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'Vazha';

// Establish connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $aadhar_num = $conn->real_escape_string($_POST['aadhar_num']);
    $name = $conn->real_escape_string($_POST['name']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $farm_type = $conn->real_escape_string($_POST['farm_type']);

    // Validate phone number (only digits, spaces, and + allowed)
    if (!preg_match('/^[\d\s\+]+$/', $phone)) {
        echo "Invalid phone number format.";
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match. Please try again.";
        exit();
    }

    // Check if Aadhar number or email already exists using prepared statements
    $check_sql = "SELECT * FROM FarmerRegister WHERE aadhar_num = ? OR email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $aadhar_num, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Aadhar number or Email already exists. Please use a different one.";
        exit();
    }

    // SQL query to insert data using prepared statements (no password hash)
    $sql = "INSERT INTO FarmerRegister (aadhar_num, name, password, address, phone, email, type) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $aadhar_num, $name, $password, $address, $phone, $email, $farm_type);

    // Execute the query
    if ($stmt->execute()) {
        header('Location: Farmerlogin.html');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Close connection
$conn->close();
