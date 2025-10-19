<?php
#author: Ameline Ramesan + Daniel Alzawahra(for the timeTable Java Script )


header('Content-Type: text/html; charset=UTF-8');

$pollId = isset($_GET['pollId']) ? urldecode($_GET['pollId']) : "Poll Not Found";
$pollTitle = isset($_GET['title']) ? urldecode($_GET['title']) : "Poll Not Found";
$pollDescription = isset($_GET['description']) ? urldecode($_GET['description']) : "No description available.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Young+Serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="TimetableStyles.css">
    <link rel="stylesheet" href="styleSidebar.css">
    

    <title>Timetable</title>

</head>
<body>

<div class = "close-button">
    <a href="availabilityPolls.php">
            <button class="form-button" id="poll-cancel">Cancel</button>
            <p></p>
            <button class="form-button" id="poll-inactive" onclick = "closePoll()">Close this poll</button>

    </a>
</div>

<div style ="display:block">

    
    
    <h1 class ="timetable-title"><?php echo htmlspecialchars($pollTitle); ?></h1>
    <center><p class="timetable-description"><?php echo htmlspecialchars($pollDescription); ?></p></center>
    

    <div id="timetable" class="timetable-container"></div>

    <script >

        class Timetable
        {
            constructor(containerID, options = {})
            {
                this.container = document.getElementById(containerID);
                this.startHour =  8;
                this.endHour   = 20;
                this.interval  = 30;
                this.days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                this.selectedSlots = new Set();


                this.isSelecting   = false;
                this.isDeselecting = false;
                this.initialize();
            }

            initialize()
            {

                this.showHeader();
                this.showTimetable();
                this.eventListeners();
            }

            showHeader()
            {
                const headerCorner = document.createElement('div');
                headerCorner.classList.add('timetable-header');
                this.container.appendChild(headerCorner);

                this.days.forEach(day =>
                {
                    const dayCell = document.createElement('div');
                    dayCell.classList.add('timetable-header');
                    dayCell.innerText = day;
                    this.container.appendChild(dayCell);
                });
            }

            showTimetable()
            {
                for (let hour = this.startHour; hour < this.endHour; hour++)
                {
                    for (let half = 0; half < 60; half += this.interval)
                    {
                        let time = `${String(hour).padStart(2, '0')}:${String(half).padStart(2, '0')}`;

                        let timeRow = document.createElement('div');
                        timeRow.classList.add('timetable-time');

                        if (half === 0)
                        {
                            timeRow.innerText = `${hour}:00`;
                        }
                        else
                            timeRow.innerText = ' ';

                        this.container.appendChild(timeRow);

                        for (let day = 0; day < this.days.length; day++)
                        {
                            let slot = document.createElement('div');
                            slot.classList.add('timetable-slot');
                            slot.dataset.time = time;
                            slot.dataset.day = this.days[day];
                            this.container.appendChild(slot);
                        }
                    }
                }
            }

            
            //TODO::  Maybe use this?

            
            eventListeners()
            {
            
            }
        }
    </script>
    <script>


        let timetable = new Timetable('timetable',
            {
                startHour: 8,
                endHour: 20,
                interval: 30,
            });
    </script>

    
</div>

    <center><p class="timetable-description">Here is the link to vote: </p></center>
    <center><a href="VoteForPoll.html?pollId=<?php echo htmlspecialchars($pollId); ?>&title=<?php echo htmlspecialchars($pollTitle); ?>&description=<?php echo htmlspecialchars($pollDescription); ?>">
        VoteForPoll.html?pollId=<?php echo htmlspecialchars($pollId); ?>&title=<?php echo htmlspecialchars($pollTitle); ?>&description=<?php echo htmlspecialchars($pollDescription); ?>
    </a></center>
    <br>


<script>

        const pollId = <?php echo json_encode($pollId); ?>;
        const timetableContainer = document.getElementById('timetable');
        fetch(`getVotes.php?pollId=${pollId}`)
            .then(response => response.json())
            .then(data => {
                

                if (data.length === 0){
                    return;
                }
                const maxVotes = Math.max(...data.map(vote => vote.vote_count));
                const week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                const timeTableSlots = document.querySelectorAll('.timetable-slot');

                data.forEach(vote => {

                    //color slots with heat map 

                    let slotNumber = vote.slot_number; 
                    const voteCount = vote.vote_count;
                    let dayName = vote.day;

                    let day_index = week.findIndex(weekDay => weekDay === dayName);

                    if(day_index !== -1){

                        const startHourSlot = 8;
                        const endHourSlot= 20;
                        const intervalSlot= 30;

                        
                        let slotCount = 0;
                        let columnCount= 0;
                        let rowCount = 0;
                        
                        let slotIndex = day_index + slotNumber * 7;
                        

                        
                        
                        if (timeTableSlots[slotIndex]){

                            const intensity = voteCount/maxVotes;
                            if(intensity > 0.66){
                                timeTableSlots[slotIndex].classList.add('high');
                            }else if (intensity> 0.33){
                                timeTableSlots[slotIndex].classList.add('medium');
                            }else{
                                timeTableSlots[slotIndex].classList.add('low');
                            }
                        }
                    }
                }); 

                    
                })
       
            .catch(error => {
                console.error("Error fetching polls:", error);
                
            })
    
</script>

<script>
    
    //

    function closePoll(){    

        let pollID = <?php echo json_encode($pollId); ?>;

        const PollData = {
            pollId: pollID
        };

        fetch('closePoll.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }, 
            body: JSON.stringify(PollData)

        })
        .then(response => response.json())
        .then (data => {
            if (data.success){
                alert(data.message);
                document.getElementById('poll-inactive').disabled = true

            }else{
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
        console.error("Error closing poll:", error);
        alert("An error occurred while closing the poll.");
            });
        }
</script>
    

            
        

</body>
</html>
