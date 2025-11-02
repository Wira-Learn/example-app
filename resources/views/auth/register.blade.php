<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Daftar Akun - {{ config('app.name', 'BrokerIoT') }}</title>

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

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">

        <div>
            <a href="{{ route('welcome') }}" class="text-3xl font-bold text-primary-700">
                {{ config('app.name', 'BrokerIoT') }}
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">

            <h2 class="text-2xl font-bold text-center text-gray-900">Buat Akun Baru</h2>

            @if ($errors->any())
                <div class="mb-4 mt-4 bg-red-50 border border-red-200 text-sm text-red-700 rounded-md p-4"
                    role="alert">
                    <div class="font-medium">Whoops! Ada masalah.</div>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.submit') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">Nama</label>
                    <div class="relative mt-1 rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required
                            autofocus autocomplete="name"
                            class="block w-full pl-10 pt-2 pb-2 rounded-md shadow-sm border-gray-300 hover:border-gray-400 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
                    </div>
                </div>

                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                    <div class="relative mt-1 rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                                <path
                                    d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email"
                            class="block w-full pl-10 pt-2 pb-2  rounded-md shadow-sm border-gray-300 hover:border-gray-400 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
                    </div>
                </div>

                <div>
                    <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                    <div class="relative mt-1 rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="block w-full pl-10 pt-2 pb-2 rounded-md shadow-sm border-gray-300 hover:border-gray-400 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Konfirmasi
                        Password</label>
                    <div class="relative mt-1 rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password"
                            class="block w-full pl-10 pt-2 pb-2  rounded-md shadow-sm border-gray-300 hover:border-gray-400 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Register
                    </button>
                </div>

                <div class="text-center pt-4">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login.show') }}" class="font-medium text-primary-600 hover:text-primary-500">
                            Login di sini
                        </a>
                    </p>
                </div>

            </form>
        </div>

        <div class="mt-4">
            <a href="{{ route('welcome') }}" class="text-sm text-gray-600 hover:text-primary-600">
                &larr; Kembali ke Halaman Utama
            </a>
        </div>
    </div>

</body>

</html>
