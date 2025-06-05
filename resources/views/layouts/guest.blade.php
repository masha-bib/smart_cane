<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart Cane') }} - {{ ucfirst(Route::currentRouteName()) }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased
             flex flex-col min-h-screen relative
             bg-gradient-to-b from-brand-purple-mid-light to-brand-purple-deep text-white">

    <div class="absolute inset-0 z-0 pointer-events-none">
        @for ($i = 0; $i < 70; $i++)
            <div class="absolute bg-white rounded-full animate-twinkle" style="
                        left: {{ rand(1, 99) }}%;
                        top: {{ rand(1, 99) }}%;
                        width: {{ rand(1, 2) }}px;
                        height: {{ rand(1, 2) }}px;
                        animation-delay: {{ rand(0, 3000) / 1000 }}s;
                        animation-duration: {{ rand(2000, 4000) / 1000 }}s;
                    "></div>
        @endfor
    </div>

    {{-- Header untuk Logo --}}
    {{-- Padding atas dikurangi sedikit, padding bawah dikurangi signifikan --}}
    <header class="relative z-20 flex justify-center items-center pt-8 pb-2 sm:pt-12 sm:pb-4 w-full">
        <a href="{{ url('/') }}" class="flex items-center">
            {{-- Ukuran logo tetap text-4xl sm:text-5xl, bisa disesuaikan jika masih terlalu besar --}}
            <span
                class="self-center text-4xl sm:text-5xl font-semibold whitespace-nowrap text-white hover:text-indigo-300 transition-colors">
                Smart<span class="text-indigo-400 dark:text-indigo-300">Cane</span>
            </span>
        </a>
    </header>

    {{-- Konten Utama (Form Login/Register) --}}
    {{-- justify-start agar form lebih dekat ke header, tambahkan sedikit padding atas di sini --}}
    {{-- flex-grow agar mengisi sisa ruang dan mendorong footer ke bawah --}}
    <div class="relative z-10 flex flex-col items-center justify-start flex-grow w-full px-4 pt-4 sm:pt-6">
        {{-- Card form --}}
        <div
            class="w-full sm:max-w-md px-6 py-8 bg-white bg-opacity-10 dark:bg-black dark:bg-opacity-20 backdrop-blur-md shadow-xl rounded-lg">
            {{-- Tidak ada margin atas di sini --}}
            {{ $slot }}
        </div>
    </div>

    {{-- Footer --}}
    <footer class="relative z-20 py-6 text-center text-xs text-white text-opacity-70">
        © {{ date('Y') }} {{ config('app.name', 'Smart Cane') }}. All rights reserved.
    </footer>
</body>

</html>