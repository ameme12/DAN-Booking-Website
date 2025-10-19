<?php
session_start();

//Author: Neelab Wafasharefe

// Database connection
$host = "mysql.cs.mcgill.ca";
$dbname = "test";
$user = "users-909468";
$pass = "4rTPckXCr7qL";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (!isset($_SESSION['userID'])) {
    header("Location: login2.html");
    exit();
}

$userId = $_SESSION['userID']; // logged-in user's ID

// getting user's info for icon
try {
    $stmt = $pdo->prepare("
        SELECT firstName, lastName 
        FROM User 
        WHERE userId = :userId
    ");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userDetails) {
        die("Error: User not found, UserID: " . $userId);
    }
} catch (PDOException $e) {
    die("Error fetching user details: " . $e->getMessage());
}

// get meeting without title request and put them under hosting meetings
try {
    $stmt = $pdo->prepare("
        SELECT bookingId, location, is_recurring, awaiting_response, title, description
        FROM Booking
        WHERE creator = :userId AND title != 'Request'
    ");
    $stmt->bindParam(':userId', $_SESSION['userID'], PDO::PARAM_INT);
    $stmt->execute();

    $hostingMeetings = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    die("Error fetching meetings: " . $e->getMessage());
}



// get meetings attending
try {
    $stmt = $pdo->prepare("
        SELECT 
            b.bookingId,
            b.location,
            b.is_recurring,
            b.awaiting_response,
            b.title,
            b.description
        FROM 
            Reserve r
        JOIN 
            Booking b ON r.bookingId = b.bookingId
        WHERE 
            r.userId = :userId AND b.title != 'Request' AND b.creator != :userId
    ");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $attendingMeetings = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    die("Error fetching meetings: " . $e->getMessage());
}



try {
    $stmt = $pdo->prepare("
        SELECT bookingId, location, is_recurring, awaiting_response, title, description
        FROM Booking
        WHERE creator = :userId AND title = 'Request'
    ");
    $stmt->bindParam(':userId', $_SESSION['userID'], PDO::PARAM_INT);
    $stmt->execute();

    $requestedMeetings = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    die("Error fetching requested meetings: " . $e->getMessage());
}



// get past meetings
try {
    $stmt = $pdo->prepare("
        SELECT 
            b.bookingId,
            b.location,
            b.is_recurring,
            b.awaiting_response,
            b.title,
            b.description,
            d.start_date,
            d.end_date
        FROM 
            Reserve r
        JOIN 
            Booking b ON r.bookingId = b.bookingId
        JOIN 
            Dates d ON b.bookingId = d.bookingId
        WHERE 
            r.userId = :userId
            AND d.end_date < CURDATE();
    ");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $pastMeetings = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    die("Error fetching past meetings: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styleSidebar.css">
    <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            display: flex;
        }

        /* Page container to adjust for sidebar */
        .content {
            margin-left: 15%;
            padding: 40px;
            flex: 1;
        }

        .content h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        /* Cards Section */
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: calc(33.33% - 20px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            box-sizing: border-box;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .card i {
            font-size: 24px;
            margin-bottom: 10px;
            color: #ee3c30;
        }

        .card h3 {
            font-size: 16px;
            margin: 10px 0;
        }

        .card p {
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }

        .card a {
            font-size: 14px;
            font-weight: bold;
            color: #ee3c30;
            text-decoration: none;
        }


        @media screen and (max-width: 900px) {
            .card {
                width: calc(50% - 20px);
            }

        }

        @media screen and (max-width: 600px) {
            .card {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar close">
    <header>
        <div class="dashboard-box">
                <span class="image">
                    <img src="mcgill-crest.png" alt="mcgill-crest">
                </span>
            <div class="text dashboard-text">
                <span class="name">McGill</span>
                <span class="function">Bookings</span>
            </div>
        </div>
        <i class="fas fa-chevron-left toggle"></i>
    </header>
    <div class="dashboard-divide"></div>
    <div class="menu-bar">
        <div class="menu">
            <li><a href="dashboard.php"><i class="fas fa-calendar-alt icon"></i><span class="text nav-text" style="color: #ee3c30;">Bookings</span></a></li>
            <li><a href="CreateBooking.php"><i class="fas fa-plus-circle icon"></i><span class="text nav-text">Create a booking</span></a></li>
            <li><a href="requestBooking.php"><i class="fas fa-envelope icon"></i><span class="text nav-text">Request a booking</span></a></li>
            <li><a href="availabilityPolls.php"><i class="fas fa-poll icon"></i><span class="text nav-text">Availability polls</span></a></li>
        </div>
    </div>
    <div class="dashboard-divide"></div>
    <div class="sidebar-bottom"></div>
    <button onclick="window.location.href='logout.php'" class="logout-button">
        <i class="fas fa-sign-out-alt icon" ></i><span class="text logout-text">Logout</span>
    </button>

</div>

<!-- Content -->
<div class="content">
    <h1>My Upcoming Meetings</h1>

    <!-- Hosting  -->
    <div class="section-title">Hosting</div>
    <div class="cards">
        <?php if (!$hostingMeetings || count($hostingMeetings) == 0 ): ?>
            <div style="text-align: center; color: #777; font-size: 14px;">
                You are not hosting any meetings.
            </div>
        <?php else: ?>
            <?php foreach ($hostingMeetings as $meeting): ?>
                <div class="card">
                    <i class="fas fa-envelope"></i>
                    <h3><?php echo htmlspecialchars($meeting['title'] ?? 'Untitled Meeting'); ?></h3>
                    <p>
                        Location: <?php echo htmlspecialchars($meeting['location'] ?? 'No location specified'); ?><br>
                        Recurring: <?php echo !empty($meeting['is_recurring']) ? 'Yes' : 'No'; ?><br>
                        Awaiting Response: <?php echo !empty($meeting['awaiting_response']) ? 'Yes' : 'No'; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>


    <!-- Attending -->
    <div class="section-title">Attending</div>
    <div class="cards">
        <?php if (empty($attendingMeetings)): ?>
            <div style="text-align: center; color: #777; font-size: 14px;">
                You are not attending any meetings.
            </div>
        <?php else: ?>
            <?php foreach ($attendingMeetings as $meeting): ?>
                <div class="card">
                    <i class="fas fa-calendar-check"></i>
                    <h3> <?php echo htmlspecialchars($meeting['title']); ?></h3>
                    <p>
                        Location: <?php echo htmlspecialchars($meeting['location'] ?? 'Not specified'); ?><br>
                        Recurring: <?php echo $meeting['is_recurring'] ? 'Yes' : 'No'; ?><br>
                        Awaiting Response: <?php echo $meeting['awaiting_response'] ? 'Yes' : 'No'; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Requested Meetings -->
    <div class="section-title">Requests</div>
    <div class="cards">
        <?php if (empty($requestedMeetings)): ?>
            <div style="text-align: center; color: #777; font-size: 14px;">
                You have no requested meetings.
            </div>
        <?php else: ?>
            <?php foreach ($requestedMeetings as $meeting): ?>
                <div class="card">
                    <i class="fas fa-paper-plane"></i>
                    <h3> <?php echo htmlspecialchars($meeting['title']); ?></h3>
                    <p>
                        Location: <?php echo htmlspecialchars($meeting['location'] ?? 'Not specified'); ?><br>
                        Recurring: <?php echo $meeting['is_recurring'] ? 'Yes' : 'No'; ?><br>
                        Awaiting Response: <?php echo $meeting['awaiting_response'] ? 'Yes' : 'No'; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>


    <!-- Past Meetings -->
    <div class="section-title">Past Meetings</div>
    <div class="cards">
        <?php if (empty($pastMeetings)): ?>
            <div style="text-align: center; color: #777; font-size: 14px;">
                You have no past meetings.
            </div>
        <?php else: ?>
            <?php foreach ($pastMeetings as $meeting): ?>
                <div class="card">
                    <i class="fas fa-calendar-check"></i>
                    <h3> <?php echo htmlspecialchars($meeting['title']); ?></h3>
                    <p>
                        Location: <?php echo htmlspecialchars($meeting['location'] ?? 'Not specified'); ?><br>
                        Recurring: <?php echo $meeting['is_recurring'] ? 'Yes' : 'No'; ?><br>
                        End Date: <?php echo htmlspecialchars($meeting['end_date']); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="user-icon">
        <div class="circle">
            <!-- Display user's initials -->
            <span>
            <?php
            echo strtoupper(substr($userDetails['firstName'], 0, 1)) .
                strtoupper(substr($userDetails['lastName'], 0, 1));
            ?>
        </span>
        </div>
        <!-- Display user's full name -->
        <span class="username">
        <?php echo htmlspecialchars($userDetails['firstName'] . " " . $userDetails['lastName']); ?>
    </span>

    </div>


</div>

<script>
    const body = document.querySelector("body"),
        sidebar = document.querySelector(".sidebar"),
        toggle = document.querySelector(".toggle");


    toggle.addEventListener("click", () => {
        sidebar.classList.toggle("open");
        sidebar.classList.toggle("close");
    });


    window.addEventListener("resize", () => {
        if (window.innerWidth > 900) {
            sidebar.classList.remove("open");
            sidebar.classList.remove("close");
            content.style.marginLeft = "15%";
        } else {
            sidebar.classList.remove("close");
            sidebar.classList.add("open");
            content.style.marginLeft = "0";
        }
    });


</script>




</body>
</html>
