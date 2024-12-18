<?php

header('content-type: application/json');

$host = "mysql.cs.mcgill.ca";
$dbname = "test";
$username = "users-909468";
$password = "4rTPckXCr7qL";

try{
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $stmt = $db->prepare("SELECT User.firstName, User.lastName, User.userId 
                            FROM User JOIN Members ON User.userId = Members.userId
                            WHERE email LIKE '%@mcgill.ca' ");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);


}catch (PDOException $e){
    echo json_encode(["error" => $e->getMessage()]);
}
?>