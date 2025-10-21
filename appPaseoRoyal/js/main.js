document.addEventListener('DOMContentLoaded', function() {
    // Calendar Management
    const calendarView = document.getElementById('calendar-view');
    const calendarContainer = document.getElementById('calendar-container');
    const prevButton = document.getElementById('prev');
    const nextButton = document.getElementById('next');
    const currentDateElement = document.getElementById('current-date');
    let currentDate = new Date();

    function updateCalendar() {
        const view = calendarView.value;
        const container = calendarContainer;
        container.innerHTML = '';
        updateCurrentDateDisplay();

        switch(view) {
            case 'month':
                renderMonthView();
                break;
            case 'week':
                renderWeekView();
                break;
            case 'day':
                renderDayView();
                break;
        }
    }

    function updateCurrentDateDisplay() {
        const options = { year: 'numeric', month: 'long' };
        currentDateElement.textContent = currentDate.toLocaleDateString('es-ES', options);
    }

    function renderMonthView() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const firstDayOfWeek = firstDay.getDay();

        const calendarHTML = `
            <div class="calendar-header">
                <div class="weekdays">
                    <div>Dom</div><div>Lun</div><div>Mar</div><div>Mié</div>
                    <div>Jue</div><div>Vie</div><div>Sáb</div>
                </div>
            </div>
            <div class="calendar-grid">
                ${generateMonthDays(firstDayOfWeek, daysInMonth)}
            </div>
        `;

        calendarContainer.innerHTML = calendarHTML;
        addCellClickListeners();
    }

    function renderWeekView() {
        const startOfWeek = new Date(currentDate);
        startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());
        
        let weekHTML = `
            <div class="week-view">
                <div class="time-slots">
                    ${generateTimeSlots()}
                </div>
                <div class="week-grid">
                    ${generateWeekDays(startOfWeek)}
                </div>
            </div>
        `;

        calendarContainer.innerHTML = weekHTML;
    }

    function renderDayView() {
        const dayHTML = `
            <div class="day-view">
                <h3>${currentDate.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</h3>
                <div class="time-slots">
                    ${generateDayTimeSlots()}
                </div>
            </div>
        `;

        calendarContainer.innerHTML = dayHTML;
    }

    function generateMonthDays(firstDayOfWeek, daysInMonth) {
        let days = '';
        
        // Empty cells for days before the first day of the month
        for (let i = 0; i < firstDayOfWeek; i++) {
            days += '<div class="calendar-cell empty"></div>';
        }

        // Cells for each day of the month
        for (let day = 1; day <= daysInMonth; day++) {
            days += `
                <div class="calendar-cell" data-date="${day}">
                    <div class="date-number">${day}</div>
                    <div class="reservations"></div>
                </div>
            `;
        }

        return days;
    }

    function generateTimeSlots() {
        let slots = '';
        for (let hour = 6; hour <= 22; hour++) {
            slots += `
                <div class="time-slot">
                    <div class="hour">${hour}:00</div>
                </div>
            `;
        }
        return slots;
    }

    function generateWeekDays(startDate) {
        let days = '';
        for (let i = 0; i < 7; i++) {
            const currentDay = new Date(startDate);
            currentDay.setDate(startDate.getDate() + i);
            days += `
                <div class="week-day">
                    <div class="day-header">${currentDay.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric' })}</div>
                    ${generateTimeSlots()}
                </div>
            `;
        }
        return days;
    }

    function generateDayTimeSlots() {
        let slots = '';
        for (let hour = 6; hour <= 22; hour++) {
            slots += `
                <div class="day-time-slot" data-hour="${hour}">
                    <div class="hour">${hour}:00</div>
                    <div class="slot-content"></div>
                </div>
            `;
        }
        return slots;
    }

    function addCellClickListeners() {
        const cells = document.querySelectorAll('.calendar-cell:not(.empty)');
        cells.forEach(cell => {
            cell.addEventListener('click', () => showReservationForm(cell));
        });
    }

    function showReservationForm(cell) {
        const date = cell.dataset.date;
        const month = currentDate.getMonth();
        const year = currentDate.getFullYear();
        
        const reservationHTML = `
            <div class="reservation-form">
                <h3>Nueva Reservación</h3>
                <p>Fecha: ${date}/${month + 1}/${year}</p>
                <select id="reservation-time">
                    ${generateTimeOptions()}
                </select>
                <select id="duration">
                    <option value="1">1 hora</option>
                    <option value="2">2 horas</option>
                    <option value="3">3 horas</option>
                </select>
                <input type="text" id="client-name" placeholder="Nombre del cliente">
                <button onclick="saveReservation()">Reservar</button>
            </div>
        `;

        const existingForm = document.querySelector('.reservation-form');
        if (existingForm) {
            existingForm.remove();
        }
        cell.appendChild(document.createElement('div')).innerHTML = reservationHTML;
    }

    function generateTimeOptions() {
        let options = '';
        for (let hour = 6; hour <= 22; hour++) {
            options += `<option value="${hour}">${hour}:00</option>`;
        }
        return options;
    }

    // Navigation event listeners
    prevButton.addEventListener('click', () => {
        switch(calendarView.value) {
            case 'month':
                currentDate.setMonth(currentDate.getMonth() - 1);
                break;
            case 'week':
                currentDate.setDate(currentDate.getDate() - 7);
                break;
            case 'day':
                currentDate.setDate(currentDate.getDate() - 1);
                break;
        }
        updateCalendar();
    });

    nextButton.addEventListener('click', () => {
        switch(calendarView.value) {
            case 'month':
                currentDate.setMonth(currentDate.getMonth() + 1);
                break;
            case 'week':
                currentDate.setDate(currentDate.getDate() + 7);
                break;
            case 'day':
                currentDate.setDate(currentDate.getDate() + 1);
                break;
        }
        updateCalendar();
    });

    // Event Listeners
    calendarView.addEventListener('change', updateCalendar);

    // Global function for saving reservations
    window.saveReservation = function() {
        const time = document.getElementById('reservation-time').value;
        const duration = document.getElementById('duration').value;
        const clientName = document.getElementById('client-name').value;

        if (!clientName) {
            alert('Por favor ingrese el nombre del cliente');
            return;
        }

        alert(`Reservación guardada:\nCliente: ${clientName}\nHora: ${time}:00\nDuración: ${duration} hora(s)`);
        document.querySelector('.reservation-form').remove();
    };

    // Initial render
    updateCalendar();
});