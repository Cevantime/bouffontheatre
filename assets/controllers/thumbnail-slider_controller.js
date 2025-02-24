import { Controller } from '@hotwired/stimulus';
import Splide from "@splidejs/splide";

export default class extends Controller {
    static values = { perPage: { type: Number, default: 6 } }
    connect() {
        let nav = new Splide(this.element.querySelector('.thumbnail-nav-carousel'), {
            gap: 0,
            rewind: true,
            pagination: false,
            focus: 'center',
            isNavigation: true,
            perPage: this.perPageValue,
            // breakpoints: {
            //     600: {
            //         fixedWidth : 60,
            //         fixedHeight: 44,
            //     },
            // },
        });

        let main = new Splide(this.element.querySelector('.thumbnail-main-carousel'), {
            type: 'fade',
            rewind: true,
            pagination: false,
            arrows: false,
        });

        main.sync(nav);
        main.mount();
        nav.mount();
    }
}