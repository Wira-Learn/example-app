<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'BrokerIoT') }} - Broker MQTT Private Instan untuk Developer Indonesia</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#DC2626',
                            '50': '#FEF2F2',
                            '100': '#FEE2E2',
                            '200': '#FECACA',
                            '300': '#FCA5A5',
                            '400': '#F87171',
                            '500': '#EF4444',
                            '600': '#DC2626',
                            '700': '#B91C1C',
                            '800': '#991B1B',
                            '900': '#7F1D1D',
                            '950': '#450A0A',
                        },
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50">

    <div class="min-h-screen flex flex-col">

        <header class="bg-white/80 backdrop-blur-sm shadow-sm sticky top-0 z-50">
            <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">

                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('welcome') }}"
                            class="text-2xl font-bold text-primary-700">{{ config('app.name', 'BrokerIoT') }}</a>
                    </div>

                    <div class="hidden md:flex md:space-x-8">
                        <a href="#fitur"
                            class="font-medium text-gray-500 hover:text-gray-900 transition duration-150 ease-in-out">Fitur</a>
                        <a href="#harga"
                            class="font-medium text-gray-500 hover:text-gray-900 transition duration-150 ease-in-out">Harga</a>
                        <a href="#cara-kerja"
                            class="font-medium text-gray-500 hover:text-gray-900 transition duration-150 ease-in-out">Cara
                            Kerja</a>
                    </div>

                    <div class="hidden md:flex items-center space-x-4">
                        @if (Route::has('login'))
                            <a href="{{ route('dashboard') }}"
                                class="px-5 py-2 rounded-md text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-sm transition duration-150 ease-in-out">
                                Ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login.show') }}"
                                class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 transition duration-150 ease-in-out">
                                Login
                            </a>
                            @if (Route::has('register.show'))
                                <a href="{{ route('register.show') }}"
                                    class="inline-flex items-center px-5 py-2 rounded-md text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-sm transition duration-150 ease-in-out">
                                    Daftar Gratis
                                </a>
                            @endif
                        @endif
                    </div>

                    <div class="flex items-center md:hidden">
                        <button type="button" id="hamburger-btn"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
                            aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Buka menu</span>
                            <svg id="hamburger-icon" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                            <svg id="close-icon" class="h-6 w-6 hidden" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                </div>
            </nav>

            <div class="hidden md:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-200">
                    <a href="#fitur"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Fitur</a>
                    <a href="#harga"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Harga</a>
                    <a href="#cara-kerja"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50">Cara
                        Kerja</a>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="px-2 space-y-3">
                        @if (Route::has('login'))
                            <a href="{{ route('dashboard') }}"
                                class="block w-full text-left px-4 py-2 rounded-md text-base font-medium text-white bg-primary-600 hover:bg-primary-700">
                                Ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('register.show') }}"
                                class="block w-full text-left px-4 py-2 rounded-md text-base font-medium text-white bg-primary-600 hover:bg-primary-700">
                                Daftar Gratis
                            </a>
                            <a href="{{ route('login.show') }}"
                                class="block w-full text-left px-4 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                                Login
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </header>


        <main class="flex-grow">
            <section class="container mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 sm:pt-24 sm:pb-32 text-center">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                    <span class="block">Broker MQTT Private Instan</span>
                    <span class="block text-primary-600">untuk Proyek IoT Anda.</span>
                </h1>

                <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-600">
                    Fokus pada pengembangan device, bukan mengurus server. Dapatkan broker MQTT yang
                    <strong>aman, private, dan instan</strong> dalam 30 detik gratis untuk
                    mahasiswa dan developer.
                </p>

                <div class="mt-10">
                    @if (Route::has('register.show'))
                        <a href="{{ Route::has('login') ? (Auth::check() ? route('dashboard') : route('register.show')) : Route('login.show') }}"
                            class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 text-lg transition duration-150 ease-in-out">
                            {{ Auth::check() ? 'Buka Dashboard' : 'Mulai Gratis Sekarang' }}
                        </a>
                        <p class="mt-4 text-sm text-gray-500">
                            {{ Auth::check() ? 'Selamat datang kembali!' : 'Tanpa perlu kartu kredit.' }}
                        </p>
                    @endif
                </div>
            </section>

            <section id="fitur" class="py-20 bg-white border-t">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <h2 class="text-3xl font-extrabold text-gray-900">
                            Dibuat untuk Developer, Hobiis, dan Mahasiswa
                        </h2>
                        <p class="mt-4 text-lg text-gray-600">
                            Kami memecahkan masalah yang sering Anda hadapi saat mengerjakan proyek IoT.
                        </p>
                    </div>

                    <div class="mt-16 grid md:grid-cols-3 gap-10">
                        <div class="text-center">
                            <div
                                class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="mt-5 text-lg leading-6 font-medium text-gray-900">Penyediaan Instan</h3>
                            <p class="mt-2 text-base text-gray-600">
                                Lupakan instalasi VPS yang rumit. Dapatkan Host, Port, Username, dan Password Anda
                                dalam 30 detik setelah mendaftar.
                            </p>
                        </div>

                        <div class="text-center">
                            <div
                                class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="mt-5 text-lg leading-6 font-medium text-gray-900">Aman & Private</h3>
                            <p class="mt-2 text-base text-gray-600">
                                Berhenti menggunakan broker publik gratis dimana data Anda bisa dilihat semua orang.
                                Setiap pengguna terisolasi dengan ACL yang ketat.
                            </p>
                        </div>

                        <div class="text-center">
                            <div
                                class="flex items-center justify-center h-12 w-12 rounded-md bg-primary-500 text-white mx-auto">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <h3 class="mt-5 text-lg leading-6 font-medium text-gray-900">Edukasi Lokal</h3>
                            <p class="mt-2 text-base text-gray-600">
                                Senjata utama kami. Kami menyediakan tutorial lengkap Bahasa Indonesia untuk
                                ESP32, Node-RED, dan Notifikasi Telegram.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="cara-kerja" class="py-20 bg-gray-50">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-extrabold text-gray-900">
                            Mulai dalam Hitungan Detik
                        </h2>
                        <p class="mt-4 text-lg text-gray-600">
                            Tidak perlu konfigurasi server yang memusingkan.
                        </p>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="flex flex-col items-center text-center">
                            <div
                                class="flex items-center justify-center h-16 w-16 rounded-full bg-primary-600 text-white font-bold text-2xl">
                                1</div>
                            <h3 class="mt-5 text-xl font-medium text-gray-900">Daftar Akun</h3>
                            <p class="mt-2 text-base text-gray-600">Buat akun gratis Anda, tanpa perlu kartu
                                kredit.
                            </p>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div
                                class="flex items-center justify-center h-16 w-16 rounded-full bg-primary-600 text-white font-bold text-2xl">
                                2</div>
                            <h3 class="mt-5 text-xl font-medium text-gray-900">Buat Device</h3>
                            <p class="mt-2 text-base text-gray-600">Tekan tombol "Buat Device Baru" di dashboard
                                Anda.</p>
                        </div>
                        <div class="flex flex-col items-center text-center">
                            <div
                                class="flex items-center justify-center h-16 w-16 rounded-full bg-primary-600 text-white font-bold text-2xl">
                                3</div>
                            <h3 class="mt-5 text-xl font-medium text-gray-900">Hubungkan Device</h3>
                            <p class="mt-2 text-base text-gray-600">Gunakan kredensial yang baru Anda dapatkan
                                (host, port, user, pass) di ESP32 atau device Anda.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="harga" class="py-20 bg-white border-t">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-extrabold text-gray-900">
                            Harga Sederhana untuk Semua Kebutuhan
                        </h2>
                        <p class="mt-4 text-lg text-gray-600">
                            Mulai gratis, upgrade saat proyekmu berkembang. Model Freemium.
                        </p>
                    </div>

                    <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">

                        <div class="border rounded-xl shadow-lg p-8 flex flex-col bg-white">
                            <h3 class="text-2xl font-bold text-center">Pelajar / Free</h3>
                            <p class="mt-4 text-center text-gray-600">Cukup untuk 1-2 proyek skripsi atau
                                eksperimen.</p>
                            <div class="mt-6 text-center">
                                <span class="text-5xl font-extrabold">Gratis</span>
                            </div>
                            <ul class="mt-8 space-y-4">
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span><strong>25</strong> Koneksi</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span><strong>100MB</strong> Traffic / Bulan</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Dashboard Dasar</span>
                                </li>
                            </ul>
                            <div class="mt-auto pt-8">
                                <a href="{{ route('register.show') }}"
                                    class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">
                                    Daftar Gratis
                                </a>
                            </div>
                        </div>

                        <div class="border rounded-xl shadow-lg p-8 flex flex-col bg-white">
                            <h3 class="text-2xl font-bold text-center">Hobbyist / Pro</h3>
                            <p class="mt-4 text-center text-gray-600">Untuk developer serius atau startup yang baru
                                mulai.</p>
                            <div class="mt-6 text-center">
                                <span class="text-4xl font-extrabold text-gray-700">Segera Hadir</span>
                            </div>
                            <ul class="mt-8 space-y-4 opacity-60">
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span><strong>150</strong> Koneksi</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span><strong>1GB</strong> Traffic / Bulan</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Dashboard Lebih Lengkap</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Integrasi (Webhook, dll)</span>
                                </li>
                            </ul>
                            <div class="mt-auto pt-8">
                                <a href="#"
                                    class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-gray-500 bg-gray-300 cursor-not-allowed">
                                    Segera Hadir
                                </a>
                            </div>
                        </div>

                        <div class="border rounded-xl shadow-lg p-8 flex flex-col bg-white">
                            <h3 class="text-2xl font-bold text-center">Business</h3>
                            <p class="mt-4 text-center text-gray-600">Target profit utama untuk perusahaan dan
                                produk komersial.</p>
                            <div class="mt-6 text-center">
                                <span class="text-4xl font-extrabold text-gray-700">Segera Hadir</span>
                            </div>
                            <ul class="mt-8 space-y-4 opacity-60">
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span><strong>Ribuan</strong> Koneksi</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Dedicated Instance</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-6 w-6 text-green-500 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Dukungan Prioritas</span>
                                </li>
                            </ul>
                            <div class="mt-auto pt-8">
                                <a href="#"
                                    class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-gray-500 bg-gray-300 cursor-not-allowed">
                                    Segera Hadir
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <section class="bg-gray-50 py-20">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900">
                        Siap Membangun Proyek IoT Anda?
                    </h2>
                    <p class="mt-4 text-lg text-gray-600">
                        Daftar sekarang dan dapatkan broker MQTT private pertamamu dalam 30 detik.
                    </p>
                    <div class="mt-8">
                        {{-- PERBAIKAN: Typo `rsounded-md` diperbaiki menjadi `rounded-md` --}}
                        <a href="{{ Route::has('login') ? (Auth::check() ? route('dashboard') : route('register.show')) : Route('login.show') }}"
                            class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 text-lg transition duration-150 ease-in-out">
                            {{ Auth::check() ? 'Buka Dashboard' : 'Mulai Gratis Sekarang' }}
                        </a>
                    </div>
                </div>
            </section>

        </main>

        <footer class="bg-white border-t">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col md:flex-row justify-between items-center text-center md:text-left">
                    <div>
                        <a href="{{ route('welcome') }}"
                            class="text-lg font-bold text-primary-700">{{ config('app.name', 'BrokerIoT') }}</a>
                        <p class="mt-2 text-sm text-gray-500">
                            &copy; {{ date('Y') }} {{ config('app.name', 'BrokerIoT') }}. All rights reserved.
                        </p>
                    </div>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#fitur" class="text-sm font-medium text-gray-500 hover:text-gray-900">Fitur</a>
                        <a href="#harga" class="text-sm font-medium text-gray-500 hover:text-gray-900">Harga</a>
                        <a href="#cara-kerja" class="text-sm font-medium text-gray-500 hover:text-gray-900">Cara
                            Kerja</a>
                    </div>
                </div>
            </div>
        </footer>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburgerIcon = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            if (hamburgerBtn && mobileMenu && hamburgerIcon && closeIcon) {
                hamburgerBtn.addEventListener('click', function() {
                    const isExpanded = hamburgerBtn.getAttribute('aria-expanded') === 'true';

                    hamburgerBtn.setAttribute('aria-expanded', !isExpanded);

                    mobileMenu.classList.toggle('hidden');

                    hamburgerIcon.classList.toggle('hidden', !
                        isExpanded);
                    closeIcon.classList.toggle('hidden',
                        isExpanded);
                });
            }

            document.querySelectorAll('#mobile-menu a').forEach(link => {
                link.addEventListener('click', () => {
                    hamburgerBtn.setAttribute('aria-expanded', 'false');
                    mobileMenu.classList.add('hidden');
                    hamburgerIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                });
            });
        });
    </script>

</body>

</html>
