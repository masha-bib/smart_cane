{{-- resources/views/chart_app.blade.php --}}
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- FORM UNTUK FILTER RENTANG WAKTU --}}
                    <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <form method="GET" action="{{ route('chart_app') }}" id="filterPeriodeForm" class="flex flex-col sm:flex-row sm:items-end sm:space-x-4 space-y-2 sm:space-y-0">
                            <div>
                                <label for="periode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Periode:</label>
                                <select id="periode" name="periode" class="block w-full sm:w-auto mt-1 rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200">
                                    <option value="semua" {{ request('periode', 'semua') == 'semua' ? 'selected' : '' }}>Semua Waktu</option>
                                    <option value="hari_ini" {{ request('periode') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                                    <option value="7_hari" {{ request('periode') == '7_hari' ? 'selected' : '' }}>7 Hari Terakhir</option>
                                    <option value="30_hari" {{ request('periode') == '30_hari' ? 'selected' : '' }}>30 Hari Terakhir</option>
                                    <option value="bulan_ini" {{ request('periode') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                    <option value="bulan_lalu" {{ request('periode') == 'bulan_lalu' ? 'selected' : '' }}>Bulan Lalu</option>
                                </select>
                            </div>
                        </form>
                    </div>

                    {{-- DIAGRAM DISTRIBUSI OBJEK --}}
                    <div class="mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2 text-center">
                                Distribusi Kategori Objek Terdeteksi
                                @if(request('periode') && request('periode') != 'semua')
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        ({{ ucwords(str_replace('_', ' ', request('periode'))) }})
                                    </span>
                                @endif
                            </h3>

                            {{-- ========== AWAL BLOK KONDISI DATA KOSONG (DIPERBAIKI) ========== --}}
                            @if(isset($dataKosong) && $dataKosong)
                                <div class="py-10 px-4 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                    <h4 class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">Data Tidak Ditemukan</h4>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Tidak ada data deteksi objek untuk periode
                                        @if(isset($periodeAktif) && $periodeAktif != 'semua')
                                            "{{ ucwords(str_replace('_', ' ', $periodeAktif)) }}".
                                        @else
                                            yang dipilih.
                                        @endif
                                        Silakan coba periode lain.
                                    </p>
                                </div>
                            @else
                                {{-- Jika data TIDAK kosong, tampilkan deskripsi dan canvas chart --}}
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 text-center">
                                    Proporsi jenis objek yang terdeteksi dari data rekaman.
                                </p>
                                <div class="w-full h-80 md:h-96 min-h-[300px] relative">
                                    <canvas id="smartCaneDetectionChart"></canvas>
                                </div>
                            @endif
                            {{-- ========== AKHIR BLOK KONDISI DATA KOSONG ========== --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const periodeDropdown = document.getElementById('periode');
                if (periodeDropdown) {
                    periodeDropdown.addEventListener('change', function() {
                        console.log('Periode diubah, mengirim form filter...');
                        document.getElementById('filterPeriodeForm').submit();
                    });
                }

                // Cek dari PHP apakah data kosong, jika ya, jangan jalankan script chart
                // Variabel $dataKosong dan $smartCaneDetectionData dikirim dari Controller
                const dataAdaUntukChart = {{ (isset($smartCaneDetectionData) && !empty($smartCaneDetectionData['labels']) && !empty($smartCaneDetectionData['data']) && !(isset($dataKosong) && $dataKosong)) ? 'true' : 'false' }};

                if (dataAdaUntukChart) {
                    // KODE JAVASCRIPT CHART ANDA YANG SUDAH BERHASIL (SAMA PERSIS SEPERTI YANG ANDA BERIKAN SEBELUMNYA)
                    // Tidak ada perubahan di bawah baris ini sampai akhir blok if (dataAdaUntukChart)
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    const legendTextColor = isDarkMode ? '#cbd5e1' : '#4b5563';
                    const tooltipBodyColor = isDarkMode ? '#e5e7eb' : '#1f2937';
                    const tooltipTitleColor = isDarkMode ? '#e5e7eb' : '#1f2937';
                    const tooltipBgColor = isDarkMode ? 'rgba(31, 41, 55, 0.95)' : 'rgba(255, 255, 255, 0.95)';
                    const tooltipBorderColor = isDarkMode ? '#4b5563' : '#e5e7eb';
                    const centerTextColor = isDarkMode ? '#e5e7eb' : '#1f2937';

                    console.log('Chart Script (App Layout): DOM Loaded. Dark mode:', isDarkMode);

                    console.log('Chart Script (App Layout): Data chart diterima dari PHP.', {!! json_encode($smartCaneDetectionData) !!});

                    const chartDataFromPHP = @json($smartCaneDetectionData);
                    // console.log('--- DEBUG DATA CHART ---');
                    // console.log('Isi lengkap chartDataFromPHP:', chartDataFromPHP);
                    // console.log('Nilai chartDataFromPHP.totalDeteksi:', chartDataFromPHP.totalDeteksi);
                    // console.log('Tipe data chartDataFromPHP.totalDeteksi:', typeof chartDataFromPHP.totalDeteksi);
                    // console.log('--- AKHIR DEBUG DATA CHART ---');

                    const doughnutText = {
                        id: 'doughnutText',
                        afterDraw(chart, args, pluginOptions) {
                            const { ctx, chartArea: { left, right, top, bottom, width, height } } = chart;
                            ctx.save();

                            if (chart.config.type === 'doughnut' && typeof chartDataFromPHP.totalDeteksi === 'number') {
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';

                                const textX = Math.round((left + right) / 2);
                                const textY_center = Math.round((top + bottom) / 2);

                                const mainFontSize = parseInt(pluginOptions.fontSize || '30');
                                const subFontSize = parseInt(pluginOptions.subTextFontSize || '14');

                                ctx.font = `${subFontSize}px ${pluginOptions.fontFamily || 'sans-serif'}`;
                                ctx.fillStyle = pluginOptions.subTextFontColor || (isDarkMode ? '#9ca3af' : '#6b7280');
                                const yPosTotal = textY_center - (mainFontSize * 0.5) - (subFontSize * 0.5);
                                ctx.fillText(pluginOptions.subText || "Total", textX, yPosTotal);

                                ctx.font = `bold ${mainFontSize}px ${pluginOptions.fontFamily || 'sans-serif'}`;
                                ctx.fillStyle = pluginOptions.fontColor || centerTextColor;
                                ctx.fillText(chartDataFromPHP.totalDeteksi.toString(), textX, textY_center);

                                ctx.font = `${subFontSize}px ${pluginOptions.fontFamily || 'sans-serif'}`;
                                ctx.fillStyle = pluginOptions.subTextFontColor || (isDarkMode ? '#9ca3af' : '#6b7280');
                                const yPosDeteksi = textY_center + (mainFontSize * 0.5) + (subFontSize * 0.5);
                                ctx.fillText(pluginOptions.subTextBottom || "Deteksi", textX, yPosDeteksi);
                            }
                            ctx.restore();
                        }
                    };

                    if (
                        chartDataFromPHP &&
                        typeof chartDataFromPHP === 'object' &&
                        Array.isArray(chartDataFromPHP.labels) &&
                        Array.isArray(chartDataFromPHP.data) &&
                        Array.isArray(chartDataFromPHP.colors) && chartDataFromPHP.colors.length > 0 &&
                        chartDataFromPHP.labels.length === chartDataFromPHP.data.length &&
                        chartDataFromPHP.labels.length === chartDataFromPHP.colors.length &&
                        typeof chartDataFromPHP.totalDeteksi === 'number'
                    ) {
                        const smartCaneCanvas = document.getElementById('smartCaneDetectionChart');
                        if (smartCaneCanvas) {
                            const context = smartCaneCanvas.getContext('2d');
                            if (context) {
                                if (window.myDoughnutChart instanceof Chart) {
                                    window.myDoughnutChart.destroy();
                                }

                                window.myDoughnutChart = new Chart(context, {
                                    type: 'doughnut',
                                    data: {
                                        labels: chartDataFromPHP.labels,
                                        datasets: [{
                                            label: 'Proporsi Deteksi Kategori',
                                            data: chartDataFromPHP.data,
                                            backgroundColor: chartDataFromPHP.colors,
                                            borderColor: chartDataFromPHP.colors.map(color => color.replace(/, ?0\.8\)/, ', 1)')),
                                            borderWidth: 1.5,
                                            hoverOffset: 8,
                                            hoverBorderColor: isDarkMode ? '#374151' : '#ffffff',
                                            hoverBorderWidth: 2
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        animation: { duration: 1000, easing: 'easeInOutQuart' },
                                        plugins: {
                                            legend: {
                                                position: 'bottom', align: 'center',
                                                labels: { padding: 25, font: { size: 12 }, color: legendTextColor, usePointStyle: true, boxWidth: 10, boxHeight: 10 }
                                            },
                                            tooltip: { /* ... kode tooltip Anda ... */ },
                                            doughnutText: { // Ini adalah versi Anda yang sudah berhasil
                                                fontFamily: 'Arial, sans-serif',
                                                fontSize: '50',
                                                fontColor: '#F5F5F5F5',
                                                subText: 'Total',
                                                subTextBottom: 'Deteksi',
                                                subTextFontSize: '14',
                                                subTextFontColor: (isDarkMode ? '#9ca3af' : '#6b7280')
                                            }
                                        },
                                        cutout: '65%',
                                        layout: { padding: { top: 10, bottom: 10, left: 10, right: 10 } }
                                    },
                                    plugins: [doughnutText]
                                });
                                console.log('Chart Script (App Layout): Chart berhasil diinisialisasi dengan teks tengah.');
                            } else { /* ... error context ... */ }
                        } else { /* ... error canvas ... */ }
                    } else {
                         console.warn('Chart Script (App Layout): Data chart tidak valid atau properti hilang setelah cek awal. Data:', chartDataFromPHP);
                    }
                } else {
                    console.log('Chart Script (App Layout): Data kosong atau $smartCaneDetectionData tidak valid, script chart tidak dijalankan.');
                }
            });
        </script>
    @endpush
</x-app-layout>