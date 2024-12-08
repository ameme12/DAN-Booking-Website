

class Calendar
{
    constructor(containerID, options ={})
    {
        this.container = document.getElementById(containerID);
        this.options = options;
        this.currentDate = new Date();

        this.baseDate = new Date();
        this.selectedDays = new Set();

        this.isSelecting   = false;
        this.isDeselecting = false;


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
    }

    showHeader()
    {
        console.log("Yippeee");

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
        let daysContainer = document.createElement('div');
        daysContainer.className = 'calendar-days';
        daysContainer.id = 'calendar-days';

        let year = this.currentDate.getFullYear();
        let month = this.currentDate.getMonth();
        let daysThisMonth = new Date(year, month + 1, 0).getDate();
        let firstDay = new Date(year, month, 1).getDay();
        let pad = (firstDay + 6) % 7;

        this.container.querySelectorAll('.calendar-days').forEach(el => el.remove());

        for (let day = 1; day <= daysThisMonth + pad; day++)
        {
            let dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';

            if (day > pad)
            {
                const date = day - pad;
                dayElement.innerText = date;

                if (this.options.mode === 'availability')
                    this.manageMode(dayElement, date);
                else
                    this.dayListeners(dayElement, date);
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
        dayElement.addEventListener('mousedown', () =>
            {
            if (dayElement.classList.contains('day-selected'))
            {
                this.isDeselecting = true;
                dayElement.classList.remove('day-selected');
                this.selectedDays.delete(date);
            }
            else
            {
                this.isSelecting = true;
                dayElement.classList.add('day-selected');
                this.selectedDays.add(date);
            }

            if (this.options.onDaySelect)
                this.options.onDaySelect(Array.from(this.selectedDays));
        });

        dayElement.addEventListener('mousemove', () =>
        {
            if (this.isSelecting && !dayElement.classList.contains('day-selected'))
            {
                dayElement.classList.add('day-selected');
                this.selectedDays.add(date);
            }
            else if (this.isDeselecting && dayElement.classList.contains('day-selected'))
            {
                dayElement.classList.remove('day-selected');
                this.selectedDays.delete(date);
            }
        });
    }

    eventListeners()
    {
        let prevMonthButton = this.container.querySelector('#prevMonth');
        let nextMonthButton = this.container.querySelector('#nextMonth');

        

        prevMonthButton.addEventListener('click', () =>
        {
            let newDate = new Date(this.currentDate);
            newDate.setMonth(newDate.getMonth()-1);

            if((newDate >= this.baseDate))
            {
                this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                this.updateCalendar();
            }

        });

        nextMonthButton.addEventListener('click', () =>
        {
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.updateCalendar();
        });
    }

    updateCalendar()
    {
        this.container.querySelector('#monthYear').innerText = this.currentDate.toLocaleDateString('en-us', {month: 'long', year:'numeric'});
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

    showSidebar()
    {
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
        
        slots.forEach((slot)=>
        {
            slot.addEventListener('click', () =>
            {
                slots.forEach(otherSlot => otherSlot.classList.remove('calendar-time-selected'));

                slot.classList.add('calendar-time-selected');

            });
        });
    }
    
}
