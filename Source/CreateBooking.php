<?php
//#Daniel Alzawahra and Neelab Wafasharefe (security)

session_start();

if (!isset($_SESSION['userID']))
{
    header("Location: login2.html");
    exit();
}
?>

<!DOCTYPE html>
<html>

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
    <title>Create Booking</title>
</head>

<body>


<div class = "sidebar">

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
                    <span class="text nav-text" style="color: #ee3c30;">Create a booking</span>

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
        <a href="logout.php">
        <button href="logout.php" class="button logout-button">
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

<div class="calendar-elements">
    <div class="poll-container">
        <h2 class="poll-title">Meeting Details</h2>
        <div class="form-group">
            <label for="poll-title">Title*</label>
            <input type="text" id="poll-title" placeholder="Coziest room on campus" />
        </div>

        <div class="form-group">
            <label for="poll-type">Type*</label>
            <select id="poll-type">
                <option>One-Off</option>
                <option>Recurring</option>
            </select>
        </div>




        <div class="form-group">
            <label for="poll-capacity">Capacity Per Slot*</label>
            <input type="number" id="poll-capacity" class="poll-capacity" placeholder="15" min="0" max="100" required />
        </div>

        <div class="form-group">
            <label for="poll-location">Location*</label>
            <input type="text" id="poll-location" placeholder="Lorem Ipsum" />
        </div>

        <div class="form-group">
            <label for="poll-description">Description</label>
            <textarea id="poll-description" rows="4" placeholder="Help others understand your goals"></textarea>
        </div>

        <div class="form-buttons">
            <a href="dashboard.php">
            <button class="form-button" id="poll-cancel">Cancel</button>
            </a>
            <button class="form-button" id="poll-select-hours">Next</button>

        </div>
    </div>

    <div id="calendar-wrapper" class ="calendar-wrapper">
        <div id = "calendar-info" class = "calendar-info">
            Select Days
        </div>
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


<script src = "BookingCalendar.js"></script>
<script>
    let calendar;
    let globalSelections = new Set();

    //Reloads the calendar when the user changes their booking mode
    function ResetCalendar(multiselect, startDate = null, isRecurring = false)
    {
        document.getElementById("calendar-container").innerHTML = "";
        document.getElementById("calendar-sidebar").innerHTML = "";

        calendar = new BookingCalendar('calendar-container',
            {
                isRecurring: isRecurring,
                startDate: startDate || new Date(),
                mode: 'selection',
                globalDates: globalSelections,
                availableDates: [3, 5, 7, 10, 12],
                multiselect: multiselect,
                onDaySelect: (selectedDays) =>
                {
                }
            });

        if(multiselect)
            RestoreRecurringDays();
        function RestoreRecurringDays()
        {
            let daysContainer = document.querySelector(".calendar-days");
            daysContainer.querySelectorAll(".calendar-day").forEach((option) =>
            {
                let day = parseInt(option.innerText);
                let month = calendar.currentDate.getMonth();
                let year = calendar.currentDate.getFullYear();

                if (globalSelections.has(`${year}-${month}-${day}`))
                {
                    option.classList.add("day-selected");
                }
            });
        }


    }


    //Handles Recurring Meeting initialization
    function ManageRecurring()
    {
        globalSelections = new Set();

        if (calendar.selectedDays.size > 0)
        {
            let selectedDay = Math.min(...Array.from(calendar.selectedDays));
            let nextDay = new Date(calendar.currentDate);
            nextDay.setDate(selectedDay + 1);

            ResetCalendar(false, nextDay, true);
        }
        else
        {
            alert("Please select at least one day!");
        }
    }

    //Handles One-off Meeting initialization
    function ManageOneoffs()
    {
        globalSelections = new Set();
        const daysContainer = document.querySelector(".calendar-days");

        daysContainer.querySelectorAll(".calendar-day.day-selected").forEach((option) =>
        {
            let day = parseInt(option.innerText);
            let month = calendar.currentDate.getMonth();
            let year = calendar.currentDate.getFullYear();

            globalSelections.add(`${year}-${month}-${day}`);
        });

    }

    ResetCalendar(true);

