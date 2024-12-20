import './assets/main.css';

import { createApp, markRaw } from 'vue';
import { createPinia } from 'pinia';

import App from './App.vue';
import router from './router';

// Importando Font Awesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faTrash, faEdit, faMapMarkerAlt } from '@fortawesome/free-solid-svg-icons';

// Adicionando ícones à biblioteca
library.add(faTrash, faEdit, faMapMarkerAlt);

const app = createApp(App);
const pinia = createPinia();

pinia.use(({ store }) => {
  store.router = markRaw(router);
});

// Registrando o componente de ícone globalmente
app.component('font-awesome-icon', FontAwesomeIcon);

app.use(pinia);
app.use(router);

app.mount('#app');
