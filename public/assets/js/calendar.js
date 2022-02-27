let menuLinks = document.querySelectorAll(".navbar-nav .nav-link")
menuLinks[1].classList.add("active")

let styles = window.getComputedStyle(document.querySelector('#mainPlanningBlock h1'));
let margin = parseFloat(styles['marginTop']) + parseFloat(styles['marginBottom']);
let marginBottom = 50;
let calendarHeight = document.getElementById('mainPlanningBlock').offsetHeight - (document.querySelector('#mainPlanningBlock h1').offsetHeight + margin + marginBottom)
let externalEvents = document.querySelectorAll("#externalEvents .fc-event");
let planning = document.getElementById("mainPlanningBlock")
let modal = document.querySelector(".modal");
let csrfToken = document.getElementById("_tokenUpdate").value;


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
                hexaColor : bcolor,
                isImportant : 0,
                planning : planning.hiddenId,
            };
        },
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
            saveEvent(info,calendar)
        },
        eventDrop: function(info){
            updateEvent(info,calendar)
        },
        eventResize: function(info){
            updateEvent(info,calendar)
        },
        eventClick: function(info) {
            modal.style.display = "block";
            //console.log(info.event)
            modal.querySelectorAll("#eventType option")[info.event.extendedProps.eventType -1].selected = true
            modal.querySelector("#label").value = info.event.title
            modal.querySelector("#dateStart").value = info.event.start?.toISOString().split('.')[0]  
            modal.querySelector("#dateEnd").value = info.event.end?.toISOString().split('.')[0] ;
            modal.querySelector("#description").value = info.event.extendedProps.description;
            modal.querySelector("#color").value = info.event.extendedProps.customColor ?? info.event.backgroundColor;
            modal.querySelector("#isImportant").checked = info.event.extendedProps.isImportant ? true : false;
            modal.hiddenId = info.event.id;

            modalUpdateEvent = function (ev)
            {
                ev.stopImmediatePropagation();
                ev.target.removeEventListener("click" , modalUpdateEvent);
                //console.log(info.event.id)
                args = {
                    "id" : info.event.id,
                    "label" : modal.querySelector("#label").value,
                    "dateStart" : modal.querySelector("#dateStart").value,
                    "dateEnd" : modal.querySelector("#dateEnd").value  == "" ? null : modal.querySelector("#dateEnd").value,
                    "eventType" : modal.querySelector("#eventType").selectedIndex + 1,
                    "planning" : planning.hiddenId,
                    "description" : modal.querySelector("#description").value,
                    "color" : modal.querySelector("#color").value,
                    "isImportant" : modal.querySelector("#isImportant").checked,
                    '_token' : csrfToken,
                };
                updateEvent("",calendar,args)
                modal.style.display = "none";
                //location.reload();
            }
            modalDeleteEvent = function (ev)
            {
                ev.stopImmediatePropagation();
                ev.target.removeEventListener("click" , modalDeleteEvent);
                id = info.event.id;

                deleteEvent(id,calendar)
                modal.style.display = "none";
                location.reload();
            }
            modal.querySelector(".btn-primary").addEventListener("click", modalUpdateEvent)
            modal.querySelector(".btn-danger").addEventListener("click", modalDeleteEvent)
        }
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

function updateEvent(info,calendar,args = null) 
{
    //console.log(info.event.extendedProps)
    args = args ? args : {
        "id" : info.event.id,
        "label" : info.event.title,
        "dateStart" : info.event.start.toISOString().split('.')[0] ,
        "dateEnd" : info.event.end ? info.event.end.toISOString().split('.')[0] : null,
        "eventType" : info.event.extendedProps.eventType,
        "planning" : info.event.extendedProps.planning,
        "description" : info.event.extendedProps.description,
        "color" : info.event.extendedProps.customColor,
        "isImportant" : info.event.extendedProps.isImportant,
        '_token' : csrfToken,
    };
    link = "/calendar/save"
    //console.log(args)
    goFetch(args,calendar,link);
}

function deleteEvent(id,calendar) 
{

    args = {
        "id" : id,
        "_token" : document.querySelector("#_token").value
    };
    link = "/calendar/delete"
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
            xError ? console.log(decodeURI(xError)) : null;
    
            if (!response.ok) {
                // get error message from body or default to response status
                const error = (data && data.message) || response.status;
                return Promise.reject(error);
            }else{
                data.then(value => { console.log(value)})
                //calendar.refetchEvents();
                
            }
        })
        .catch(error => {
            console.log('There was an error!', error);
        })
}

modal.querySelector(".btn-close").addEventListener("click", ev => {
    modal.style.display = "none"
})
modal.querySelector(".btn-info").addEventListener("click", ev => {
    modal.style.display = "none"
})

