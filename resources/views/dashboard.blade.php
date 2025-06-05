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
        .spinner { border: 3px solid rgba(0, 0, 0, 0.1); width: 18px; height: 18px; border-radius: 50%; border-left-color: #007bff; animation: spin 1s ease infinite; display: none; }
        .dark .spinner { border-left-color: #3b82f6; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .leaflet-popup-content img { max-width: 100%; height: auto; border-radius: 4px; margin-top: 5px; }
        .text-xxs { font-size: 0.65rem; line-height: 0.85rem; }
        #latestImageViewerPython { min-height: 200px; background-color: #e5e7eb; border: 2px dashed #d1d5db; display: flex; align-items: center; justify-content: center; text-align: center; color: #6b7280; cursor: default; border-radius: 0.375rem; overflow: hidden; }
        .dark #latestImageViewerPython { background-color: #374151; border-color: #4b5563; color: #9ca3af; }
        #latestImageViewerPython img { max-width: 100%; max-height: 100%; height: auto; width: auto; object-fit: contain; }
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

                            {{-- Kolom Kanan: Gambar Terbaru dari Python Server --}}
                            <div class="w-full md:w-1/3 lg:w-1/4">
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg shadow-md flex flex-col" style="height: 450px;">
                                    <h3 class="text-base md:text-lg font-semibold mb-3 border-b border-gray-300 dark:border-gray-600 pb-2 text-gray-900 dark:text-gray-100">
                                        Gambar Deteksi Terbaru
                                    </h3>
                                    <div id="latestImageViewerPython" class="flex-grow rounded-md mb-2" title="Klik untuk lihat detail">
                                        {{-- PERUBAHAN BAGIAN INI UNTUK SERVER-SIDE RENDERING --}}
                                        @if(isset($latestDetectedImage) && $latestDetectedImage && !empty($latestDetectedImage['filename']))
                                            <img src="{{ route('serve.detected.image', ['filename' => $latestDetectedImage['filename']]) }}"
                                                 alt="{{ $latestDetectedImage['filename'] }}"
                                                 onclick="showImageModal('{{ route('serve.detected.image', ['filename' => $latestDetectedImage['filename']]) }}', '{{ $latestDetectedImage['filename'] }}')"
                                                 style="cursor: pointer;">
                                        @else
                                            <span>Menunggu gambar deteksi...</span>
                                        @endif
                                        {{-- AKHIR PERUBAHAN --}}
                                    </div>
                                    <div id="latestImageInfoPython" class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                        {{-- PERUBAHAN BAGIAN INI UNTUK SERVER-SIDE RENDERING --}}
                                        <p>Waktu: <span id="latestImageTimestampPython">{{ $latestDetectedImage['timestamp_formatted'] ?? '-' }}</span></p>
                                        <p class="font-semibold">Objek: <span id="latestImageObjectPython">{{ $latestDetectedImage['detected_object'] ?? 'N/A' }}</span></p>
                                        {{-- AKHIR PERUBAHAN --}}
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
                    <img id="modalImageSrc" src="" alt="Gambar Tercapture Detail" class="w-full h-auto rounded-md object-contain max-h-[calc(80vh-100px)]">
                </div>
                <div class="mt-4 pt-3 border-t border-gray-300 dark:border-gray-700 text-right">
                     <button onclick="closeImageModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50">Tutup</button>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                let smartCaneMap = null;
                function focusMapOnLocation(lat, lng) { if(smartCaneMap && lat && lng) smartCaneMap.setView([lat, lng], 18); }
                function showImageModal(imageUrl, imageName) {
                    document.getElementById('modalImageSrc').src = imageUrl;
                    document.getElementById('imageModalTitle').textContent = imageName || 'Detail Gambar';
                    document.getElementById('imageModal').classList.remove('hidden');
                    document.getElementById('imageModal').classList.add('flex');
                }
                function closeImageModal() {
                    document.getElementById('imageModal').classList.add('hidden');
                    document.getElementById('imageModal').classList.remove('flex');
                    document.getElementById('modalImageSrc').src = '';
                }
                function closeImageModalOutside(event) {
                    if (event.target === document.getElementById('imageModal')) {
                        closeImageModal();
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    const mapElement = document.getElementById('smartCaneMap');
                    const statusElement = document.getElementById('smartCaneStatus');
                    const goToBtn = document.getElementById('goToSmartCaneLocation');
                    const spinnerElement = document.getElementById('loadingSpinner');

                    const latestImageViewerPython = document.getElementById('latestImageViewerPython');
                    const imgTimestampElPython = document.getElementById('latestImageTimestampPython');
                    const imgObjectElPython = document.getElementById('latestImageObjectPython');

                    let currentPythonImageFilename = "{{ (isset($latestDetectedImage) && $latestDetectedImage && !empty($latestDetectedImage['filename'])) ? addslashes($latestDetectedImage['filename']) : '' }}";

                    function handleLibraryError(message, mapId, statusId) { if(document.getElementById(statusId)) document.getElementById(statusId).innerHTML = `<span class="text-red-500">${message}</span>`; if(document.getElementById(mapId)) document.getElementById(mapId).innerHTML = `<div class="flex items-center justify-center h-full"><span class="text-red-500">${message}</span></div>`; console.error(message); }
                    if (typeof L === 'undefined') { handleLibraryError('Leaflet (L) TIDAK terdefinisi.', 'smartCaneMap', 'smartCaneStatus'); return; }
                    if (typeof Swal === 'undefined') { window.Swal = { fire: (options) => alert(options.text || options.title || JSON.stringify(options)) }; }

                    let smartCaneMarker = null;
                    let smartCaneAccuracyCircle = null;
                    let lastKnownSmartCaneLatLng = null;
                    let firstDataUpdate = true;
                    const initialLat = -2.548926; const initialLng = 118.014863; const initialZoom = 5; 
                    const deviceId = 'smartcane_001'; 
                    const API_ENDPOINT_SMARTCANE_LOCATION = `/api/get-latest-location/${deviceId}`;
                    const FETCH_LOCATION_INTERVAL_MS = 7000;

                    const API_ENDPOINT_PYTHON_IMAGE = '/api/get-latest-detection-from-db';
                    const FETCH_PYTHON_IMAGE_INTERVAL_MS = 5000; 

                    function showSpinner() { if (spinnerElement) spinnerElement.style.display = 'block'; }
                    function hideSpinner() { if (spinnerElement) spinnerElement.style.display = 'none'; }
                    function updateStatus(message, isError = false) { if (statusElement) { statusElement.textContent = message; statusElement.classList.toggle('text-red-500', isError); statusElement.classList.toggle('dark:text-red-400', isError); } }
                    function formatTimestamp(isoString) { if (!isoString) return 'N/A'; try { const date = new Date(isoString); return date.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' }); } catch (e) { return isoString; } }


                    if (!mapElement) { updateStatus('Error: Elemen peta tidak ditemukan.', true); return; }
                    smartCaneMap = L.map('smartCaneMap').setView([initialLat, initialLng], initialZoom);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors', maxZoom: 19
                    }).addTo(smartCaneMap);

                    async function fetchSmartCaneLocationData() { 
                        showSpinner();
                        updateStatus('Mengambil data lokasi SmartCane...');
                        try {
                            const response = await fetch(API_ENDPOINT_SMARTCANE_LOCATION);
                            if (!response.ok) {
                                updateStatus(`Error lokasi: HTTP ${response.status}`, true);
                                if (response.status === 404) {
                                    updateStatus(`SmartCane dengan ID '${deviceId}' tidak ditemukan atau belum ada data.`, true);
                                }
                                throw new Error(`HTTP error ${response.status}`);
                            }
                            const data = await response.json();
                            processSmartCaneLocationData(data);
                        } catch (error) {
                            console.error('Error fetchSmartCaneLocationData:', error);
                            updateStatus(`Error mengambil data lokasi: ${error.message}`, true);
                        } finally {
                            hideSpinner();
                        }
                    }

                    function processSmartCaneLocationData(data) { 
                        if (!data || data.latitude == null || data.longitude == null) {
                            updateStatus(data?.message || 'Data lokasi SmartCane tidak valid atau tidak ditemukan.');
                            if (smartCaneMarker) { smartCaneMap.removeLayer(smartCaneMarker); smartCaneMarker = null; }
                            if (smartCaneAccuracyCircle) { smartCaneMap.removeLayer(smartCaneAccuracyCircle); smartCaneAccuracyCircle = null; }
                            lastKnownSmartCaneLatLng = null;
                            return;
                        }
                        const caneLatLng = L.latLng(data.latitude, data.longitude);
                        lastKnownSmartCaneLatLng = caneLatLng;
                        const timestampFormatted = formatTimestamp(data.timestamp);
                        updateStatus(`Lokasi: ${parseFloat(data.latitude).toFixed(5)}, ${parseFloat(data.longitude).toFixed(5)} (Update: ${timestampFormatted})`);
                        let popupContent = `<b>SmartCane ${deviceId}</b><br>Posisi: ${parseFloat(data.latitude).toFixed(5)}, ${parseFloat(data.longitude).toFixed(5)}<br>Akurasi: ${data.accuracy ? data.accuracy.toFixed(0) + 'm' : 'N/A'}<br>Waktu: ${timestampFormatted}`;
                        if (data.objects && data.objects.length) popupContent += `<br>Objek (di Lokasi): ${data.objects.join(', ')}`;
                        if (data.voice_alert) popupContent += `<br>Suara: ${data.voice_alert}`;
                        if (data.image_url) popupContent += `<br><img src="${data.image_url}" alt="Gambar di Lokasi SmartCane">`; 
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
                    }

                    async function fetchAndUpdatePythonImage() {
                        try {
                            const response = await fetch(API_ENDPOINT_PYTHON_IMAGE);
                            if (!response.ok) {
                                if (response.status === 404 && latestImageViewerPython) {
                                    if (currentPythonImageFilename || !latestImageViewerPython.textContent.includes('Menunggu gambar deteksi...')) {
                                        latestImageViewerPython.innerHTML = '<span>Tidak ada gambar deteksi baru.</span>';
                                        if (imgTimestampElPython) imgTimestampElPython.textContent = '-';
                                        if (imgObjectElPython) imgObjectElPython.textContent = 'N/A';
                                        currentPythonImageFilename = ''; 
                                    }
                                } else {
                                    console.warn('Polling gambar Python: Gagal mengambil info gambar, status:', response.status, await response.text());
                                }
                                return;
                            }
                            const result = await response.json();

                            if (result && result.data && result.data.url && result.data.filename) {
                                const newImageData = result.data;
                                if (newImageData.filename !== currentPythonImageFilename || !currentPythonImageFilename) {
                                    console.log('New Python image detected via API:', newImageData.filename);
                                    currentPythonImageFilename = newImageData.filename;

                                    if (latestImageViewerPython) {
                                        latestImageViewerPython.innerHTML =
                                            `<img src="${newImageData.url}" alt="${newImageData.filename}"
                                                 onclick="showImageModal('${newImageData.url}', '${newImageData.filename}')"
                                                 style="cursor: pointer;">`;
                                    }
                                    if (imgTimestampElPython) imgTimestampElPython.textContent = newImageData.timestamp || '-';
                                    if (imgObjectElPython) imgObjectElPython.textContent = newImageData.detected_object || 'N/A';
                                }
                            } else if (latestImageViewerPython && !currentPythonImageFilename) {
                                latestImageViewerPython.innerHTML = '<span>Data gambar tidak lengkap dari API.</span>';
                                Log.warn('API merespon OK, tapi data gambar tidak lengkap:', result);
                            }
                        } catch (error) {
                            console.error('Error polling gambar Python:', error);
                            if (latestImageViewerPython) {
                                latestImageViewerPython.innerHTML = '<span>Error mengambil gambar. Cek konsol.</span>';
                            }
                        }
                    }


                    if (goToBtn) { goToBtn.addEventListener('click', () => { if (lastKnownSmartCaneLatLng) { focusMapOnLocation(lastKnownSmartCaneLatLng.lat, lastKnownSmartCaneLatLng.lng); } else { Swal.fire('Info', 'Belum ada data lokasi SmartCane diterima.', 'info'); } }); }

                    if (typeof API_ENDPOINT_SMARTCANE_LOCATION !== 'undefined' && API_ENDPOINT_SMARTCANE_LOCATION && API_ENDPOINT_SMARTCANE_LOCATION.includes('/api/')) {
                        fetchSmartCaneLocationData(); 
                        setInterval(fetchSmartCaneLocationData, FETCH_LOCATION_INTERVAL_MS);
                    } else {
                        updateStatus('API endpoint lokasi tidak valid atau tidak terdefinisi.', true);
                        console.warn("API_ENDPOINT_SMARTCANE_LOCATION tidak valid atau tidak terdefinisi. Polling lokasi tidak akan berjalan.");
                    }

                    if (API_ENDPOINT_PYTHON_IMAGE && API_ENDPOINT_PYTHON_IMAGE.includes('/api/')) {
                        fetchAndUpdatePythonImage(); 
                        setInterval(fetchAndUpdatePythonImage, FETCH_PYTHON_IMAGE_INTERVAL_MS);
                    } else {
                        console.warn("API_ENDPOINT_PYTHON_IMAGE tidak valid atau tidak terdefinisi. Polling gambar Python tidak akan berjalan.");
                        if (latestImageViewerPython) latestImageViewerPython.innerHTML = '<span>Polling gambar tidak aktif.</span>';
                    }

                    document.addEventListener('keydown', function(event) { if (event.key === "Escape") { closeImageModal(); } });
                }); 
            </script>
        @endpush
    </x-app-layout>
</body>
</html>