<>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
        integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
        integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles <!-- Jika menggunakan Livewire -->
</>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            <!-- Peta Lokasi Pengguna -->
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Peta Lokasi 
                    </h3>
                    <div id="dashboardMap" style="height: 450px; width: 100%; border-radius: 8px;"></div>
                    <div id="locationStatus" class="mt-2 text-sm text-gray-600 dark:text-gray-400">Mencari lokasi
                        Anda...
                    </div>
                    <div style="position: relative;">
                        <div id="dashboardMap" style="height: 50px; border-radius: 8px;"></div>
                        <button id="goToMyLocation"style =" position: absolute; top: 10px; right: 10px; z-index: 1000;"
                            class="px-3 py-2 bg-blue-600 text-white text-sm rounded shadow hover:bg-blue-700">
                            Ke Lokasi Saya
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                console.log('DOM Content Loaded. Initializing live location map...');

                if (typeof L === 'undefined') {
                    console.error('Leaflet (L) is NOT defined. Ensure it is loaded before this script.');
                    const mapErrorDiv = document.getElementById('dashboardMap');
                    if (mapErrorDiv) {
                        mapErrorDiv.innerHTML = '<p style="color:red; text-align:center; padding: 20px;">Error: Komponen peta gagal dimuat (Leaflet tidak ditemukan).</p>';
                    }
                    const statusErrorDiv = document.getElementById('locationStatus');
                    if (statusErrorDiv) {
                        statusErrorDiv.textContent = 'Gagal memuat peta.';
                    }
                    return;
                }
                console.log('Leaflet (L) is defined.');

                const mapElement = document.getElementById('dashboardMap');
                const locationStatusElement = document.getElementById('locationStatus');
                const goToMyLocationBtn = document.getElementById('goToMyLocation');
                let lastKnownLatLng = null;

                if (!mapElement) {
                    console.error('Map container #dashboardMap NOT found in DOM.');
                    if (locationStatusElement) locationStatusElement.textContent = 'Elemen peta tidak ditemukan.';
                    return;
                }
                console.log('Map container #dashboardMap found.');

                const initialLat = -2.548926;
                const initialLng = 118.014863;
                const initialZoom = 5;
                const dashboardMap = L.map('dashboardMap').setView([initialLat, initialLng], initialZoom);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19
                }).addTo(dashboardMap);
                console.log('Tile layer added.');

                let userMarker = null;
                let accuracyCircle = null;
                let firstLocationUpdate = true;

                function updateLocationStatus(message, isError = false) {
                    if (locationStatusElement) {
                        locationStatusElement.textContent = message;
                        locationStatusElement.style.color = isError
                            ? 'red'
                            : (document.body.classList.contains('dark') ? '#cbd5e0' : '#4a5568');
                    }
                }

                if (navigator.geolocation) {
                    updateLocationStatus('Mencoba mendapatkan lokasi Anda...');
                    console.log('Geolocation API is available.');

                    const watchOptions = {
                        enableHighAccuracy: true,
                        timeout: 20000,
                        maximumAge: 0
                    };

                    navigator.geolocation.watchPosition(
                        function (position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            const accuracy = position.coords.accuracy;

                            if (accuracy > 100) {
                                updateLocationStatus("Sinyal anda rendah. Mencoba mendapatkan sinyal yang lebih baik...", true);
                                return;
                            }

                            console.log(`Location update: Lat: ${lat}, Lng: ${lng}, Accuracy: ${accuracy}m`);
                            updateLocationStatus(`Lokasi ditemukan: Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}, Akurasi: ${accuracy.toFixed(0)} meter.`);

                            const userLatLng = L.latLng(lat, lng);
                            lastKnownLatLng = userLatLng; // Simpan posisi terakhir

                            if (!userMarker) {
                                userMarker = L.marker(userLatLng).addTo(dashboardMap)
                                    .bindPopup("Posisi Anda saat ini.")
                                    .openPopup();
                                console.log('User marker created.');
                            } else {
                                userMarker.setLatLng(userLatLng);
                                console.log('User marker updated.');
                            }

                            if (!accuracyCircle) {
                                accuracyCircle = L.circle(userLatLng, {
                                    radius: accuracy,
                                    color: '#007bff',
                                    fillColor: '#007bff',
                                    fillOpacity: 0.15
                                }).addTo(dashboardMap);
                                console.log('Accuracy circle created.');
                            } else {
                                accuracyCircle.setLatLng(userLatLng);
                                accuracyCircle.setRadius(accuracy);
                                console.log('Accuracy circle updated.');
                            }

                            if (firstLocationUpdate) {
                                dashboardMap.setView(userLatLng, 16);
                                firstLocationUpdate = false;
                                console.log('Map view set to user location.');
                            } else {
                                const currentCenter = dashboardMap.getCenter();
                                if (currentCenter.distanceTo(userLatLng) > 50) {
                                    dashboardMap.panTo(userLatLng);
                                    console.log('Map panned to updated user location.');
                                }
                            }
                        },
                        function (error) {
                            let errorMessage = "Tidak dapat mengambil lokasi: ";
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage += "Anda menolak permintaan untuk Geolokasi.";
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage += "Informasi lokasi tidak tersedia (aktifkan GPS/Layanan Lokasi).";
                                    break;
                                case error.TIMEOUT:
                                    errorMessage += "Permintaan untuk mendapatkan lokasi pengguna timeout.";
                                    break;
                                default:
                                    errorMessage += "Terjadi kesalahan yang tidak diketahui.";
                                    break;
                            }
                            console.error('Geolocation error:', errorMessage, error);
                            updateLocationStatus(errorMessage, true);

                            if (!userMarker) {
                                L.marker([initialLat, initialLng]).addTo(dashboardMap)
                                    .bindPopup("Gagal mendapatkan lokasi Anda. Menampilkan lokasi default.")
                                    .openPopup();
                                dashboardMap.setView([initialLat, initialLng], initialZoom);
                            }
                        },
                        watchOptions
                    );
                } else {
                    const noGeoMessage = "Geolocation tidak didukung oleh browser ini.";
                    console.error(noGeoMessage);
                    updateLocationStatus(noGeoMessage, true);
                    alert(noGeoMessage);
                }

                // Tombol "Ke Lokasi Saya"
                if (goToMyLocationBtn) {
                    goToMyLocationBtn.addEventListener('click', function () {
                        if (lastKnownLatLng) {
                            dashboardMap.setView(lastKnownLatLng, 17);
                            console.log('Zoom ke lokasi pengguna.');
                        } else {
                            alert("Lokasi anda belum ditemukan.");
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>