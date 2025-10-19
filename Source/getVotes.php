<?php
#author: Ameline Ramesan

header('content-type: application/json');

$host = "mysql.cs.mcgill.ca";
$dbname = "test";
$username = "users-909468";
$password = "4rTPckXCr7qL";

try{
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pollId = $_GET['pollId'];

    $stmt = $db->prepare("SELECT day, slot_number, COUNT(*) AS vote_count 
                            FROM Votes 
                            WHERE pollId = ?
                            GROUP BY day, slot_number");
    $stmt->execute([$pollId]);
    $votes= $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($votes);

}catch (PDOException $e){
    echo json_encode(["error" => $e->getMessage()]);
}
?>