import {Controller} from "@hotwired/stimulus";
import Routing from 'fos-router';

export default class extends Controller {
    connect() {
        document.querySelectorAll('.cross-row').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.parseCrossCheck(checkbox);
            });
        });
    }

    parseCrossCheck(checkbox) {
        const tr = checkbox.closest('tr');
        const checked = checkbox.checked ? 1 : 0;

        fetch(Routing.generate('app_reservation_check', {id: tr.dataset.id}), {
            method: 'PATCH',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({checked})
        }).then(() => tr.classList.toggle('crossed', checkbox.checked));
    }
}


