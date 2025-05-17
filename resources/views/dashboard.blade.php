<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

            <!-- Tambahkan Peta di Sini -->
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Peta Lokasi
                    </h3>
                    <div id="dashboardMap" style="height: 450px; width: 100%; border-radius: 8px;"></div>
                </div>
            </div>

            <!-- (Opsional) Contoh Integrasi dengan Chart.js jika ada -->
            {{-- <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Contoh Chart
                    </h3>
                    <canvas id="myChart"></canvas>
                </div>
            </div> --}}

        </div>
    </div>

    @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM Content Loaded. Trying to initialize map...');

        if (typeof L === 'undefined') {
            console.error('Leaflet (L) is NOT defined. Check JS import and Vite build.');
            // Tampilkan pesan error ke pengguna jika L tidak ada
            document.getElementById('dashboardMap').innerHTML = '<p style="color:red; text-align:center; padding: 20px;">Error: Komponen peta gagal dimuat (Leaflet tidak ditemukan). Periksa konsol untuk detail.</p>';
            return;
        }
        console.log('Leaflet (L) is defined.');

        try {
            const mapElement = document.getElementById('dashboardMap');
            if (!mapElement) {
                console.error('Map container #dashboardMap NOT found in DOM.');
                return;
            }
            console.log('Map container #dashboardMap found.');

            // Pastikan div peta memiliki tinggi
            if (mapElement.clientHeight === 0) {
                console.warn('#dashboardMap has zero height. Make sure CSS for height is applied.');
                // Anda bisa set tinggi di sini juga jika perlu, tapi lebih baik via CSS
                // mapElement.style.height = '450px';
            }


            const initialCoordinates = [-2.548926, 118.0148634]; // Pusat Indonesia
            const initialZoom = 5;
            var dashboardMap = L.map('dashboardMap').setView(initialCoordinates, initialZoom);
            console.log('L.map initialized.');

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(dashboardMap);
            console.log('Tile layer added.');

            // Tambah marker statis untuk tes
            L.marker([-6.200000, 106.816666]).addTo(dashboardMap)
              .bindPopup("<b>Halo Jakarta!</b><br>Ini marker tes.")
              .openPopup();
            console.log('Static marker added.');

        } catch (error) {
            console.error('An error occurred during map initialization:', error);
            document.getElementById('dashboardMap').innerHTML = `<p style="color:red; text-align:center; padding: 20px;">Error: Terjadi masalah saat memuat peta. Detail: ${error.message}</p>`;
        }
    });
</script>
@endpush

    <!-- Pastikan Anda memiliki meta tag CSRF di layout utama Anda (biasanya app.blade.php) -->
    <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->

</x-app-layout>
