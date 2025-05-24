<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Pantauan SmartCane</title>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- CSS & JS Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
        integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
        integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Skrip Aplikasi (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Gaya Livewire (jika digunakan) -->
    @livewireStyles

    <style>
        /* CSS untuk Indikator Loading Sederhana */
        .spinner {
            border: 3px solid rgba(0, 0, 0, 0.1);
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border-left-color: #007bff;
            animation: spin 1s ease infinite;
            display: none;
        }
        .dark .spinner { border-left-color: #3b82f6; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Style untuk gambar di popup Leaflet */
        .leaflet-popup-content img { max-width: 100%; height: auto; border-radius: 4px; margin-top: 5px; }

        /* Ukuran font sangat kecil untuk info gambar */
        .text-xxs { font-size: 0.65rem; line-height: 0.85rem; }

        /* Gaya untuk placeholder gambar terbaru */
        #latestImageViewer {
            min-height: 200px; /* Tinggi minimal untuk placeholder */
            background-color: #e5e7eb; /* bg-gray-200 */
            border: 2px dashed #d1d5db; /* border-gray-300 */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #6b7280; /* text-gray-500 */
            cursor: default; /* Default cursor, akan diubah jika ada gambar */
            border-radius: 0.375rem; /* rounded-md */
            overflow: hidden; /* Agar gambar tidak keluar dari border radius */
        }
        .dark #latestImageViewer {
            background-color: #374151; /* dark:bg-gray-700 */
            border-color: #4b5563; /* dark:border-gray-600 */
            color: #9ca3af; /* dark:text-gray-400 */
        }
        #latestImageViewer img {
            max-width: 100%;
            max-height: 100%; /* Sesuaikan dengan min-height #latestImageViewer jika perlu */
            height: auto; /* Jaga rasio aspek */
            width: auto; /* Jaga rasio aspek, biarkan salah satu auto agar tidak terdistorsi */
            object-fit: contain; /* Agar seluruh gambar terlihat tanpa terpotong */
        }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(55, 65, 81, 0.3); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(107, 114, 128, 0.7); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(156, 163, 175, 0.9); }
    </style>
</head>

