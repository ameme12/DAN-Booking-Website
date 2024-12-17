<?php

session_start();

// Enforce secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);

// Database Connection (Replace with your credentials)
$host = 'localhost';
$dbname = 'your_database_name';
$user = 'your_username';
$pass = 'your_password';

try {
    // Connect to database with PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize inputs
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);

    // Prepare SQL query to fetch hashed password and userID for the email
    $stmt = $pdo->prepare("SELECT userID, password FROM Members WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Start session and store userID
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['email'] = $email;

        // Redirect to dashboard or home page
        header('Location: dashboard.php');
        exit();
    } else {
        // Invalid credentials message
        echo "Invalid email or password.";
    }
}

?>