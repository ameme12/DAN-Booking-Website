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
        document.addEventListener('mouseup', () =>
        {
            this.isSelecting = false;
            this.isDeselecting = false;
        });

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

    eventListeners()
    {
        this.container.addEventListener('mousedown', (e) =>
        {
            if (e.target.classList.contains('timetable-slot'))
            {
                let slot = e.target;
                let time = slot.dataset.time;
                let day = slot.dataset.day;

                let key = `${day}-${time}`;
                if (slot.classList.contains('slot-selected'))
                {
                    this.isDeselecting = true;
                    slot.classList.remove('slot-selected');
                    this.selectedSlots.delete(key);
                }
                else
                {
                    this.isSelecting = true;
                    slot.classList.add('slot-selected');
                    this.selectedSlots.add(key);
                }

            }
        });
        this.container.addEventListener('mousemove', (e) =>
        {
            if ((this.isSelecting || this.isDeselecting) && e.target.classList.contains('timetable-slot'))
            {
                let slot = e.target;
                let time = slot.dataset.time;
                let day = slot.dataset.day;
                let key = `${day}-${time}`;

                if (this.isSelecting && !slot.classList.contains('slot-selected'))
                {
                    slot.classList.add('slot-selected');
                    this.selectedSlots.add(key);
                }
                else if (this.isDeselecting && slot.classList.contains('slot-selected'))
                {
                    slot.classList.remove('slot-selected');
                    this.selectedSlots.delete(key);
                }

            }
        });

    }
    

    GetSelectedData()
    {
        return Array.from(this.selectedSlots).map(slot =>
        {
            let [day, time] = slot.split('-');
            let [hour, minute] = time.split(':').map(Number);

            let row = ((hour - this.startHour) * (60 / this.interval)) + (minute / this.interval);

            let column = this.days.indexOf(day);

            return { day, time, row: row, col: column };
        });
    }

    
    
}