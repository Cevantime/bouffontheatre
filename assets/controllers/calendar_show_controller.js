import { Controller } from '@hotwired/stimulus';
import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import frLocale from '@fullcalendar/core/locales/fr';
import Routing from 'fos-router';


export default class extends Controller {

    static targets = ['calendar', 'viewWrap'];

    connect() {
        let calendarEl = this.calendarTarget;
        let { eventsUrl } = calendarEl.dataset;
        this.calendar = new Calendar(calendarEl, {
            eventSources: [
                {
                    url: eventsUrl,
                    method: "POST",
                    extraParams: {
                        filters: JSON.stringify({}) // pass your parameters to the subscriber
                    },
                    failure: () => {
                        // alert("There was an error while fetching FullCalendar!");
                    },
                },
            ],
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek"
            },
            locale: frLocale,
            initialView: "dayGridMonth",
            navLinks: true, // can click day/week names to navigate views
            plugins: [interactionPlugin, dayGridPlugin, timeGridPlugin, listPlugin],
            eventClick: (info) => {
                let updateUrl = Routing.generate('app_booking_show', {
                    id: info.event.id
                });
                fetch(updateUrl)
                    .then((r) => r.text())
                    .then((html) => {
                        let viewWrap = this.viewWrapTarget;
                        viewWrap.innerHTML = html;
                        viewWrap.scrollIntoView();
                    })
            }
        });

        this.calendar.render();

        setInterval(() => {
            this.calendar.refetchEvents();
        }, 30000);

    }
}