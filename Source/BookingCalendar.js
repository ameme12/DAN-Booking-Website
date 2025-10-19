//Author: Daniel Alzawahra

class BookingCalendar
{
    constructor(containerID, options ={})
    {
        this.container = document.getElementById(containerID);
        this.options = options;
        this.currentDate = options.startDate;
        this.multiselect = options.multiselect;
        this.globalDates = options.globalDates;

        this.selectedSlotId = null;
        this.bookingId = options.bookingId || -1;

        this.data = options.data;


        this.buttonId = options.buttonId || '';
        this.userId = options.userId || 1;

        this.checkInput = false;


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
        this.HideForm();
        if(this.buttonId !== '')
            document.getElementById(this.buttonId).onclick = () => this.SubmitBooking();

    }

    showHeader()
    {
        let header = document.createElement('div');
        let monthYear = this.currentDate.toLocaleDateString('en-us', {month: 'long', year:'numeric'});
        header.className = 'calendar-header';
        header.innerHTML =
            `
            <button id="prevMonth">&lt;</button>
            <span id="monthYear">${monthYear}</span>
            <button id="nextMonth">&gt;</button>
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
        const daysContainer = document.createElement('div');
        daysContainer.className = 'calendar-days';
        daysContainer.id = 'calendar-days';

        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        const daysThisMonth = new Date(year, month + 1, 0).getDate();
        const firstDay = new Date(year, month, 1).getDay();
        const pad = (firstDay + 6) % 7;
        const today = new Date();
        const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;

        this.container.querySelectorAll('.calendar-days').forEach(element => element.remove());

        if(this.options.mode === 'availability')
        {
            this.availableDays = this.ExtractDays();
        }

        for(let day = 1; day <= daysThisMonth + pad; day++)
        {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';

            if(day > pad)
            {
                const date = day - pad;
                dayElement.innerText = date;
                const key = `${year}-${month}-${date}`;

                if(this.options.mode === 'availability')
                {

                    if(!this.availableDays.includes(key))
                    {
                        dayElement.classList.add('day-inactive');
                        dayElement.style.pointerEvents = 'none';
                    }
                    else
                    {

                        dayElement.classList.add('day-selected');
                        dayElement.addEventListener('click', () =>
                        {
                            this.ShowSidebar(
                                {
                                    slots: this.options.data.slots.filter(slot => slot.js_date === `${year}-${month+1}-${date}`),
                                });
                        });
                    }
                }
                else if(isCurrentMonth && date < today.getDate())
                {
                    dayElement.classList.add('day-inactive');
                    dayElement.style.pointerEvents = 'none';
                }
                else
                {
                    this.dayListeners(dayElement, date);
                }
            }

            daysContainer.appendChild(dayElement);
        }

        this.container.appendChild(daysContainer);
    }



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

    //Handles selection of a date Interval
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

    //Ensures the end date is AFTER  the start date
    IsValidEndDate(startKey, endKey)
    {
        const [yearStart, monthStart, dayStart] = startKey.split('-').map(Number);
        const [yearEnd, monthEnd, dayEnd] = endKey.split('-').map(Number);

        const start = new Date(yearStart, monthStart, dayStart);
        const end = new Date(yearEnd, monthEnd, dayEnd);

        return end >= start;
    }

    //Clears all selections
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

    //Highlights the range of selected days in a Recurring Booking
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
    //Applies CSS changes to day elements within the Calendar
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



    //Handles the Calendar's prev/next buttons.
    eventListeners()
    {
        let prev = this.container.querySelector('#prevMonth');
        let next = this.container.querySelector('#nextMonth');



        prev.addEventListener('click', () =>
        {
            let newDate = new Date(this.currentDate);
            newDate.setMonth(newDate.getMonth()-1);

            if((newDate >= this.baseDate))
            {
                this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                this.UpdateCalendar();
                this.advancesAllowed +=1;
            }

        });

        next.addEventListener('click', () =>
        {
            if(this.advancesAllowed > 0)
            {
                this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                this.UpdateCalendar();
                this.advancesAllowed -=1;
            }

        });
    }

    //Re-renders the Calendar
    UpdateCalendar()
    {
        this.container.querySelector('#monthYear').innerText = this.currentDate.toLocaleDateString('en-us', {month: 'long', year:'numeric'});
        this.showDays();
        if(this.isRecurring && this.options.mode === 'selection')
            this.HighlightRange();
    }



    //Reveals the hidden Sidebar. Input data is used to figure out which time slots to show. Builds a list
    ShowSidebar(data)
    {

        document.getElementById('calendar-container').classList.add('calendar-trim');

        let sidebar = document.getElementById('calendar-sidebar');

        let timesList = data.slots.map(slot =>
        {
            let [hour, minute] = slot.start_time.split(':');
            return `
        <li data-slot-id="${slot.slotId}">
             ${hour}:${minute}
        </li>
    `;
        }).join('');

        sidebar.innerHTML = `
        <h3>Available Times</h3>
        <ul id="calendar-times">${timesList}</ul>
    `;
        sidebar.style.display = 'block';

        let slots = sidebar.querySelectorAll('#calendar-times li');
        slots.forEach(slot =>
        {
            slot.addEventListener('click', () =>
            {
                slots.forEach(otherSlot => otherSlot.classList.remove('calendar-time-selected'));
                slot.classList.add('calendar-time-selected');

                let selectedSlotId = slot.dataset.slotId;
                this.selectedSlotId = selectedSlotId;
            });
        });
    }


    //Returns the days, offset by 1 month (Because December is 11 not 12 in JS for some reason)
    ExtractDays()
    {
        let days = new Set();

        if (this.options.data.slots)
        {
            this.options.data.slots.forEach(slot =>
            {
                let [year, month, day] = slot.js_date.split('-').map(Number);

                month -= 1;

                days.add(`${year}-${month}-${day}`);
            });
        }

        return Array.from(days);
    }


    //Hides the form in case it is not needed.
    HideForm()
    {


        const nameField = document.getElementById("user-name");
        const emailField = document.getElementById("user-email");
        const submitButton = document.getElementById("poll-submit");

        if(this.userId !== 1)
        {
            nameField.parentNode.style.display = "none";
            emailField.parentNode.style.display = "none";

            document.getElementById("poll-title").innerText = "Member";


        }
        else
        {
            this.checkInput = true;
            document.getElementById('site-sidebar').remove();
        }


    }

    //Submits data to PHP, attempting to reserve a slot in a Booking
    SubmitBooking()
    {
        if(this.checkInput)
        {
            let nameField = document.getElementById("user-name");
            let emailField = document.getElementById("user-email");

            if(!nameField.value.trim() || !emailField.value.trim())
            {
                alert("Please enter your information");
                return;
            }

        }

        if(!this.selectedSlotId)
        {
            alert("Please select a time slot.");
            return;
        }

        let slotId = this.selectedSlotId;
        let bookingId = this.bookingId;
        let userId = this.userId;






        fetch("ReserveSlot.php",
            {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(
                    {
                        bookingId, slotId, userId
                    })
            })
            .then(response => response.json())
            .then(res =>
            {
                if(res.success)
                {
                    alert("Booking successful!");
                    window.location.href = "dashboard.php";
                }
                else
                {
                    alert(res.message);
                }
            });
    }


}
