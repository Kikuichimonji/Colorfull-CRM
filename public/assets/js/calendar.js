let styles = window.getComputedStyle(document.querySelector('#mainPlanningBlock h1'));
let margin = parseFloat(styles['marginTop']) + parseFloat(styles['marginBottom']);
let marginBottom = 50;
let calendarHeight = document.getElementById('mainPlanningBlock').offsetHeight - (document.querySelector('#mainPlanningBlock h1').offsetHeight + margin + marginBottom)
let externalEvents = document.querySelectorAll("#externalEvents .fc-event");
let planning = document.getElementById("mainPlanningBlock")


externalEvents.forEach( el => {
    el.hiddenId = el.getAttribute("data-id");
    el.removeAttribute("data-id");
})

planning.hiddenId = planning.getAttribute("data-id");
planning.removeAttribute("data-id");

document.addEventListener('DOMContentLoaded', () => {
    let calendarEl = document.getElementById('calendar');
    let containerEl = document.getElementById('externalEvents');
    let Draggable = FullCalendarInteraction.Draggable;

    new Draggable(containerEl, {
        itemSelector: '.fc-event',
        eventData: function(eventEl) {
            bcolor = eventEl.getAttribute("style").substr(-7)
            return { 
                title: eventEl.innerText,
                color: eventEl.style.backgroundColor,
                eventType : eventEl.hiddenId,
                hexaColor : bcolor = eventEl.getAttribute("style").substr(-7),
                isImportant : 0,
                planning : planning.hiddenId,
            };
        }
    });

    let calendar = new FullCalendar.Calendar(calendarEl, {
        defaultView: 'dayGridMonth',
        buttonText: {
            today: 'aujourd\'hui',
            month: 'mois',
            week: 'semaine',
            day: 'jour'
        },
        editable: true,
        droppable: true,
        themeSystem: 'bootstrap',
        events: events,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'bootstrap' ], // https://fullcalendar.io/docs/plugin-index
        timeZone: 'Europe/Paris',
        locale: 'fr',
        height: calendarHeight,
        eventReceive: function(info){
           // console.log(info.draggedEl)
            saveEvent(info,calendar)
        },
        eventDrop: function(info){
            //console.log(info.event.extendedProps)
            //saveEvent(info,calendar)
        },
        eventResize: function(info){
            //saveEvent(info,calendar)
        },
    });
    calendar.render();
});


function saveEvent(info,calendar) 
{
    //console.log(info.event)
    args = {
        "label" : info.event.title,
        "dateStart" : info.event.start.toISOString(),
        "dateEnd" : info.event.end ? info.event.end.toISOString() : null,
        "eventType" : info.event.extendedProps.eventType,
        "planning" : info.event.extendedProps.planning,
        "description" : info.event.extendedProps.description,
        "color" : info.event.extendedProps.hexaColor,
        "isImportant" : info.event.extendedProps.isImportant,
    };
    link = "/calendar"
    //console.log(args)
    goFetch(args,calendar,link);
}

function goFetch(args,calendar,link) 
{
    let myHeaders = new Headers(); //If we want custom headers
    myHeaders = {

    };
    let formData = new FormData(); //We append the POST data here

    for (const key in args) {
        formData.append(key,args[key])
    }
    let myInit = {
        method: 'POST',
        headers: myHeaders,
        mode: 'cors',
        cache: 'default',
        body: formData
    };
    const element = document.querySelector('#get-request .result');
    let myRequest = new Request(link, myInit);
    fetch(myRequest)
        .then((response) => {
            const isJson = response.headers.get('content-type')?.includes('application/json');
            const data = isJson ? response.json() : null;
            const xError = response.headers.get('X-debug-Exception');
            console.log(decodeURI(xError))
    
            if (!response.ok) {
                // get error message from body or default to response status
                const error = (data && data.message) || response.status;
                return Promise.reject(error);
            }else{
                console.log(response)
                calendar.refetchEvents();
            }
        })
        .catch(error => {
            console.log('There was an error!', error);
        })
}