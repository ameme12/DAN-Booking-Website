<?php
//Daniel Alzawahra
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

//Grabs all the relevant data for a given meeting

$bookingId = $_GET['bookingId'] ?? null;

if (!$bookingId)
{
    die(json_encode(["success" => false, "message" => "Invalid booking ID."]));
}

$query = "SELECT slotId, bookingId, week_day, start_time, end_time, DATE_FORMAT(date, '%Y-%c-%e') AS js_date FROM AvailableSlots WHERE bookingId = ?";
$statement = $conn->prepare($query);
$statement->bind_param("i", $bookingId);
$statement->execute();
$result = $statement->get_result();

$slots = [];
$dates = [];
while ($row = $result->fetch_assoc())
{
    $slots[] = $row;
    $dates[] = $row['js_date'];

}
error_log(json_encode(["success" => true, "slots" => $slots, "dates" => array_unique($dates)]));
echo json_encode(["success" => true, "slots" => $slots, "dates" => array_unique($dates)]);
$conn->close();

