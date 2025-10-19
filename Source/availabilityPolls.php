<?php
session_start();

// Author: Ameline Ramesan and Neelab Wafasharefe (security)

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    // Redirect to login page
    header("Location: login2.html");
    exit();
}
?>

<html lang="en">

<meta name = "viewport" content="width=device-width, initial-scale=1">

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Young+Serif&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="poll.css">
<link rel="stylesheet" href="styleSidebar.css">

<head>
    <meta charset="UTF-8">
    <title>Polls Page</title>

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
                    <span class="text nav-text">Request a booking</span>

                </a>

            </li>

            <li>

                <a href="availabilityPolls.php">
                    <i class="fas fa-poll icon"></i>
                    <span class="text nav-text" style="color: #ee3c30;">Availability polls</span>

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

<div class = "poll-page">

    <header>
        <div class="my-polls">
            <button class = "button my-polls" style="background-color: #ee3c30;">
                My Polls
            </button>
        </div>

        <div class="new-polls">
            <a href = "newpole.php" style="text-decoration: none;">
                <button class = "button new-polls">
                    <p style="color: #fff;">
                        New Poll
                    </p>

                </button>

            </a>
        </div>
    </header>

    <div class="polls-container" id="polls-container">


    </div>

    <script>

        const pollsContainer = document.querySelector(".polls-container");
        const userId = "<?php echo isset($_SESSION['userID']) ? $_SESSION['userID'] : ''; ?>";
        fetch(`getPolls.php?userId=${userId}`)
            .then(response => response.json())
            .then(data => {
                    pollsContainer.innerHTML = "";

                    if (data.length === 0){
                        pollsContainer.innerHTML = "<p>No polls found for this user.</p>";
                        return;
                    }

                    data.forEach(poll => {

                        const pollCard = document.createElement("div");
                        pollCard.classList.add("poll-card", poll.status ? "active" : "inactive");

                        pollCard.innerHTML = `
                        <a href="TimetablePage.php?pollId=${poll.pollId}&title=${poll.title}&description=${poll.description}" class="poll-link" style="text-decoration:none;">
                        <div class="poll-header">
                         <h3>${poll.title}</h3>
                         <p>Status: ${poll.status ? "Active" : "Inactive"}</p>
                        </div>
                        <div class="poll-body">
                            <p>Description: ${poll.title}</p>
                            <p>Period: Mon-Fri</p>
                            <p>Responses: <span id="responses-${poll.pollId}">Loading...</span></p>
                            <p>Favorite Slot: <span id="favoriteSlot-${poll.pollId}">Loading...</span></p>

                        </div>
                        </a>`
                        ;
                        pollsContainer.appendChild(pollCard);


                        fetch(`responseCount.php?pollId=${poll.pollId}`)
                            .then(response => response.json())
                            .then(responseData => {
                                const responseItem = document.getElementById(`responses-${poll.pollId}`);
                                responseItem.textContent = responseData[0].response_count || "0";
                            })
                            .catch(error =>{
                                console.error("Error when fetching responses:", error);
                                document.getElementById(`responses-${poll.pollId}`).textContent = "Error";

                            });

                        fetch(`getFavoriteSlot.php?pollId=${poll.pollId}`)
                            .then(response => response.json())
                            .then(favoriteData => {
                                const favoriteItem = document.getElementById(`favoriteSlot-${poll.pollId}`);

                                if (favoriteData.length>0){
                                    const day = favoriteData[0].day;
                                    const slotNumber = favoriteData[0].slot_number;

                                    const startHour = 8;
                                    const interval = 30;
                                    let slotHour = 0;



                                    const totalMinutes = startHour * 60 + slotNumber * interval;
                                    const hours = Math.floor(totalMinutes / 60);
                                    const minutes = totalMinutes % 60;


                                    const formattedTime = `${(hours % 12 === 0 ? 12 : hours % 12)}:${minutes.toString().padStart(2, '0')} ${hours >= 12 ? 'PM' : 'AM'}`; // got this on gpt

                                    favoriteItem.textContent = `${day}, ${formattedTime}`;

                                }else{
                                    favoriteItem.textContent = "No favorite Slot";
                                }

                            })
                            .catch(error =>{
                                console.error("Error when fetchinf favorite slot:", error);
                                document.getElementById(`favoriteSlot-${poll.pollId}`).textContent = "Error";
                            })

                    });
                }
            )
            .catch(error => {
                console.error("Error fetching polls:", error);
                pollsContainer.innerHTML = "<p> Error loading polls</p>"
            });


    </script>



</div>



</body>
</html>

