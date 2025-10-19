<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login2.html");
    exit();
}

header('Content-Type: application/json');

$host = "mysql.cs.mcgill.ca";
$dbname = "test";
$username = "users-909468";
$password = "4rTPckXCr7qL";

try {
    // Connect to the database
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the query
    $stmt = $db->prepare("SELECT User.firstName, User.lastName, User.userId 
                          FROM User 
                          JOIN Members ON User.userId = Members.userId
                          WHERE Members.email LIKE '%@mcgill.ca'");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output results
    if ($results) {
        echo json_encode($results);
    } else {
        echo json_encode(["error" => "No matching professors found."]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
