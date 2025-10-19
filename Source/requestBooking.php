<?php
session_start();

// Author: Neelab Wafasharefe

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    // Redirect to login page
    header("Location: login2.html");
    exit();
}
?>

<html>

<!--transform: translate(x,y);
-->
<meta name = "viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Young+Serif&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="RequestStyles.css">
<link rel="stylesheet" href="CalendarStyles.css">
<link rel="stylesheet" href="styleSidebar1.css">
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
/>

<head>

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
                    <span class="text nav-text">Create a booking</span>

                </a>
            </li>

            <li>

                <a href="requestBooking.php">
                    <i class="fas fa-envelope icon"></i>
                    <span class="text nav-text" style="color: #ee3c30;">Request a booking</span>

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

        <button onclick="window.location.href='logout.php'" class="logout-button">
            <i class="fas fa-sign-out-alt icon" ></i><span class="text logout-text">Logout</span>
        </button>
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

<div class = "page-content">

    <form action="sendRequest.php" method="post">
        <div class="request-box">
            <h2>Request a booking</h2>

            <h4>Select a staff member:</h4>
            <select name="staff" id="staff-selector" class="staff-selector">
                <option value="">--Please choose an option--</option>
            </select>
            <input type="hidden" name="staffName" id="staff-name">

            <h4>Select a date: </h4>
            <div id="calendar-wrapper" class="calendar-wrapper">
                <div id="calendar-info" class="calendar-info" style="float:left;"></div>
                <div id="calendar-container" class="calendar-container"></div>
                <div id="calendar-sidebar" class="calendar-sidebar"></div>
            </div>

            <!-- Hidden inputs for date and time -->
            <input type="hidden" name="date" id="selected-date">
            <input type="hidden" name="time" id="selected-time">

            <div class="request-box-bottom">
                <input type="submit" value="Submit" class="submit-button">
            </div>
        </div>
    </form>





    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const staffSelector = document.getElementById("staff-selector");

            fetch("getStaff.php")
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error("Error fetching staff:", data.error);
                        staffSelector.innerHTML = "<option value=''>Error loading staff options</option>";
                        return;
                    }

                    // Populate dropdown
                    staffSelector.innerHTML = "<option value=''>--Please choose an option--</option>";
                    data.forEach(staff => {
                        const option = document.createElement("option");
                        option.value = staff.email;
                        option.textContent = `${staff.firstName} ${staff.lastName}`;
                        staffSelector.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    staffSelector.innerHTML = "<option value=''>Error loading staff options</option>";
                });
        });
    </script>


    <script src = "Calendar.js"></script>

    <script>


        class Calendar
        {
            constructor(containerID, options ={})
            {
                this.container = document.getElementById(containerID);
                this.options = options;
                this.currentDate = options.startDate;
                this.multiselect = options.multiselect;
                this.globalDates = options.globalDates;

                this.baseDate = new Date();
                this.selectedDays = new Set();

                this.isSelecting   = false;
                this.isDeselecting = false;

                this.recurringStart = null;
                this.recurringEnd = null;
                this.isRecurring = options.isRecurring;


                this.advancesAllowed = 100;
                if(this.isRecurring)
                {
                    this.advancesAllowed = 4;
                }


                this.initialize();


            }
            /*
            If recurring, I want to link each Day of the
             */
            /*
            If you have recurring, you select a start and end date, then you get a list of all the days, then you choose which days
            you want, and start and end times for each day

            Mon 13 -> 14 two 30-min slots, or one hour
            Tue 16 -> 19 3 hours, or 6 30-mins?

             */
            initialize()
            {
                document.addEventListener('mouseup', () =>
                {
                    this.isSelecting = false;
                    this.isDeselecting = false;
                });
                this.showHeader();
                this.showWeekdays();
                this.showDays();
                this.eventListeners();
            }

            showHeader()
            {


                let header = document.createElement('div');
                let monthYear = this.currentDate.toLocaleDateString('en-us', {month: 'long', year:'numeric'});
                header.className = 'calendar-header';
                header.innerHTML =
                    header.innerHTML =
                        `<button type="button" id="prevMonth">&lt;</button>
                        <span id="monthYear">${monthYear}</span>
                        <button type="button" id="nextMonth">&gt;</button>
                        `

                this.container.appendChild(header);
            }

            showWeekdays()
            {
                let weekdaysContainer = document.createElement('div');
                weekdaysContainer.className = 'calendar-weekdays';
                const days = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];

                days.forEach(day =>
                {
                    let dayElement = document.createElement('div');
                    dayElement.innerText = day;
                    weekdaysContainer.appendChild(dayElement);
                });

                this.container.appendChild(weekdaysContainer);
            }

            showDays()
            {

                let daysContainer = document.createElement('div');
                daysContainer.className = 'calendar-days';
                daysContainer.id = 'calendar-days';

                let year = this.currentDate.getFullYear();
                let month = this.currentDate.getMonth();
                let daysThisMonth = new Date(year, month + 1, 0).getDate();
                let firstDay = new Date(year, month, 1).getDay();
                let pad = (firstDay + 6) % 7;
                let today = new Date();
                let isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;

                this.container.querySelectorAll('.calendar-days').forEach(element => element.remove());

                for (let day = 1; day <= daysThisMonth + pad; day++)
                {
                    let dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';

                    if (day > pad)
                    {
                        const date = day - pad;
                        dayElement.innerText = date;
                        let key = `${year}-${month}-${date}`;


                        if (isCurrentMonth && date < today.getDate())
                        {
                            dayElement.classList.add('day-inactive');
                            dayElement.style.pointerEvents = 'none';
                        }
                        else
                        {
                            if (this.options.globalDates?.has(key))
                            {
                                if(this.isRecurring)
                                {
                                    if(key === this.startDate || key === this.endDate)
                                        dayElement.classList.add('day-selected');

                                    else
                                        dayElement.classList.add('day-range');
                                }
                                else
                                {
                                    dayElement.classList.add('day-selected');
                                }

                            }
                            if (this.options.mode === 'availability')
                            {
                                this.manageMode(dayElement, date);
                            }
                            else
                            {
                                this.dayListeners(dayElement, date);
                            }
                        }
                    }
                    else
                        dayElement.classList.remove('calendar-day');

                    daysContainer.appendChild(dayElement);
                }

                this.container.appendChild(daysContainer);
            }
            /*
            manageMode(day, date)
            {
                let isAvailable = this.options.availableDates?.includes(date);

                if (isAvailable)
                    day.classList.add('day-selected');

                else
                {
                    day.classList.add('day-inactive');
                    day.style.pointerEvents = 'none';
                }
            }

             */

            dayListeners(dayElement, date)
            {
                let year = this.currentDate.getFullYear();
                let month = this.currentDate.getMonth();
                let key = `${year}-${month}-${date}`;
                dayElement.addEventListener('mousedown', () =>
                {

                    if(this.isRecurring)
                    {
                        this.ManageDualSelection(dayElement, key, date);
                        return;
                    }
                    if (!this.multiselect)
                    {
                        this.container.querySelectorAll('.day-selected').forEach(selectedDay =>
                        {
                            selectedDay.classList.remove('day-selected');
                        });
                        this.selectedDays.clear();
                    }

                    if (dayElement.classList.contains('day-selected'))
                    {
                        this.isDeselecting = true;
                        dayElement.classList.remove('day-selected');
                        this.selectedDays.delete(date);
                        this.options.globalDates?.delete(key);

                    }
                    else
                    {
                        this.isSelecting = true;
                        dayElement.classList.add('day-selected');
                        this.selectedDays.add(date);
                        this.options.globalDates?.add(key);

                    }

                    if (this.options.onDaySelect)
                        this.options.onDaySelect(Array.from(this.selectedDays));
                });

                dayElement.addEventListener('mousemove', () =>
                {

                    if(!this.multiselect)
                    {
                        return;
                    }
                    if (this.isSelecting && !dayElement.classList.contains('day-selected'))
                    {
                        dayElement.classList.add('day-selected');
                        this.selectedDays.add(date);
                        this.options.globalDates?.add(key);

                    }
                    else if (this.isDeselecting && dayElement.classList.contains('day-selected'))
                    {
                        dayElement.classList.remove('day-selected');
                        this.selectedDays.delete(date);
                        this.options.globalDates?.delete(key);

                    }
                });
            }

            ManageDualSelection(dayElement, key, date)
            {
                if (!this.startDate)
                {
                    this.ClearAll();
                    this.startDate = key;
                    dayElement.classList.add('day-selected', 'day-start');

                }
                else if (!this.endDate)
                {
                    if (this.IsValidEndDate(this.startDate, key))
                    {
                        this.endDate = key;
                        this.HighlightRange();
                    }
                    else
                    {
                        alert("End date must be after the start date.");
                    }
                }
                else
                {
                    this.ClearAll();
                    this.startDate = key;
                    this.endDate = null;
                    dayElement.classList.add('day-selected', 'day-start');
                }
            }
            IsValidEndDate(startKey, endKey)
            {
                const [yearStart, monthStart, dayStart] = startKey.split('-').map(Number);
                const [yearEnd, monthEnd, dayEnd] = endKey.split('-').map(Number);

                const start = new Date(yearStart, monthStart, dayStart);
                const end = new Date(yearEnd, monthEnd, dayEnd);

                return end >= start;
            }

            ClearAll()
            {
                this.container.querySelectorAll('.calendar-day').forEach(day =>
                {
                    day.classList.remove('day-selected', 'day-start', 'day-end', 'day-range');
                });

                this.startDate = null;
                this.endDate = null;
                this.selectedDays.clear();
                this.options.globalDates?.clear();
            }

            HighlightRange()
            {

                this.globalDates.clear();
                const [yearStart, monthStart, dayStart] = this.startDate.split('-').map(Number);
                const [yearEnd, monthEnd, dayEnd] = this.endDate.split('-').map(Number);

                const start = new Date(yearStart, monthStart, dayStart);
                const end = new Date(yearEnd, monthEnd, dayEnd);

                this.container.querySelectorAll('.calendar-day').forEach(dayElement =>
                {


                    let start = new Date(yearStart, monthStart, dayStart);
                    let end = new Date(yearEnd, monthEnd, dayEnd);

                    while (start <= end)
                    {
                        let key = `${start.getFullYear()}-${start.getMonth()}-${start.getDate()}`;
                        this.globalDates.add(key);

                        start.setDate(start.getDate() + 1);
                    }
                    this.UpdateCSSSelections();
                });
            }
            UpdateCSSSelections()
            {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();

                this.container.querySelectorAll('.calendar-day').forEach(dayElement =>
                {
                    const date = parseInt(dayElement.innerText);
                    const key = `${year}-${month}-${date}`;

                    if (this.globalDates.has(key))
                    {
                        if(key === this.startDate || key === this.endDate)
                            dayElement.classList.add('day-selected');

                        else
                            dayElement.classList.add('day-range');
                    }
                    else
                    {
                        dayElement.classList.remove('day-selected');
                    }
                });
            }



            eventListeners()
            {
                let prev = this.container.querySelector('#prevMonth');
                let next = this.container.querySelector('#nextMonth');



                prev.addEventListener('click', () => {
                    let newDate = new Date(this.currentDate);
                    newDate.setMonth(newDate.getMonth() - 1);

                    if ((newDate >= this.baseDate)) {
                        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                        this.updateCalendar();
                        this.advancesAllowed += 1;
                    }
                });

                next.addEventListener('click', () => {
                    if (this.advancesAllowed > 0) {
                        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                        this.updateCalendar();
                        this.advancesAllowed -= 1;
                    }
                });

            }

            updateCalendar() {
                this.container.querySelector('#monthYear').innerText =
                    this.currentDate.toLocaleDateString('en-us', { month: 'long', year: 'numeric' });
                this.showDays();
            }





            generateTimeSlots(start = "13:00", end = "23:00")
            {
                let times = [];
                let [startHours, startMinutes] = start.split(':').map(Number);
                let [endHours, endMinutes] = end.split(':').map(Number);

                while (startHours < endHours || (startHours === endHours && startMinutes < endMinutes))
                {
                    times.push(`${String(startHours).padStart(2, '0')}:${String(startMinutes).padStart(2, '0')}`);
                    startMinutes += 30;
                    if (startMinutes >= 60)
                    {
                        startMinutes = 0;
                        startHours += 1;
                    }
                }

                return times;
            }


            manageMode(dayElement, date)
            {
                let isAvailable = this.options.availableDates?.includes(date);

                if (isAvailable)
                {
                    dayElement.classList.add('day-selected');

                    dayElement.addEventListener('click', () =>
                    {
                        this.showSidebar(dayElement, `Day ${date}`);
                    });
                }
                else
                {
                    dayElement.classList.add('day-inactive');
                    dayElement.style.pointerEvents = 'none';
                }
            }

            showSidebar() {
                document.getElementById('calendar-container').classList.add('calendar-trim');
                let sidebar = document.getElementById('calendar-sidebar');
                sidebar.innerHTML = `
        <h3>Available Times</h3>
        <ul id="calendar-times">
            ${this.generateTimeSlots().map(time => `<li>${time}</li>`).join('')}
        </ul>
    `;
                sidebar.style.display = 'block';

                let slots = sidebar.querySelectorAll('#calendar-times li');

                slots.forEach((slot) => {
                    slot.addEventListener('click', () => {
                        // Deselect previously selected slots
                        slots.forEach(otherSlot => otherSlot.classList.remove('calendar-time-selected'));

                        // Highlight the selected slot
                        slot.classList.add('calendar-time-selected');

                        // Set the selected time and date in hidden inputs
                        document.getElementById("selected-time").value = slot.textContent;
                        document.getElementById("selected-date").value = this.currentDate.toISOString().split('T')[0];
                    });
                });
            }


        }
    </script>

    <script>

        let daysArray = Array.from({ length: 31 }, (_, index) => index + 1);

        let calendar = new Calendar('calendar-container',
            {
                isRecurring: false,
                startDate: new Date(),
                mode: 'availability',
                globalDates: null,
                availableDates: daysArray,
                multiselect: false,
                onDaySelect: (selectedDays) =>
                {

                }
            });

    </script>





</div>
</body>
</html>