</script>
<script>
    document.addEventListener("DOMContentLoaded", () =>
    {
        let pollType = document.getElementById("poll-type");
        pollType.addEventListener("change", () =>
        {
            let oldButton = document.getElementById("poll-select-hours");

            let newButton = oldButton.cloneNode(true);
            oldButton.parentNode.replaceChild(newButton, oldButton);
            oldButton = newButton;
            ResetBehavior();

            let selectedValue = pollType.value;
            globalSelections = new Set();


            if (selectedValue === "One-Off")
            {
                document.getElementById("calendar-info").innerText = "Select days";

                ResetCalendar(true);

                document.getElementById("calendar-wrapper").classList.remove("schedule-hidden");
                document.getElementById("schedule-container").classList.add("schedule-hidden");

            }
            else if (selectedValue === "Recurring")
            {
                ResetCalendar(true,null,true);

                document.getElementById("calendar-info").innerText = "Select start and end Days";
                document.getElementById("calendar-wrapper").classList.remove("schedule-hidden");
                document.getElementById("schedule-container").classList.add("schedule-hidden");

            }
        });

        //Resets form button behavior
        function ResetBehavior()
        {
            document.getElementById('poll-select-hours').innerText = "Next";
            document.getElementById('poll-select-hours').addEventListener('click', () =>
            {
                const pollType = document.getElementById('poll-type').value;
                if (pollType === "One-Off")
                {
                    if (globalSelections.size === 0)
                    {
                        alert("Please select at least one day for a One-Off booking.");
                        return;
                    }
                }
                else if (pollType === "Recurring")
                {
                    if (globalSelections.size < 2)
                    {
                        alert("Please select a start date and an end date for a Recurring booking.");
                        return;
                    }
                }

                document.getElementById("calendar-wrapper").classList.add("schedule-hidden");
                document.getElementById("schedule-container").classList.remove("schedule-hidden");
                document.getElementById('poll-select-hours').innerText = "Create";
                document.getElementById('poll-select-hours').onclick = () =>
                {
                    if(CheckDaySelections())
                    {
                        SubmitBooking();
                    }
                    else
                    {
                        alert("Please select at least one day!");
                    }
                }
            });
        }
        ResetBehavior();



    });


    //Submits the request to Create a Booking with the provided information.
    async function SubmitBooking()
    {
        let title = document.querySelector("#poll-title").value;
        let type = document.querySelector("#poll-type").value;
        let capacity = parseInt(document.querySelector("#poll-capacity").value);
        let location = document.querySelector("#poll-location").value;
        let description = document.querySelector("#poll-description").value;

        if (!title)
        {
            alert("Please provide a title for the booking.");
            return;
        }

        if (!capacity || isNaN(capacity) || capacity <= 0)
        {
            alert("Please enter a valid capacity (greater than 0).");
            return;
        }
        if (!location)
        {
            alert("Please specify the location for the booking.");
            return;
        }

        let days = globalSelections;

        let sortedSelections = Array.from(globalSelections);
        sortedSelections.sort((a, b) => new Date(a) - new Date(b));

        sortedSelections = sortedSelections.map(date =>
        {
            let [year, month, day] = date.split("-").map(Number);
            let correctedMonth = month + 1;
            return `${year}-${String(correctedMonth).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
        });

        let times = GetSelections();

        let isRecurring = type === "Recurring" ? 1 : 0;

        const data =
            {
                title: title,
                description: description,
                is_recurring: isRecurring,
                location: location,
                creator: <?php echo $_SESSION['userID']; ?>,
                capacity: capacity,
                start_date: sortedSelections[0],
                end_date: sortedSelections[sortedSelections.length - 1],
                weekdays: times,
                dates: sortedSelections,
            };

        let response = await fetch("NewBooking.php",
            {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data),
            });


        if (!response.ok)
        {
            let result = await response.text();
            alert("Error creating booking: " + result);
            return;
        }

        let result = await response.text();
        let bookingId = parseInt(result);


        let shareableLink = `localhost/TeamDAN/Source/BookMeeting.php?BookingId=${bookingId}`;

        navigator.clipboard.writeText(shareableLink)
            .then(() =>
            {
                alert("Booking created! A shareable link has been copied to your clipboard.");
                window.location.href = "dashboard.php";
            })
            .catch(err =>
            {
                alert("Failed to generate shareable link! Redirecting...");
                window.location.href = "dashboard.php";
            });

    }





</script>
</body>
</html>
