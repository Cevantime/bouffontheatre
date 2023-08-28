import { Controller } from '@hotwired/stimulus';
import Splide from "@splidejs/splide";

export default class extends Controller {
    static values = { perPage: { type: Number, default: 4 } }
    connect() {
        new Splide(this.element, {
            perPage: this.perPageValue,
            breakpoints: {
                700: {
                    perPage: Math.round(this.perPageValue / 4)
                },
                1000: {
                    perPage: Math.round(this.perPageValue / 2)
                }
            }
        }).mount();
    }
}