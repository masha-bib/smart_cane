{{-- resources/views/chart_app.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Analitik SmartCane') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            {{-- DIAGRAM DISTRIBUSI OBJEK --}}
            <div class="mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1 text-center">
                        Distribusi Deteksi Objek SmartCane
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 text-center">
                        Proporsi jenis objek yang terdeteksi oleh tongkat pintar.
                    </p>
                    <div class="w-full h-80 md:h-96">
                        <canvas id="smartCaneDetectionChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- TEMPAT UNTUK KARTU-KARTU TAMBAHAN NANTINYA --}}
            {{-- Untuk saat ini, kita sederhanakan untuk fokus pada chart utama --}}
            {{-- Contoh satu baris kartu placeholder jika Anda ingin mengembangkannya nanti --}}
            {{--
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Data Card 1</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Isi data...</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Data Card 2</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Isi data...</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Data Card 3</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Isi data...</p>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">Data Card 4</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Isi data...</p>
                </div>
            </div>
            --}}

        </div>
    </div>

       @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () { // BUKA 1 (DOMContentLoaded)
                const isDarkMode = document.documentElement.classList.contains('dark');
                console.log('DOM Loaded. Dark mode:', isDarkMode);

                @if(isset($smartCaneDetectionData)) // IF BLADE
                    console.log('PHP $smartCaneDetectionData IS SET. Preparing chart...');

                    const chartDataFromPHP = {!! json_encode($smartCaneDetectionData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) !!};
                    console.log('Data from PHP (json_encode):', chartDataFromPHP);

                    if (chartDataFromPHP && typeof chartDataFromPHP === 'object' && chartDataFromPHP.labels && chartDataFromPHP.data && chartDataFromPHP.colors) { // BUKA 2 (IF JS #1)
                        const smartCaneCanvas = document.getElementById('smartCaneDetectionChart');

                        if (smartCaneCanvas) { // BUKA 3 (IF JS #2)
                            console.log('Canvas element found.');
                            const context = smartCaneCanvas.getContext('2d');
                            if (context) { // BUKA 4 (IF JS #3)
                                console.log('Canvas context obtained. Initializing new Chart.');
                                new Chart(context, { // BUKA 5 (Objek new Chart)
                                    type: 'doughnut',
                                    data: { // BUKA 6 (Objek data)
                                        labels: chartDataFromPHP.labels,
                                        datasets: [{ // BUKA 7 (Objek dataset)
                                            label: 'Proporsi Deteksi',
                                            data: chartDataFromPHP.data,
                                            backgroundColor: chartDataFromPHP.colors,
                                            borderColor: chartDataFromPHP.colors.map(color => color.replace('0.8', '1')),
                                            borderWidth: 1,
                                            hoverOffset: 8
                                        }] // TUTUP ARRAY datasets
                                    }, // TUTUP 6 (Objek data)
                                    options: { // BUKA 8 (Objek options)
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: { // BUKA 9 (Objek plugins)
                                            legend: { // BUKA 10 (Objek legend)
                                                position: 'bottom',
                                                labels: { // BUKA 11 (Objek labels)
                                                    padding: 20,
                                                    font: { size: 12 }, // TUTUP OBJEK font
                                                    color: isDarkMode ? '#cbd5e1' : '#4b5563'
                                                } // TUTUP 11 (Objek labels)
                                            }, // TUTUP 10 (Objek legend)
                                            tooltip: { // BUKA 12 (Objek tooltip)
                                                callbacks: { // BUKA 13 (Objek callbacks)
                                                    label: function(tooltipItem) { // BUKA 14 (Fungsi label)
                                                        let label = tooltipItem.label || '';
                                                        if (label) label += ': ';
                                                        if (tooltipItem.parsed !== null) label += tooltipItem.parsed;
                                                        return label;
                                                    } // TUTUP 14 (Fungsi label)
                                                }, // TUTUP 13 (Objek callbacks)
                                                bodyColor: isDarkMode ? '#e5e7eb' : '#1f2937',
                                                titleColor: isDarkMode ? '#e5e7eb' : '#1f2937',
                                                backgroundColor: isDarkMode ? 'rgba(31, 41, 55, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                                                borderColor: isDarkMode ? '#4b5563' : '#e5e7eb',
                                                borderWidth: 1
                                            } // TUTUP 12 (Objek tooltip)
                                        }, // TUTUP 9 (Objek plugins)
                                        cutout: '50%'
                                    } // TUTUP 8 (Objek options)
                                }); // TUTUP 5 (new Chart)
                                console.log('Chart initialized.');
                            } else { // ELSE untuk IF JS #3
                                console.error('Failed to get 2D context from canvas.');
                            } // TUTUP 4 (IF JS #3)
                        } else { // ELSE untuk IF JS #2
                            console.error('Canvas element with ID "smartCaneDetectionChart" NOT found.');
                        } // TUTUP 3 (IF JS #2)
                    } else { // ELSE untuk IF JS #1
                        console.warn('chartDataFromPHP is not a valid object or missing properties. Data:', chartDataFromPHP);
                        const canvasContainer = document.getElementById('smartCaneDetectionChart')?.parentElement;
                        if (canvasContainer) { // BUKA 15 (IF JS #4)
                            canvasContainer.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-10">Data chart tidak valid atau properti hilang.</p>';
                        } // TUTUP 15 (IF JS #4)
                    } // TUTUP 2 (IF JS #1)
                @else // ELSE IF BLADE
                    console.warn('PHP $smartCaneDetectionData IS NOT SET. Chart cannot be rendered.');
                    const canvasContainer = document.getElementById('smartCaneDetectionChart')?.parentElement;
                    if (canvasContainer) { // BUKA 16 (IF JS #5)
                        canvasContainer.innerHTML = '<p class="text-center text-gray-500 dark:text-gray-400 py-10">Data distribusi objek tidak tersedia saat ini (controller tidak mengirim data).</p>';
                    } // TUTUP 16 (IF JS #5)
                @endif // ENDIF BLADE
            }); // TUTUP 1 (DOMContentLoaded)
        </script>
    @endpush
</x-app-layout>