<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome To The Smart Cane</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
{{-- Modifikasi kelas body:
- Hapus bg-brand-purple-page-bg (karena akan diganti gradien)
- Tambahkan kelas gradien: bg-gradient-to-b from-NAMAWarnaTerang to-NAMAWarnaGelap
--}}

<body class="font-sans text-white flex flex-col min-h-screen relative
             bg-gradient-to-b from-brand-purple-mid-light to-brand-purple-deep">

    <div class="absolute inset-0 z-0 pointer-events-none">
        @for ($i = 0; $i < 100; $i++) {{-- Jumlah bintang --}}
            <div class="absolute bg-white rounded-full animate-twinkle" style="
                                    left: {{ rand(1, 99) }}%;
                                    top: {{ rand(1, 99) }}%; /* Bintang sekarang bisa di seluruh halaman */
                                    width: {{ rand(1, 3) }}px;
                                    height: {{ rand(1, 3) }}px;
                                    animation-delay: {{ rand(0, 3000) / 1000 }}s;
                                    animation-duration: {{ rand(2000, 4000) / 1000 }}s;
                                "></div>
        @endfor
    </div>

    <header
        class="relative z-20 flex flex-col md:flex-row justify-between items-center p-6 sm:p-8 w-full max-w-7xl mx-auto">
        <span
            class="self-center text-2xl font-semibold whitespace-nowrap text-gray-100 dark:text-white hover:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
            Smart<span class="text-indigo-400 dark:text-indigo-300">Cane</span>
        </span>

        {{-- Navigasi Login/Register --}}
        @if (Route::has('login'))
            <nav class="flex items-center gap-3 sm:gap-4 text-sm">
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="inline-block px-4 py-2 bg-white bg-opacity-90 hover:bg-opacity-100 text-brand-purple-deep rounded-full font-semibold transition-all">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-block px-4 py-2 bg-white bg-opacity-90 hover:bg-opacity-100 text-brand-purple-deep rounded-full font-semibold transition-all">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-block px-4 py-2 bg-white bg-opacity-90 hover:bg-opacity-100 text-brand-purple-deep rounded-full font-semibold transition-all">
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <main class="relative z-20 text-center py-12 sm:py-16 md:py-20 px-4 flex-grow w-full max-w-5xl mx-auto">
        {{-- text-center di sini akan membantu memusatkan div anak jika div anak adalah inline-block --}}

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold mb-6 sm:mb-8">
            Selamat datang di
            <span class="whitespace-nowrap"> {{-- Mencegah pecah baris antara Smart dan Cane --}}
                Smart<span class="text-indigo-400 dark:text-indigo-300">Cane</span>
            </span>
        </h1>

        <p class="text-lg sm:text-xl max-w-2xl mx-auto text-white text-opacity-90 leading-relaxed mb-8 sm:mb-10">
            Platform pemantauan Smart Cane: inovasi teknologi IoT untuk membantu penyandang tunanetra beraktivitas
            dengan lebih aman, mandiri, dan percaya diri.
        </p>

        {{-- Unordered List untuk fitur --}}
        {{-- Hapus mx-auto, tambahkan inline-block --}}
        <div class="max-w-xl inline-block">
            {{-- Buat h2 rata kiri di dalam kontainer ini --}}
            <h2 class="text-xl sm:text-2xl font-semibold text-white mb-4 text-left pl-11">
                Melalui dashboard ini, Anda dapat:
            </h2>
            {{-- ul juga akan rata kiri, dengan list-outside dan padding kiri untuk indentasi --}}
            <ul class="space-y-3 text-base sm:text-lg text-white text-opacity-80
                   list-disc list-outside
                   text-left
                   pl-12"> {{-- Sesuaikan pl-5 ini agar teks item list sejajar dengan teks h2 --}}
                <li>Memantau data real-time dari tongkat pintar</li>
                <li>Melihat lokasi pengguna secara langsung</li>
                <li>Menerima notifikasi saat terjadi kondisi darurat</li>
                <li>Mengakses riwayat pergerakan dan aktivitas pengguna</li>
            </ul>
        </div>
    </main>

    {{-- Warna lanskap mungkin perlu disesuaikan agar kontras dengan gradien baru jika warnanya sama --}}
    <div class="relative bottom-0 left-0 w-full h-[40%] sm:h-[50%] md:h-[55%] z-10 pointer-events-none mt-auto">
        <div class="absolute bottom-0 left-0 w-full h-full bg-brand-purple-mountain-far z-[1]"
            style="border-top-left-radius: 50% 20%; border-top-right-radius: 60% 30%;">
        </div>
        <div class="absolute bottom-0 left-0 w-[110%] h-[80%] bg-brand-purple-mountain-mid z-[2]"
            style="border-top-left-radius: 40% 30%; border-top-right-radius: 55% 25%; transform: translateX(-5%);">
        </div>
        <div class="absolute bottom-0 left-0 w-full h-[60%] bg-brand-purple-forest-front z-[3]"
            style="border-top-left-radius: 30px 60px; border-top-right-radius: 40px 50px;">
        </div>
        <div class="absolute bottom-0 left-0 w-[105%] h-[50%] bg-brand-purple-trees-detail z-[4]"
            style="border-top-left-radius: 20px 40px; border-top-right-radius: 25px 35px; transform: scaleX(1.02);">
        </div>
    </div>

</body>

</html>