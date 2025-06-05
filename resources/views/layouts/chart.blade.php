{{-- resources/views/layouts/chart.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>IoT Dashboard</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    {{-- Menggunakan font Instrument Sans dari config Anda --}}
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

    {{-- Menggunakan Tailwind CDN (sesuai chart.blade.php Anda). --}}
    {{-- Jika proyek ini menggunakan Vite, ganti dengan @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Heroicons dari chart.blade.php --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/heroicons/2.0.18/24/outline/heroicons.min.css" rel="stylesheet">

    {{-- @livewireStyles (jika digunakan) --}}

    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #374151; }
        ::-webkit-scrollbar-thumb { background: #6b7280; border-radius: 4px; } 
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; } 
    </style>
</head>
{{-- Menggunakan fontFamily dari config Anda: 'Instrument Sans' akan menjadi default sans-serif --}}
<body class="font-sans antialiased">

    {{-- Div luar untuk background ungu dan bintang (SAMA DENGAN app.blade.php) --}}
    <div class="min-h-screen relative bg-brand-purple-deep text-gray-200">
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            @for ($i = 0; $i < 70; $i++)
                {{-- Menggunakan kelas animate-twinkle dari config Anda --}}
                <div class="absolute bg-white rounded-full animate-twinkle" style="
                            left: {{ rand(1, 99) }}%;
                            top: {{ rand(1, 99) }}%;
                            width: {{ rand(1, 2) }}px;
                            height: {{ rand(1, 2) }}px;
                            animation-delay: {{ rand(0, 5000) / 1000 }}s;
                            animation-duration: {{ rand(2000, 5000) / 1000 }}s;
                        "></div>
            @endfor
        </div>

        {{-- Konten utama (struktur dari chart.blade.php awal Anda, dengan z-index dan bg-transparent) --}}
        <div class="relative z-10 flex h-screen overflow-hidden bg-transparent">
            {{-- Jika Anda memiliki sidebar global untuk IoT Dashboard, letakkan di sini --}}
            {{-- Contoh:
            <aside class="w-20 md:w-64 bg-slate-800 bg-opacity-70 text-slate-300 flex-shrink-0 p-4">
                Isi Sidebar IoT
            </aside>
            --}}

            <!-- Main content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                {{-- Jika Anda memiliki header/top bar global untuk IoT Dashboard, letakkan di sini --}}
                {{-- Contoh:
                <header class="bg-slate-700 bg-opacity-50 shadow p-4">
                    <h1 class="text-xl font-semibold text-gray-100">IoT Dashboard Header</h1>
                </header>
                --}}

                <!-- Page content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    {{-- @livewireScripts (jika digunakan) --}}
    @stack('scripts')
</body>
</html>