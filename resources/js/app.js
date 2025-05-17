import './bootstrap';
import '../css/app.css'; 

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
window.L = L;

import Swal from 'sweetalert2'; // Pastikan Anda sudah npm install sweetalert2
window.Swal = Swal;