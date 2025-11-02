<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Edit Perangkat: {{ $device->name }} - {{ config('app.name', 'BrokerIoT') }}</title>

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

<body class="font-sans antialiased text-gray-900 bg-gray-100">

    <div class="flex min-h-screen bg-gray-100">

        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

            <div class="p-4 flex justify-between items-center">
                <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-primary-700">
                    {{ config('app.name', 'BrokerIoT') }}
                </a>
                <button id="close-sidebar-btn" class="md:hidden text-gray-600 hover:text-gray-900">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="mt-6">
                <a href="{{ route('dashboard') }}"
                    class="block px-4 py-3 text-gray-700 bg-primary-50 hover:bg-primary-100 hover:text-primary-700 font-medium border-l-4 border-primary-500">
                    Dashboard
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col md:pl-64">

            <nav id="navbar-top" class="bg-white shadow-sm sticky top-0 z-40">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">

                        <button id="open-sidebar-btn" class="md:hidden text-gray-600 hover:text-gray-900">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
                            </svg>
                        </button>

                        <div class="hidden md:block"></div>

                        <div class="flex items-center ms-auto">
                            @auth
                                <ul class="flex items-center">
                                    <li class="relative ms-3">
                                        <button id="profile-dropdown-btn" type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-800 focus:outline-none transition ease-in-out duration-150">
                                            <div>{{ Auth::user()->name }}</div>
                                            <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                        <div id="profile-dropdown-menu"
                                            class="absolute right-0 z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1 hidden">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <a href="{{ route('logout') }}"
                                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Log Out
                                                </a>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            @else
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('login.show') }}"
                                        class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">
                                        Login
                                    </a>
                                    @if (Route::has('register.show'))
                                        <a href="{{ route('register.show') }}"
                                            class="inline-flex items-center px-5 py-2 rounded-md text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 shadow-sm">
                                            Register
                                        </a>
                                    @endif
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8">

                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-gray-900 mb-4">
                    <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    Kembali ke Dasbor
                </a>

                <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-4">

                    <form method="POST" action="{{ route('devices.update', $device) }}">
                        @csrf
                        @method('PATCH')

                        <div class="p-6 border-b border-gray-200">
                            <h1 class="text-2xl font-bold text-gray-800">
                                Edit Perangkat: {{ $device->name }}
                            </h1>
                        </div>

                        <div class="p-6 space-y-6">
                            <div>
                                <label for="name" class="block font-medium text-sm text-gray-700">Nama
                                    Perangkat</label>
                                <input type="text" id="name" name="name"
                                    value="{{ old('name', $device->name) }}" required autofocus
                                    class="block mt-1 w-full px-3 py-2 rounded-md shadow-sm border border-gray-400 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror">

                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end items-center space-x-4">
                            <a href="{{ route('dashboard') }}"
                                class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200 transition duration-150 ease-in-out">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const openSidebarBtn = document.getElementById('open-sidebar-btn');
            const closeSidebarBtn = document.getElementById('close-sidebar-btn');
            const profileDropdownBtn = document.getElementById('profile-dropdown-btn');
            const profileDropdownMenu = document.getElementById('profile-dropdown-menu');

            if (openSidebarBtn && sidebar) {
                openSidebarBtn.addEventListener('click', function() {
                    sidebar.classList.remove('-translate-x-full');
                });
            }
            if (closeSidebarBtn && sidebar) {
                closeSidebarBtn.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                });
            }

            if (profileDropdownBtn && profileDropdownMenu) {
                profileDropdownBtn.addEventListener('click', function() {
                    profileDropdownMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (profileDropdownBtn && !profileDropdownBtn.contains(event.target) && !
                        profileDropdownMenu.contains(event.target)) {
                        profileDropdownMenu.classList.add('hidden');
                    }
                });
            }
        });
    </script>

</body>

</html>
