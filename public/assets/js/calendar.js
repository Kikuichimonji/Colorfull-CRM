document.addEventListener('DOMContentLoaded', () => {
    var calendarEl = document.getElementById('calendar-holder');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        defaultView: 'dayGridMonth',
        editable: true,
        themeSystem: 'bootstrap',
        eventSources: [
            {
                url: "/fc-load-events",
                method: "POST",
                extraParams: {
                    filters: JSON.stringify({})
                },
                failure: () => {
                    // alert("There was an error while fetching FullCalendar!");
                },
            },
        ],
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'bootstrap' ], // https://fullcalendar.io/docs/plugin-index
        timeZone: 'UTC',
        height: "auto",
    });
    calendar.render();
});