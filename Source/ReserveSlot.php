<?php
//#Daniel Alzawahra

header("Content-Type: application/json");

$host = "mysql.cs.mcgill.ca";
$user = "users-909468";
$pass = "4rTPckXCr7qL";
$dbname = "test";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

//Parses input JSON, and creates a Booking Reservation.
//If the Booking was awaiting a response, sets that to false
$bookingId = $data['bookingId'] ?? null;
$slotId = $data['slotId'] ?? null;
$userId = $data['userId'] ?? null;

if(!$bookingId || !$slotId || !$userId)
{
    echo json_encode(["success" => false, "message" => "Invalid input data."]);
    exit;
}

$insertQuery = "INSERT INTO Reserve (bookingId, userId, slotId) VALUES (?, ?, ?)";
$Statement = $conn->prepare($insertQuery);
$Statement->bind_param("iii", $bookingId, $userId, $slotId);

if($Statement->execute())
{
    $checkQuery = "SELECT awaiting_response FROM Booking WHERE bookingId = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $bookingId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $booking = $result->fetch_assoc();

    if($booking && $booking['awaiting_response'] == 1)
    {
        $updateQuery = "UPDATE Booking SET awaiting_response = 0 WHERE bookingId = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("i", $bookingId);
        $updateStmt->execute();
    }

    echo json_encode(["success" => true, "message" => "Reservation successful."]);
}
else
{
    echo json_encode(["success" => false, "message" => "Failed to create reservation."]);
}

$conn->close();
?>
