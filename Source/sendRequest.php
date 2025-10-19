<?php
session_start();

// Author: Neelab Wafasharefe

// Ensure the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login2.html");
    exit();
}

// Database connection
$host = "mysql.cs.mcgill.ca";
$dbname = "test";
$username = "users-909468";
$password = "4rTPckXCr7qL";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed.");
}

// Check and collect form inputs
if (!isset($_POST['staff'], $_POST['staffName'], $_POST['date'], $_POST['time'])) {
    die("Error: Missing form inputs.");
}

$staffEmail = $_POST['staff'];
$staffName = $_POST['staffName'];
$bookingDate = $_POST['date'];
$bookingTime = $_POST['time'];
$requesterEmail = $_SESSION['email'] ?? 'unknown@example.com';
$requesterName = $_SESSION['name'] ?? 'Unknown User';
$userId = $_SESSION['userID'];

// Insert into Booking table
$stmt = $conn->prepare("INSERT INTO Booking (is_recurring, location, awaiting_response, creator, title, description) VALUES (?, ?, ?, ?, ?, ?)");
$isRecurring = 0;
$location = "NA";
$awaitingResponse = 1;
$title = "Request";
$description = "Meeting Request";
$stmt->bind_param("isiiss", $isRecurring, $location, $awaitingResponse, $userId, $title, $description);
$stmt->execute();
$bookingId = $stmt->insert_id;

// Calculate end_time as 30 minutes after start_time
$startTime = new DateTime($bookingTime);
$endTime = clone $startTime;
$endTime->add(new DateInterval('PT30M'));
$startTimeFormatted = $startTime->format('H:i:s');
$endTimeFormatted = $endTime->format('H:i:s');

// Insert into AvailableSlots table
$stmt = $conn->prepare("INSERT INTO AvailableSlots (bookingId, week_day, start_time, end_time, max_participants, date, is_full) VALUES (?, ?, ?, ?, ?, ?, ?)");
$weekDay = date('l', strtotime($bookingDate));
$maxParticipants = 1;
$isFull = 0;
$stmt->bind_param("isssssi", $bookingId, $weekDay, $startTimeFormatted, $endTimeFormatted, $maxParticipants, $bookingDate, $isFull);
$stmt->execute();

// Email content
$subject = "New Meeting Request";
$message = "
    <html>
        <head>
            <title>Meeting Request Notification</title>
        </head>
        <body>
            <h2>Meeting Request Notification</h2>
            <p><strong>From:</strong> $requesterName ($requesterEmail)</p>
            <p><strong>To:</strong> $staffName ($staffEmail)</p>
            <p><strong>Date:</strong> $bookingDate</p>
            <p><strong>Time:</strong> {$startTime->format('H:i')} - {$endTime->format('H:i')}</p>
            <p>Please log in to your dashboard to confirm or reject this request.</p>
        </body>
    </html>
";

// Email headers
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: Booking System <no-reply@yourdomain.com>" . "\r\n";
$headers .= "Reply-To: $requesterEmail" . "\r\n";

// Send the email
if (mail($staffEmail, $subject, $message, $headers)) {
    echo "Request successfully sent. The staff member has been notified!";
    echo '<br><br>';
    echo '<a href="dashboard.php" style="display: inline-block; 
        padding: 10px 20px; 
        background-color: #ee3c30; 
        color: #fff; 
        text-decoration: none; 
        border-radius: 5px;
        font-family: Arial, sans-serif;
        font-size: 14px;">
        Return to Home
    </a>';

} else {
    die("Failed to send the email. Please check your server configuration.");
}

// Close the database connection
$conn->close();
?>
