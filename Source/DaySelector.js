let showButton = document.getElementById("schedule-show-button");
let container = document.getElementById("schedule-container");

const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

//Generates 30-minute slots from midnight to midnight
function GenerateTimeSlots()
{
    const times = [];
    for (let hour = 0; hour < 24; hour++)
    {
        for (let minute = 0; minute < 60; minute += 30)
        {
            let formattedHour = String(hour).padStart(2, "0");
            let formattedMinute = String(minute).padStart(2, "0");
            times.push(`${formattedHour}:${formattedMinute}`);
        }
    }
    return times;
}

const timeOptions = GenerateTimeSlots();

//Builds the timetable with the given time slots
function CreateSchedule()
{
    let schedule = container.querySelector(".schedule");

    days.forEach(day =>
    {
        let row = document.createElement("div");
        row.className = "schedule-row";

        let checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        row.appendChild(checkbox);

        let dayLabel = document.createElement("label");
        dayLabel.textContent = day;
        row.appendChild(dayLabel);

        let startTime = document.createElement("select");
        startTime.className = "schedule-time-input";
        CreateInputs(startTime, timeOptions);
        row.appendChild(startTime);

        let rangeLabel = document.createElement("span");
        rangeLabel.className = "range-label";
        rangeLabel.textContent = "To";
        row.appendChild(rangeLabel);

        let endTime = document.createElement("select");
        endTime.className = "schedule-time-input";
        CreateInputs(endTime, timeOptions);
        row.appendChild(endTime);

        endTime.value = "23:30";

        startTime.addEventListener("change", () =>
        {
            UpdateOptions(startTime, endTime);
        });

        schedule.appendChild(row);
    });
}

//Creates data for each Time Slot
function CreateInputs(selector, options)
{
    selector.innerHTML = "";
    options.forEach(time =>
    {
        let option = document.createElement("option");
        option.value = time;
        option.textContent = time;
        selector.appendChild(option);
    });
}
//Cuts impossible options: A timeslot can't end in a past. Starting at 9 must end after 9
function UpdateOptions(start, end)
{
    let selectedIndex = timeOptions.indexOf(start.value);
    let currentEndValue = end.value;
    let validTimes = timeOptions.slice(selectedIndex + 1);

    end.innerHTML = "";

    validTimes.forEach(time =>
    {
        let option = document.createElement("option");
        option.value = time;
        option.textContent = time;
        end.appendChild(option);
    });

    if (validTimes.includes(currentEndValue))
    {
        end.value = currentEndValue;
    }
    else if (end.options.length > 0)
    {
        end.value = end.options[0].value;
    }
}

//Returns the Day of the slot, and its start + end times
function GetSelections()
{
    let scheduleRows = container.querySelectorAll(".schedule-row");
    let selectedSchedule = [];

    scheduleRows.forEach(row =>
    {
        let checkbox = row.querySelector("input[type='checkbox']");
        let day = row.querySelector("label").textContent;
        let Start = row.querySelector("select:nth-of-type(1)").value;
        let end = row.querySelector("select:nth-of-type(2)").value;

        if (checkbox.checked)
        {
            selectedSchedule.push(
                {
                    day: day,
                    start: Start,
                    end: end
                });
        }
    });

    return selectedSchedule;
}


//Verifies if the user actually made a choice
function CheckDaySelections()
{
    let rows = container.querySelectorAll(".schedule-row");
    let hasSelection = false;

    rows.forEach(row =>
    {
        let checkbox = row.querySelector("input[type='checkbox']");
        if (checkbox.checked)
        {
            hasSelection = true;
        }
    });

    return hasSelection;
}



CreateSchedule();
