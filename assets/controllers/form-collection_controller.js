import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const performanceList = this.element;

        for (let i = 0; i < performanceList.children.length; i++) {
            const row = performanceList.children.item(i);
            this.addDeleteButton(row);
        }

        this.refreshLabels();

        const performanceRowPrototype = performanceList.attributes.getNamedItem("data-prototype").textContent;
        // $performanceList.each(function () {
        //     $(this).find('label').text('Représentation n°' + ($(this).index()));
        // });

        const addButton = document.createElement('button');

        addButton.classList.add('btn', 'btn-success');
        addButton.textContent = 'Ajouter';

        addButton.addEventListener('click', (e) => {
            e.preventDefault();
            const newIndex = performanceList.children.length;
            const newRowHTML = performanceRowPrototype
                .replace(/__name__label__/g, 'Élement n°' + (newIndex + 1))
                .replace(/__name__/g, newIndex)
            ;
            const newRow = this.createElementFromHTML(newRowHTML);
            performanceList.appendChild(newRow);
            this.addDeleteButton(newRow);
        });

        performanceList.parentNode.appendChild(addButton);
    }

    addDeleteButton(row) {
        const button = document.createElement('button');
        button.classList.add('btn', 'btn-danger');
        button.textContent = 'Supprimer';
        button.addEventListener('click', () => {
            row.remove();
            this.refreshLabels();
        });
        row.appendChild(button);
    }

    createElementFromHTML(html) {
        const template = document.createElement('template');
        template.innerHTML = html.trim();
        return template.content.firstChild;
    }

    refreshLabels() {
        const performanceList = this.element;
        for (let i = 0; i < performanceList.children.length; i++) {
            const row = performanceList.children.item(i);
            row.querySelector('label').textContent = 'Élément n°' + (i + 1);
        }
    }
}
