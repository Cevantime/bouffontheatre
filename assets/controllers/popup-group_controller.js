import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['button', 'popup'];
    displayPopup() {
        this.popupTarget.classList.add('displayed');
    }
    hidePopup() {
        this.popupTarget.classList.remove('displayed');
    }
    stopPropagation(e) {
        e.stopPropagation();
    }
}