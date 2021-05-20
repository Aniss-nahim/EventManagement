/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// Bootstrap & jquery
// ------------------
import 'bootstrap';
import $ from 'jquery';

window.Popper = require('popper.js').default;
window.$ = window.jQuery = $;


// Vue component loader
// --------------------
import Vue from 'vue';

// This bus is poviding siblings communication component
export const eventListener = new Vue();

Vue.component('app', require('./components/App.vue').default);
Vue.component('eventcreator', require('./components/CreateEvent.vue').default );
Vue.component('toast', require('./components/Toast.vue').default);
Vue.component('eventfilter', require('./components/FilterEvent.vue').default);

new Vue({
    el : "#app",
});

// Images
import logo from './images/app/EventLogo.svg';
