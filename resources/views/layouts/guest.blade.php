<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Login</title> {{-- Bisa tambahkan - Login --}}

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        {{-- Gunakan font yang sama dengan welcome.blade.php --}}
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased
                 flex flex-col min-h-screen relative
                 bg-gradient-to-b from-brand-purple-mid-light to-brand-purple-deep text-white">
                 {{-- Kelas ditambahkan: flex flex-col min-h-screen relative bg-gradient-to-b ... text-white --}}
                 {{-- Kelas dihapus/diubah: text-gray-900 dark:text-gray-100 antialiased (text-white lebih dominan) --}}

        <!-- Stars Effect -->
        <div class="absolute inset-0 z-0 pointer-events-none">
            @for ($i = 0; $i < 70; $i++) {{-- Jumlah bintang bisa disesuaikan --}}
                <div class="absolute bg-white rounded-full animate-twinkle" style="
                    left: {{ rand(1, 99) }}%;
                    top: {{ rand(1, 99) }}%;
                    width: {{ rand(1, 2) }}px; /* Ukuran bintang bisa lebih kecil */
                    height: {{ rand(1, 2) }}px;
                    animation-delay: {{ rand(0, 3000) / 1000 }}s;
                    animation-duration: {{ rand(2000, 4000) / 1000 }}s;
                "></div>
            @endfor
        </div>

        {{-- Header Sederhana untuk Halaman Login (Opsional) --}}
        <header class="relative z-20 flex justify-center items-center p-6 sm:p-8 w-full">
            <a href="/" class="flex items-center text-xl font-bold">
                <span class="w-4 h-4 bg-white rounded-full mr-2.5"></span>
                LOGO Smart Cane {{-- Atau nama aplikasi Anda --}}
            </a>
        </header>

        {{-- Konten Utama (Form Login) --}}
        <div class="relative z-10 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 flex-grow">
            {{-- Div ini dihapus dari guest-layout standar:
                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                Kita akan styling formnya langsung, atau membuat card transparan
            --}}

            {{-- Kontainer untuk form agar tidak terlalu lebar dan bisa diberi style --}}
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white bg-opacity-10 dark:bg-black dark:bg-opacity-20 backdrop-blur-md shadow-xl rounded-lg">
                {{ $slot }}
            </div>
        </div>
      
        {{-- Footer Sederhana (Opsional) --}}
        <footer class="relative z-20 py-4 text-center text-xs text-white text-opacity-70 mt-auto">
            © {{ date('Y') }} {{ config('app.name', 'Smart Cane') }}. All rights reserved.
        </footer>
    </body>
</html>