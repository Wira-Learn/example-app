<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard - {{ config('app.name', 'BrokerIoT') }}</title>

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
                                            {{-- <a href="#"                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a> --}}
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

                <h1 class="text-3xl font-bold text-gray-800 mb-6">
                    Dasbor IoT Anda
                </h1>

                <div class="space-y-8">

                    @if (session('new_device_credentials'))
                        @php
                            $credentials = session('new_device_credentials');
                        @endphp
                        <div class="bg-red-50 border-l-4 border-red-600 rounded-md shadow-md p-6" role="alert">
                            <div class="flex items-center">
                                <svg class="h-6 w-6 text-red-600 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.01" />
                                </svg>
                                <h2 class="text-xl font-bold text-red-800">PENTING: Kredensial Perangkat Baru</h2>
                            </div>
                            <p class="mt-3 text-red-700">Perangkat '<strong>{{ $credentials['name'] }}</strong>' telah
                                berhasil
                                dibuat. Harap simpan kredensial berikut di tempat yang aman. <strong>Anda tidak akan
                                    dapat melihat
                                    kata sandi ini lagi.</strong></p>

                            <div
                                class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 bg-red-100 p-4 rounded-md font-mono text-sm">
                                <div class="col-span-1 sm:col-span-2">
                                    <strong>Broker:</strong>
                                    <code
                                        class="bg-red-200 px-1 py-0.5 rounded">{{ config('mqtt.broker_host', 'mqtt.layanan-anda.com') }}</code>
                                </div>
                                <div>
                                    <strong>Port:</strong>
                                    <code
                                        class="bg-red-200 px-1 py-0.5 rounded">{{ config('mqtt.port_unsecure', 1883) }}</code>
                                </div>
                                <div>
                                    <strong>Port (SSL):</strong>
                                    <code
                                        class="bg-red-200 px-1 py-0.5 rounded">{{ config('mqtt.port_secure', 8883) }}</code>
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <strong>Username:</strong>
                                    <code class="bg-red-200 px-1 py-0.5 rounded">{{ $credentials['username'] }}</code>
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <strong>Password:</strong>
                                    <code class="bg-red-200 px-1 py-0.5 rounded">{{ $credentials['password'] }}</code>
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <strong>Topic Publish:</strong>
                                    <code
                                        class="bg-red-200 px-1 py-0.5 rounded">{{ $credentials['publish_topic'] }}</code>
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <strong>Topic Subscribe:</strong>
                                    <code
                                        class="bg-red-200 px-1 py-0.5 rounded">{{ $credentials['subscribe_topic'] }}</code>
                                </div>
                            </div>
                            <p class="mt-3 text-sm text-red-600">Tip: Salin dan tempel ini ke dalam kode atau catatan
                                Anda sekarang.</p>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="bg-green-50 border border-green-300 rounded-md p-4 text-green-800" role="alert">
                            <strong>Sukses:</strong> {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-50 border border-red-300 rounded-md p-4 text-red-800" role="alert">
                            <strong>Error:</strong> {{ session('error') }}
                        </div>
                    @endif

                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                <h2 class="text-xl font-bold text-gray-800">Perangkat Anda</h2>
                                <a href="{{ route('devices.create') }}"
                                    class="inline-flex items-center px-5 py-2 rounded-md text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-sm transition duration-150 ease-in-out w-full sm:w-auto">
                                    <svg class="h-5 w-5 -ml-1 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Buat Perangkat Baru
                                </a>
                            </div>
                        </div>

                        @if ($devices->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-max">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nama Perangkat</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                MQTT Username</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dibuat Tanggal</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($devices as $device)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $device->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">ID: {{ $device->id }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <code
                                                        class="text-sm font-mono bg-gray-100 px-2 py-1 rounded">{{ $device->mqtt_username }}</code>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $device->created_at->format('d M Y') }}
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                                    <a href="{{ route('devices.edit', $device) }}"
                                                        class="text-primary-600 hover:text-primary-800">Edit</a>
                                                    <form method="POST"
                                                        action="{{ route('devices.destroy', $device) }}"
                                                        onsubmit="return confirm('Anda yakin ingin menghapus perangkat ini?');"
                                                        class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-800">Hapus</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-8 text-center">
                                <svg class="h-12 w-12 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>

                                <h3 class="mt-4 text-lg font-medium text-gray-900">Anda belum memiliki perangkat</h3>
                                <p class="mt-1 text-sm text-gray-500">Silakan buat satu untuk memulai.</p>

                            </div>
                        @endif
                    </div>

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