<body class="font-sans antialiased">
    <x-app-layout>
        <div class="py-8">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Pantauan Lokasi & Gambar SmartCane
                        </h3>

                        <div class="flex flex-col md:flex-row gap-6">
                            {{-- Kolom Kiri: Peta SmartCane --}}
                            <div class="w-full md:w-2/3 lg:w-3/4">
                                <div class="relative">
                                    <div id="smartCaneMap" style="height: 450px; width: 100%; border-radius: 8px; z-index: 1;" class="bg-gray-200 dark:bg-gray-700"></div>
                                    <button id="goToSmartCaneLocation" style="position: absolute; bottom: 10px; left: 10px; z-index: 1000;" class="px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Ke Lokasi Tongkat</button>
                                </div>
                                <div id="smartCaneStatusContainer" class="flex items-center mt-3">
                                    <div id="loadingSpinner" class="spinner mr-2"></div>
                                    <div id="smartCaneStatus" class="text-sm text-gray-600 dark:text-gray-400">Menunggu data SmartCane...</div>
                                </div>
                            </div>

                            {{-- Kolom Kanan: Gambar Terbaru dari SmartCane --}}
                            <div class="w-full md:w-1/3 lg:w-1/4">
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-md flex flex-col" style="height: 450px;">
                                    <h3 class="text-base md:text-lg font-semibold mb-3 border-b border-gray-300 dark:border-gray-600 pb-2 text-gray-900 dark:text-gray-100">
                                        Gambar Terbaru
                                    </h3>
                                    <div id="latestImageViewer" class="flex-grow rounded-md mb-2" title="Klik untuk lihat detail & fokus peta">
                                        <span>Menunggu gambar terbaru...</span>
                                    </div>
                                    <div id="latestImageInfo" class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                        <p id="latestImageTimestamp">Waktu: -</p>
                                        <p id="latestImageCoords" class="truncate">Koordinat: -</p>
                                        <p id="latestImageEvent" class="font-semibold">Event: -</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal untuk Menampilkan Gambar Lebih Besar --}}
        <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 dark:bg-opacity-85 backdrop-blur-sm z-[1000] hidden items-center justify-center p-4" onclick="closeImageModalOutside(event)">
            <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-lg shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" id="imageModalContent" role="dialog" aria-modal="true" aria-labelledby="imageModalTitle">
                <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-300 dark:border-gray-700">
                    <h4 id="imageModalTitle" class="text-xl font-semibold text-gray-900 dark:text-gray-100 truncate">Detail Gambar</h4>
                    <button onclick="closeImageModal()" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white text-3xl leading-none" aria-label="Tutup modal">×</button>
                </div>
                <div class="flex-grow overflow-y-auto custom-scrollbar">
                    <img id="modalImageSrc" src="" alt="Gambar Tercapture Detail" class="w-full h-auto rounded-md object-contain max-h-[calc(80vh-100px)]"> {{-- max-height disesuaikan --}}
                </div>
                <div class="mt-4 pt-3 border-t border-gray-300 dark:border-gray-700 text-right">
                     <button onclick="closeImageModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">Tutup</button>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                let smartCaneMap = null;
                let currentLatestImageData = null; // Menyimpan data {url, name, lat, lng, event} dari gambar terbaru

                // Fungsi global untuk interaksi dari HTML (onclick)
                function focusMapOnLocation(lat, lng) {
                    if (smartCaneMap) {
                        smartCaneMap.setView([lat, lng], 17);
                    } else { console.warn("Peta belum siap untuk difokuskan."); }
                }

                function showImageModal(imageUrl, imageName) {
                    const modal = document.getElementById('imageModal');
                    const modalImage = document.getElementById('modalImageSrc');
                    const modalTitle = document.getElementById('imageModalTitle');
                    if (!modal || !modalImage || !modalTitle) { console.error("Elemen modal tidak ditemukan!"); return; }
                    modalImage.src = imageUrl;
                    modalTitle.textContent = imageName || 'Detail Gambar';
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }

                function closeImageModal() {
                    const modal = document.getElementById('imageModal');
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.style.overflow = 'auto';
                }

                function closeImageModalOutside(event) {
                    if (event.target === document.getElementById('imageModal')) {
                        closeImageModal();
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    // --- Inisialisasi Peta SmartCane ---
                    const mapElement = document.getElementById('smartCaneMap');
                    const statusElement = document.getElementById('smartCaneStatus');
                    const goToBtn = document.getElementById('goToSmartCaneLocation');
                    const spinnerElement = document.getElementById('loadingSpinner');
                    const latestImageViewer = document.getElementById('latestImageViewer');
                    const imgTimestampEl = document.getElementById('latestImageTimestamp');
                    const imgCoordsEl = document.getElementById('latestImageCoords');
                    const imgEventEl = document.getElementById('latestImageEvent');


                    function handleLibraryError(message, mapId, statusId) {
                        console.error(message);
                        const mapDiv = document.getElementById(mapId);
                        const statusDiv = document.getElementById(statusId);
                        if (mapDiv) mapDiv.innerHTML = `<p style="color:red; text-align:center; padding:20px;">Error: Peta gagal dimuat (${message}).</p>`;
                        if (statusDiv) statusDiv.textContent = 'Gagal memuat komponen peta.';
                    }

                    if (typeof L === 'undefined') { handleLibraryError('Leaflet (L) TIDAK terdefinisi.', 'smartCaneMap', 'smartCaneStatus'); return; }
                    if (typeof Swal === 'undefined') { window.Swal = { fire: (options) => alert(options.text || options.title || JSON.stringify(options)) }; }

                    let smartCaneMarker = null;
                    let smartCaneAccuracyCircle = null;
                    let lastKnownSmartCaneLatLng = null;
                    let firstDataUpdate = true;
                    const initialLat = -2.548926; // Indonesia Tengah
                    const initialLng = 118.014863;
                    const initialZoom = 5;
                    const deviceId = 'smartcane_001'; // Ganti dengan ID device Anda
                    const API_ENDPOINT_SMARTCANE = `/api/get-latest-location/${deviceId}`;
                    const FETCH_INTERVAL_MS = 7000; // Interval fetch data

                    function showSpinner() { if (spinnerElement) spinnerElement.style.display = 'block'; }
                    function hideSpinner() { if (spinnerElement) spinnerElement.style.display = 'none'; }

                    function updateStatus(message, isError = false) {
                        if (statusElement) {
                            statusElement.textContent = message;
                            const isDarkMode = document.documentElement.classList.contains('dark');
                            statusElement.style.color = isError ? '#ef4444' : (isDarkMode ? '#cbd5e1' : '#4b5563');
                        }
                    }

                    function formatTimestamp(isoString) {
                        if (!isoString) return 'N/A';
                        try { return new Date(isoString).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' }); }
                        catch (e) { return isoString; }
                    }

                    if (!mapElement) { updateStatus('Error: Elemen peta tidak ditemukan.', true); return; }

                    smartCaneMap = L.map('smartCaneMap').setView([initialLat, initialLng], initialZoom);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors', maxZoom: 19
                    }).addTo(smartCaneMap);

                    async function fetchSmartCaneData() {
                        showSpinner();
                        updateStatus('Mengambil data SmartCane terkini...');
                        try {
                            const response = await fetch(API_ENDPOINT_SMARTCANE);
                            if (!response.ok) {
                                let errorMsg = `HTTP error ${response.status}`;
                                try { const errData = await response.json(); errorMsg += `: ${errData.message || response.statusText}`; } catch (e) { errorMsg += `: ${response.statusText}`; }
                                throw new Error(errorMsg);
                            }
                            const data = await response.json();
                            processSmartCaneData(data);
                        } catch (error) {
                            updateStatus(`Error: ${error.message}`, true);
                        } finally {
                            hideSpinner();
                        }
                    }

                    function processSmartCaneData(data) {
                        if (!data || data.latitude == null || data.longitude == null) { // Periksa null atau undefined
                            updateStatus(data?.message || 'Data lokasi SmartCane tidak valid.');
                            if (latestImageViewer) latestImageViewer.innerHTML = '<span>Gagal memuat gambar atau data lokasi tidak valid.</span>';
                            if (imgTimestampEl) imgTimestampEl.textContent = 'Waktu: -';
                            if (imgCoordsEl) imgCoordsEl.textContent = 'Koordinat: -';
                            if (imgEventEl) imgEventEl.textContent = 'Event: -';
                            currentLatestImageData = null;
                            if (smartCaneMarker) { smartCaneMap.removeLayer(smartCaneMarker); smartCaneMarker = null; }
                            if (smartCaneAccuracyCircle) { smartCaneMap.removeLayer(smartCaneAccuracyCircle); smartCaneAccuracyCircle = null; }
                            lastKnownSmartCaneLatLng = null;
                            return;
                        }

                        const caneLatLng = L.latLng(data.latitude, data.longitude);
                        lastKnownSmartCaneLatLng = caneLatLng;
                        const timestampFormatted = formatTimestamp(data.timestamp);
                        updateStatus(`Lokasi: ${parseFloat(data.latitude).toFixed(5)}, ${parseFloat(data.longitude).toFixed(5)} (Update: ${timestampFormatted})`);

                        // Update Peta
                        let popupContent = `<b>SmartCane ${deviceId}</b><br>Posisi: ${parseFloat(data.latitude).toFixed(5)}, ${parseFloat(data.longitude).toFixed(5)}<br>Akurasi: ${data.accuracy ? data.accuracy.toFixed(0) + 'm' : 'N/A'}<br>Waktu: ${timestampFormatted}`;
                        if (data.objects && data.objects.length) popupContent += `<br>Objek: ${data.objects.join(', ')}`;
                        if (data.voice_alert) popupContent += `<br>Suara: ${data.voice_alert}`;
                        if (data.image_url) popupContent += `<br><img src="${data.image_url}" alt="Gambar SmartCane">`;

                        const accuracyRadius = data.accuracy ? Math.max(parseFloat(data.accuracy), 5) : 30;
                        if (!smartCaneMarker) {
                            smartCaneMarker = L.marker(caneLatLng).addTo(smartCaneMap);
                            smartCaneAccuracyCircle = L.circle(caneLatLng, { radius: accuracyRadius, color: '#10B981', fillColor: '#10B981', fillOpacity: 0.15 }).addTo(smartCaneMap);
                        } else {
                            smartCaneMarker.setLatLng(caneLatLng);
                            smartCaneAccuracyCircle.setLatLng(caneLatLng).setRadius(accuracyRadius);
                        }
                        smartCaneMarker.bindPopup(popupContent).openPopup();
                        if (firstDataUpdate) { smartCaneMap.setView(caneLatLng, 17); firstDataUpdate = false; }

                        // Update Gambar Terbaru di Sidebar
                        currentLatestImageData = {
                            url: data.image_url,
                            name: `Gambar @ ${timestampFormatted} (${data.event_detected || 'Normal'})`,
                            lat: data.latitude,
                            lng: data.longitude,
                            event: data.event_detected || null
                        };

                        if (latestImageViewer) {
                            if (currentLatestImageData.url) {
                                latestImageViewer.innerHTML = `<img src="${currentLatestImageData.url}" alt="Gambar terbaru">`;
                                latestImageViewer.style.cursor = 'pointer';
                                latestImageViewer.onclick = () => {
                                    showImageModal(currentLatestImageData.url, currentLatestImageData.name);
                                    focusMapOnLocation(currentLatestImageData.lat, currentLatestImageData.lng);
                                };
                            } else {
                                latestImageViewer.innerHTML = '<span>Tidak ada gambar terbaru.</span>';
                                latestImageViewer.style.cursor = 'default';
                                latestImageViewer.onclick = null;
                            }
                        }
                        if (imgTimestampEl) imgTimestampEl.textContent = `Waktu: ${timestampFormatted}`;
                        if (imgCoordsEl) imgCoordsEl.textContent = `Koordinat: ${parseFloat(data.latitude).toFixed(5)}, ${parseFloat(data.longitude).toFixed(5)}`;
                        if (imgEventEl) {
                            const eventText = currentLatestImageData.event ? `Event: ${currentLatestImageData.event}` : 'Event: Normal';
                            imgEventEl.textContent = eventText;
                            imgEventEl.className = 'text-xxs font-semibold'; // Reset class
                            if (currentLatestImageData.event === 'Tombol Bantuan') imgEventEl.classList.add('text-red-600', 'dark:text-red-400');
                            else if (currentLatestImageData.event) imgEventEl.classList.add('text-yellow-600', 'dark:text-yellow-400');
                        }
                    } // Akhir processSmartCaneData

                    if (goToBtn) {
                        goToBtn.addEventListener('click', function () {
                            if (lastKnownSmartCaneLatLng) {
                                smartCaneMap.setView(lastKnownSmartCaneLatLng, 17);
                                if (smartCaneMarker) smartCaneMarker.openPopup();
                            } else { Swal.fire({ icon: 'info', title: 'Lokasi Belum Ada', text: 'Data lokasi SmartCane belum diterima.' }); }
                        });
                    }

                    if (API_ENDPOINT_SMARTCANE && API_ENDPOINT_SMARTCANE.includes('/api/')) {
                        fetchSmartCaneData();
                        setInterval(fetchSmartCaneData, FETCH_INTERVAL_MS);
                    } else {
                        updateStatus('Error: API Endpoint tidak valid.', true);
                        Swal.fire({ icon: 'error', title: 'Konfigurasi Error', text: 'API Endpoint SmartCane tidak valid.' });
                    }

                    document.addEventListener('keydown', function(event) {
                        if (event.key === "Escape" && !document.getElementById('imageModal').classList.contains('hidden')) {
                            closeImageModal();
                        }
                    });
                }); // Akhir DOMContentLoaded
            </script>
        @endpush
    </x-app-layout>
</body>
</html>