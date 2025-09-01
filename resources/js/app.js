import './bootstrap';

import Alpine from 'alpinejs';
import mask from '@alpinejs/mask'; // <-- PLUGIN

window.Alpine = Alpine;

Alpine.plugin(mask); // <-- PLUGIN REGISTRADO NO ALPINE

Alpine.start();