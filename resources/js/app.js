import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse'; // THE FIX: Import the collapse plugin

// THE FIX: Register the plugin with Alpine
Alpine.plugin(collapse);

window.Alpine = Alpine;

Alpine.start();