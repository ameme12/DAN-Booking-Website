<?php
session_start();

// Author: Neelab Wafasharefe

// Database connection
$host = "mysql.cs.mcgill.ca";
$dbname = "test";
$user = "users-909468";
$pass = "4rTPckXCr7qL";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    try {
        // Step 1: Check if email already exists
        $checkEmail = $pdo->prepare("SELECT COUNT(*) FROM Members WHERE email = :email");
        $checkEmail->bindParam(':email', $email, PDO::PARAM_STR);
        $checkEmail->execute();

        if ($checkEmail->fetchColumn() > 0) {
            die("Error: This email is already registered. Please use a different email.");
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Start transaction
        $pdo->beginTransaction();

        // Step 1: Insert into User table
        $stmtUser = $pdo->prepare("INSERT INTO User (firstName, lastName) VALUES (:firstName, :lastName)");
        $stmtUser->bindParam(':firstName', $firstName, PDO::PARAM_STR);
        $stmtUser->bindParam(':lastName', $lastName, PDO::PARAM_STR);
        $stmtUser->execute();

        // Get the generated userId
        $userId = $pdo->lastInsertId();

        // Step 2: Insert into Members table
        $stmtMembers = $pdo->prepare("INSERT INTO Members (email, password, userId) VALUES (:email, :password, :userId)");
        $stmtMembers->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtMembers->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmtMembers->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtMembers->execute();

        // Commit the transaction
        $pdo->commit();

        // Redirect to login page
        header("Location: login2.html");
        exit();
    } catch (PDOException $e) {
        // Rollback on error
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>
