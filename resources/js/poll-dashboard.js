import './bootstrap';
import { createApp } from 'vue';
import App from './AppPollDashboard.vue';

// Utilise le container #app de la vue Blade et les props passées en attribut pour monter l'app Vue
const el = document.getElementById('app');
const props = JSON.parse(el.dataset.props ?? '{}');

createApp(App, props).mount(el);
