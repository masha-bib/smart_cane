<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> {{-- Biarkan font asli dulu --}}

        {{-- Jika Anda memilih memuat Leaflet CSS & JS secara global via app.blade.php --}}
        {{-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" ... /> --}}
        {{-- <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" ... ></script> --}}


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        {{-- Div luar untuk background ungu dan bintang --}}
        <div class="min-h-screen relative bg-brand-purple-deep text-gray-200"> {{-- Ganti dengan warna ungu gelap Anda --}}
            <!-- Stars Effect -->
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                @for ($i = 0; $i < 70; $i++)
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

            {{-- Konten Asli Breeze/Jetstream sekarang berada di dalam div dengan z-index lebih tinggi --}}
            <div class="relative z-10 min-h-screen bg-transparent"> {{-- bg-transparent agar background ungu terlihat --}}
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    {{-- Buat header semi-transparan agar bintang terlihat --}}
                    <header class="bg-gray-800 bg-opacity-50 shadow backdrop-blur-sm">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }} {{-- $header biasanya berisi teks putih/terang --}}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main>
                    {{ $slot }} {{-- $slot akan berisi konten dari dashboard.blade.php --}}
                </main>
            </div>
        </div>
        @livewireScripts
        @stack('scripts')
    </body>
</html>