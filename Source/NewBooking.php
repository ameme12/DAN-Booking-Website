<?php
//#Daniel Alzawahra

$host = "mysql.cs.mcgill.ca";
$user = "users-909468";
$pass = "4rTPckXCr7qL";
$dbname = "test";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}


//Generates slots in 30-minute increments. 1 -> 2 becomes 1 -> 1:30 +  1:30 -> 2
function GenerateTimeSlots($startTime, $endTime): array
{
    $slots = [];
    $start = strtotime($startTime);
    $end = strtotime($endTime);

    while ($start < $end)
    {
        $slotStart = date("H:i:s", $start);
        $slotEnd = date("H:i:s", strtotime("+30 minutes", $start));
        $slots[] = ["start_time" => $slotStart, "end_time" => $slotEnd];
        $start = strtotime("+30 minutes", $start);
    }
    return $slots;
}

//Parses the JSON. Creates a Booking, a Date entry, and a bunch of slots
$data = json_decode(file_get_contents('php://input'), true);

$title= $data["title"];
$description= $data["description"];
$isRecurring = $data['is_recurring'];
$location = $data['location'];
$creator = $data['creator'];
$capacity = $data['capacity'];
$startDate = $data['start_date'];
$endDate = $data['end_date'];
$weekdays = $data['weekdays'];
$dates = $data['dates'];

$statement = $conn->prepare("INSERT INTO Booking (is_recurring, location, awaiting_response, creator, title, description) VALUES (?, ?, 0, ?, ?, ?)");
$statement->bind_param("isiss", $isRecurring, $location, $creator, $title, $description);
$statement->execute();
$bookingId = $conn->insert_id;

$statement = $conn->prepare("INSERT INTO Dates (bookingId, start_date, end_date) VALUES (?, ?, ?)");
$statement->bind_param("iss", $bookingId, $startDate, $endDate);
$statement->execute();

$start = new DateTime($startDate);
$end = new DateTime($endDate);
$interval = new DateInterval('P1D');
$period = new DatePeriod($start, $interval, $end->modify('+1 day'));

$statement = $conn->prepare("INSERT INTO AvailableSlots (bookingId, week_day, start_time, end_time, max_participants, date, is_full) 
                        VALUES (?, ?, ?, ?, ?, ?, 0)");

foreach ($period as $date)
{
    $dayOfWeek = $date->format('l');

    $currentDate = $date->format('Y-m-d');

    foreach ($weekdays as $weekday)
    {


        if ($weekday['day'] === $dayOfWeek)
        {

            $slots = GenerateTimeSlots($weekday['start'], $weekday['end']);


            foreach ($slots as $slot)
            {
                $statement->bind_param("isssis", $bookingId, $dayOfWeek, $slot['start_time'], $slot['end_time'], $capacity, $currentDate);
                $statement->execute();
            }
        }
    }
}

//echo "Booking and slots successfully created!";
echo $bookingId;
$conn->close();

