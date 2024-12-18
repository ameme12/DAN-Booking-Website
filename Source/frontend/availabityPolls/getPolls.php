<?php

header('content-type: application/json');

$host = "mysql.cs.mcgill.ca";
$dbname = "test";
$username = "users-909468";
$password = "4rTPckXCr7qL";

try{
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userId = $_GET['userId'];

    $stmt = $db->prepare("SELECT * FROM Poll WHERE creator = ?");
    $stmt->execute([$userId]);
    $polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($polls);

}catch (PDOException $e){
    echo json_encode(["error" => $e->getMessage()]);
}
?>