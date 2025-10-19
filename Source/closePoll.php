<?php
#author: Ameline Ramesan
ini_set('error_log', 'C:/xampp/logs.log');

header('content-type: application/json');

$host = "mysql.cs.mcgill.ca";
$dbname = "test";
$username = "users-909468";
$password = "4rTPckXCr7qL";

try{
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    $pollId = $data['pollId'];

    error_log(print_r($pollId, true));

    if (!$pollId) {
        echo json_encode(["success" => false, "message" => "Poll ID is missing."]);
        exit;
    }

    $stmt = $db->prepare("UPDATE Poll SET status = 0 WHERE pollId = ?");

    $stmt->execute([$pollId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Poll closed successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Poll not found or already closed."]);
    }
   

}catch (PDOException $e){
    echo json_encode(["error" => $e->getMessage()]);
}
?>