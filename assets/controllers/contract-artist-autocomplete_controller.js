import { Controller } from '@hotwired/stimulus';
import Routing from 'fos-router';

export default class extends Controller {

    tomSelect;

    initialize() {
        this._onPreConnect = this._onPreConnect.bind(this);
        this._onConnect = this._onConnect.bind(this);
    }

    connect() {
        this.element.addEventListener('autocomplete:pre-connect', this._onPreConnect);
        this.element.addEventListener('autocomplete:connect', this._onConnect);
    }

    disconnect() {
        // You should always remove listeners when the controller is disconnected to avoid side-effects
        this.element.removeEventListener('autocomplete:connect', this._onConnect);
        this.element.removeEventListener('autocomplete:pre-connect', this._onPreConnect);
    }

    _onPreConnect(event) {
        event.detail.options.create = (input) => {
            const parts = input.trim().split(' ');
            if (parts.length < 2) {
                alert('Vous devez fournir un nom et un prénom');
                return false;
            }
            const lastName = parts.pop();
            const firstName = parts.join(' ');

            const tempId = "id" + Math.random().toString(16).slice(2);

            const newOption = {
                value: tempId,
                text: input,
                data: {
                    firstName,
                    lastName,
                    isTemporary: true
                }
            };

            const createArtistUrl = Routing.generate('app_artist_create_ajax');
            fetch(createArtistUrl, {
                method: 'post',
                body: new URLSearchParams({
                    firstName,
                    lastName
                })
            }).then((rep) => {
                return rep.json()
            }).then((rep) => {
                this.tomSelect.removeOption(tempId);
                this.tomSelect.addOption({
                    value: rep.id,
                    text: rep.fullName,
                    rep
                });
                this.tomSelect.addItem(rep.id);
            })
            return newOption;
        };
    }

    _onConnect(event) {
        this.tomSelect = event.detail.tomSelect;
    }
}