<!--//#Daniel Alzawahra-->

<!DOCTYPE html>
<html>
<?php
session_start();
$id = 1;
if (isset($_SESSION['userID']))
{
    $id=$_SESSION['userID'];

}
?>
<!--transform: translate(x,y);
-->
<meta name = "viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="BookingCalendarStyles.css">
<link rel="stylesheet" href="styleSidebar.css">
<link rel="stylesheet" href="MeetingPollStyles.css">
<link rel="stylesheet" href="WeekStyles.css">

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Young+Serif&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<head>
    <title>Book Meeting</title>
</head>

<body>


<div class = "sidebar" id="site-sidebar">

    <header>

        <div class ="dashboard-box">

                        <span class="image">

                        <img src="mcgill_crest.png" alt="mcgill-crest">

                        </span>


            <div class="text dashboard-text">

                <span class="name" >Mcgill</span>
                <span class="function"  >Bookings</span>

            </div>

        </div>

        <i class="fas fa-chevron-left toggle"></i>

    </header>




    <div class="dashboard-divide">

    </div>

    <div class="menu-bar">

        <div class="menu">

            <li>
                <a href="dashboard.php">
                    <i class="fas fa-calendar-alt icon"></i>
                    <span class="text nav-text">Bookings</span>

                </a>

            </li>

            <li>

                <a href="CreateBooking.php">
                    <i class="fas fa-plus-circle icon"></i>
                    <span class="text nav-text">Create a booking</span>

                </a>
            </li>

            <li>

                <a href="requestBooking.php">
                    <i class="fas fa-envelope icon"></i>
                    <span class="text nav-text">Request a booking</span>

                </a>

            </li>

            <li>

                <a href="availabilityPolls.php">
                    <i class="fas fa-poll icon"></i>
                    <span class="text nav-text">Availability polls</span>

                </a>

            </li>


        </div>


    </div>

    <br><br>
    <div class="dashboard-divide"></div>

    <div class= "sidebar-bottom">
        <a href="logout.php"
        <button class="button logout-button">
            <i class="fas fa-sign-out-alt icon" ></i><span class="text logout-text">Logout</span>
        </button>
        </a>
    </div>


</div>
<script>

    const body = document.querySelector("body"),
        sidebar = body.querySelector(".sidebar"),
        toggle = body.querySelector(".toggle");


    toggle.addEventListener("click", () => {
        sidebar.classList.toggle("close");
    });
</script>

<div class="calendar-elements transformer">
    <div class="poll-container">
        <h2 class="poll-title" id="poll-title">Non-Member</h2>
        <div class="form-group">
            <label for="user-name">Name*</label>
            <input type="text" id="user-name" placeholder="Joseph Vybihal" />
        </div>

        <div class="form-group">
            <label for="user-email">Email*</label>
            <input type="email" id="user-email" placeholder="email@example.com" />
        </div>




        <div class="form-buttons">
            <a href="dashboard.php"><button class="form-button" id="poll-cancel">Cancel</button></a>
            <button class="form-button" id="poll-submit">Submit</button>

        </div>
    </div>


    <div id="calendar-wrapper" class ="calendar-wrapper sideways">

        <div id="calendar-container" class="calendar-container">
        </div>
        <div id="calendar-sidebar" class="calendar-sidebar">

        </div>
    </div>
    <div id="schedule-container" class="schedule-hidden">
        <div class="schedule">
            <h3>Select Days & Hours</h3>
        </div>
    </div>
</div>


<script src="DaySelector.js"></script>


<script src="BookingCalendar.js"></script>

<!-- Initializes a Calendar in availability mode, no multiselect.  -->
<script>

    let calendar;
    async function InitializeCalendar()
    {
        let bookingId = new URLSearchParams(window.location.search).get("BookingId");

        let data = await FetchBooking(bookingId);

        calendar = new BookingCalendar('calendar-container',
            {
                isRecurring: false,
                bookingId: bookingId,
                userId: <?php echo $id ?>,
                data: data,
                buttonId: 'poll-submit',
                startDate: new Date(),
                mode: 'availability',
                globalDates: null,
                availableDates: [3, 5, 7, 31, 12],
                multiselect: false,
                onDaySelect: (selectedDays) =>
                {
                }
            });

    }

    document.addEventListener("DOMContentLoaded", InitializeCalendar);

    async function FetchBooking(bookingId)
    {
        if (!bookingId)
        {
            throw new Error("Invalid booking ID.");
        }

        let response = await fetch(`GetMeeting.php?bookingId=${bookingId}`);
        return await response.json();

    }



</script>




</body>
</html>
