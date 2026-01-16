import { Controller } from '@hotwired/stimulus';
import Cropper from 'cropperjs';

export default class extends Controller {
	static targets = ['file', 'preview', 'cropperContainer'];
	static values = {
		aspectRatio: String,
		existingUrl: String,
		modalId: String
	};

	cropper = null;
	pendingImageUrl = null;

	connect() {
		console.log('[Cropper] Controller connected', {
			modalId: this.modalIdValue,
			aspectRatio: this.aspectRatioValue,
			hasFile: this.hasFileTarget,
			hasPreview: this.hasPreviewTarget,
			hasContainer: this.hasCropperContainerTarget
		});

		// Prépare l'instance Bootstrap Modal et attache les hooks de cycle de vie
		const modalElement = this.getModalElement();
		if (modalElement && window.bootstrap?.Modal) {
			this.modal = new window.bootstrap.Modal(modalElement);

			// Initialise le cropper une fois la modale affichée (pour avoir une largeur > 0)
			modalElement.addEventListener('shown.bs.modal', () => {
				if (this.pendingImageUrl) {
					this.initCropper(this.pendingImageUrl);
				}
			});

			// Nettoie le cropper quand on ferme
			modalElement.addEventListener('hidden.bs.modal', () => {
				if (this.cropper) {
					this.cropper.destroy();
					this.cropper = null;
				}
			});
		}
	}

	fileSelected(event) {
		const file = event.target.files[0];
		if (!file) return;

		console.log('[Cropper] File selected:', file.name);

		const formData = new FormData();
		formData.append('file', file);

		fetch('/temp-image-upload', {
			method: 'POST',
			body: formData
		})
			.then(response => {
				if (!response.ok) throw new Error('Upload failed');
				return response.json();
			})
			.then(result => {
				console.log('[Cropper] Upload successful:', result);

				// Mémorise le chemin temporaire (pour sauvegarde côté serveur)
				this.setTempPath(result.tempPath);

				// Prépare l'image et ouvre la modale; le cropper sera initialisé sur shown.bs.modal
				this.pendingImageUrl = result.tempPath;

				// Affiche le bouton "Modifier" s'il n'existe pas
				this.showModifyButton();

				this.openModal();
			})
			.catch(error => {
				console.error('[Cropper] Upload error:', error);
				alert('Erreur lors de l\'upload');
			});
	}

	prepareExistingModal(event) {
		const existingUrl = this.existingUrlValue;
		console.log('[Cropper] Preparing existing image:', existingUrl);

		// L'init du cropper se fera sur shown.bs.modal; on stocke juste l'URL
		if (existingUrl) {
			this.pendingImageUrl = existingUrl;
		}
	}

	initCropper(imageUrl) {
		console.log('[Cropper] Setting up cropper with image:', imageUrl);

		if (!this.hasCropperContainerTarget) {
			console.error('[Cropper] Cropper container target not found');
			return;
		}

		const container = this.cropperContainerTarget;

		// Nettoie et affiche le conteneur
		container.innerHTML = '';
		container.style.display = 'block';

		// Crée l'image et attend le chargement avant d'initialiser Cropper
		const img = document.createElement('img');
		img.style.maxWidth = '100%';
		img.style.height = 'auto';
		img.src = imageUrl;

		const init = () => {
			// Détruit l'ancien cropper s'il existe
			if (this.cropper) {
				this.cropper.destroy();
			}

			try {
				this.cropper = new Cropper(img, {
					aspectRatio: this.aspectRatioValue ? parseFloat(this.aspectRatioValue) : NaN,
					autoCropArea: 1,
					responsive: true,
					restore: true,
					guides: true,
					center: true,
					highlight: true,
					cropBoxMovable: true,
					cropBoxResizable: true,
					toggleDragModeOnDblclick: true,
				});
				console.log('[Cropper] Initialized successfully');
			} catch (e) {
				console.error('[Cropper] Error initializing:', e);
			}
		};

		img.onload = init;
		img.onerror = () => console.error('[Cropper] Image failed to load');

		container.appendChild(img);
	}

	applyCrop(event) {
		event.preventDefault();

		if (!this.cropper) {
			console.error('[Cropper] Cropper instance not available');
			return;
		}

		console.log('[Cropper] Applying crop...');

		const canvas = this.cropper.getCroppedCanvas();
		const cropData = this.cropper.getData(true); // true = arrondi, données sérialisables
		const tempPath = this.getTempPath();

		console.log('[Cropper] Crop data:', cropData, 'Temp path:', tempPath);

		// Sauvegarde les données dans le champ caché (pour le POST du formulaire)
		const cropDataField = this.element.querySelector('[name*="cropData"]');
		if (cropDataField) {
			cropDataField.value = JSON.stringify(cropData);
		}

		// Met à jour l'aperçu
		if (this.hasPreviewTarget) {
			this.previewTarget.src = canvas.toDataURL();
		}

		// Ferme la modale
		const modalElement = document.getElementById(this.modalIdValue);
		if (modalElement) {
			const modal = window.bootstrap.Modal.getInstance(modalElement);
			if (modal) {
				console.log('[Cropper] Closing modal');
				modal.hide();
			} else {
				console.error('[Cropper] Could not get modal instance');
			}
		}
	}

	getTempPath() {
		const input = this.element.querySelector('[name*="tempPath"]');
		return input ? input.value : '';
	}

	setTempPath(path) {
		const input = this.element.querySelector('[name*="tempPath"]');
		if (input) {
			input.value = path;
		}
	}

	openModal() {
		if (this.modal) {
			this.modal.show();
			return;
		}

		const modalElement = this.getModalElement();
		if (!modalElement) {
			console.error('[Cropper] Modal not found (id=', this.modalIdValue, ')');
			return;
		}

		if (!window.bootstrap?.Modal) {
			console.error('[Cropper] Bootstrap Modal missing on window');
			return;
		}

		this.modal = new window.bootstrap.Modal(modalElement);
		this.modal.show();
	}

	getModalElement() {
		const id = this.modalIdValue;
		if (!id) return null;

		// Cherche globalement en priorité (id unique attendu)
		const globalEl = document.getElementById(id);
		if (globalEl) return globalEl;

		// Fallback: cherche dans le scope du contrôleur en échappant l'ID si nécessaire
		try {
			if (window.CSS?.escape) {
				return this.element.querySelector(`#${CSS.escape(id)}`);
			}
			return this.element.querySelector(`#${id}`);
		} catch (e) {
			try {
				return this.element.querySelector(`[id="${id}"]`);
			} catch (e2) {
				return null;
			}
		}
	}

	showModifyButton() {
		// Cherche s'il existe déjà un bouton "Modifier"
		let modifyBtn = this.element.querySelector('.croppable-modify-btn');

		if (!modifyBtn) {
			// Crée le bouton dynamiquement
			modifyBtn = document.createElement('button');
			modifyBtn.type = 'button';
			modifyBtn.className = 'btn btn-outline-primary btn-sm mt-3 croppable-modify-btn';
			modifyBtn.textContent = '✏️ Modifier';
			modifyBtn.setAttribute('data-bs-toggle', 'modal');
			modifyBtn.setAttribute('data-bs-target', `#${this.modalIdValue}`);
			modifyBtn.addEventListener('click', () => this.prepareExistingModal());

			// Insère le bouton après le file input
			const fileInput = this.element.querySelector('[data-action*="fileSelected"]');
			if (fileInput && fileInput.parentNode) {
				fileInput.parentNode.appendChild(modifyBtn);
			}
		}

		modifyBtn.style.display = 'block';
	}
}
