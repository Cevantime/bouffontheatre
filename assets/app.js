/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

import '@splidejs/splide/css';
import 'cropperjs/dist/cropper.css';

import './extensions';

// start the Stimulus application
// import 'bootstrap/js/dist/util';
import { Modal } from 'bootstrap';
// Expose only Modal component to window for Stimulus controllers
window.bootstrap = { Modal };

import './bootstrap';
