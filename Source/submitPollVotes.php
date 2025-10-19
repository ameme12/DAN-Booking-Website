<?php

ini_set('error_log', 'C:/xampp/logs.log');
//author: Ameline Ramesan 

    $host = "mysql.cs.mcgill.ca";
    $user = "users-909468";
    $pass = "4rTPckXCr7qL";
    $dbname = "2024fall-comp307-909468";

    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    $pollId = $data['pollId'];
    $userId = $data['userId'];
    $day = $data['day'];
    $slot = $data['slot'];

    $conn = new mysqli($host, $user, $pass, $dbname);

    if ($conn->connect_error)
    {
        die("Connection failed: " . $conn->connect_error);
    }
    if (!$conn->query("USE test"))
    {
        die("Failed to switch to database 'test': " . $conn->error);
    }
   

    
    $stmt = $conn->prepare("INSERT INTO Votes (pollId, userId, day, slot_number) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Failed to prepare statement: " . $conn->error);
    }
    
   
       
    $stmt->bind_param("iisi", $pollId, $userId, $day, $slot);
    if ($stmt->execute()) {
       echo "success";
    }else{
        echo "vote submitted";
    }
    

    
    $stmt->close();

    $conn->close();

    echo "Vote submitted successfully!";
    
?>
