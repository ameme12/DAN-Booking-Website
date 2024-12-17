<?php

// Start session and database connection
session_start();

// Database connection
$host = "mysql.cs.mcgill.ca";
$dbname = "test";
$user = "users-909468";
$pass = "4rTPckXCr7qL";

$conn = new mysqli($host, $user, $pass, $dbname);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Check if the email already exists
    $stmt = $pdo->prepare("SELECT email FROM Members WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        die("This email is already registered.");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->execute();

        echo "Registration successful! You can now log in.";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

?>