import { Controller } from '@hotwired/stimulus';
import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import frLocale from '@fullcalendar/core/locales/fr';
import Routing from 'fos-router';


export default class extends Controller {

    static targets = ['calendar', 'formWrap'];

    connect() {
        let calendarEl = this.calendarTarget;
        let { eventsUrl } = calendarEl.dataset;
        this.calendar = new Calendar(calendarEl, {
            editable: true,
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
            selectable: true,
            select: (selection) => {
                let dateStart = this.convertDateToPhpFormat(selection.start);
                let dateEnd = this.convertDateToPhpFormat(selection.end)

                this.createForm(dateStart, dateEnd);

                let formWrap = this.formWrapTarget;
                let form = formWrap.querySelector("#form");
                form.scrollIntoView();
            },
            eventDrop: (info) => {
                let dateStart = this.convertDateToPhpFormat(info.event.start);
                let dateEnd = this.convertDateToPhpFormat(info.event.end);
                let updateUrl = Routing.generate('app_booking_update', {
                    id: info.event.id
                });
                fetch(updateUrl, {
                    method: 'post',
                    body: new URLSearchParams({
                        dateStart,
                        dateEnd
                    })
                })
                    .then(() => {
                        // this.calendar.refetchEvents()
                    })
            },
            eventClick: (info) => {
                let updateUrl = Routing.generate('app_booking_edit', {
                    id: info.event.id
                });
                fetch(updateUrl)
                    .then((r) => r.text())
                    .then((html) => {
                        let formWrap = this.formWrapTarget;
                        formWrap.innerHTML = html;
                        this.connectForm();
                        form.scrollIntoView();
                    })
            }
        });

        this.calendar.render();

        this.generateForm();

        setInterval(() => {
            this.calendar.refetchEvents();
        }, 30000);
    }

    createForm(dateStart, dateEnd) {
        let newUrl = Routing.generate('app_booking_new', {
            dateStart,
            dateEnd
        });
        fetch(newUrl)
            .then((r) => r.text())
            .then((html) => {
                let formWrap = this.formWrapTarget;
                formWrap.innerHTML = html;
                this.connectForm();
            })
    }

    generateForm() {
        fetch(Routing.generate("app_booking_new"))
            .then((r) => r.text())
            .then((html) => {
                let formWrap = this.formWrapTarget;
                formWrap.innerHTML = html;
                this.connectForm();
            });

    }

    connectForm() {
        let formWrap = this.formWrapTarget;
        let form = formWrap.querySelector("#form");
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            let f = e.target;
            fetch(f.action, { method: f.method, body: new FormData(f) })
                // .then(r => r.json())
                .then((resp) => {
                    if(resp.status === 422) {
                        return resp.text();
                    } else {
                        this.generateForm();
                        this.calendar.refetchEvents();
                    }
                })
                .then((t)=>{
                    formWrap.innerHTML = t;
                    this.connectForm();
                })
                .catch((reason)=> {
                    console.log('error')
                })
            ;
        });
        let formDelete = formWrap.querySelector("#form-delete");
        if (formDelete != null) {
            formDelete.addEventListener('submit', (e) => {
                e.preventDefault();
                if (confirm('Voulez-vous vraiment supprimer cet évènement ?')) {
                    let f = e.target;
                    fetch(f.action, { method: f.method, body: new FormData(f) })
                        // .then(r => r.json())
                        .then(() => {
                            this.generateForm();
                            this.calendar.refetchEvents();
                        });
                }
            });
        }
    }

    convertDateToPhpFormat(d) {
        return [
            d.getDate().padLeft(),
            (d.getMonth() + 1).padLeft(),
            d.getFullYear()].join('/') + ' ' +
            [d.getHours().padLeft(),
            d.getMinutes().padLeft(),
            d.getSeconds().padLeft()].join(':');
    }
}