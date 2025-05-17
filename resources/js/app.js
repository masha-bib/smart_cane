import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
window.L = L;

Alpine.start();
import L from 'leaflet';
window.L = L;

import Swal from 'sweetalert2'; // Pastikan Anda sudah npm install sweetalert2
window.Swal = Swal;