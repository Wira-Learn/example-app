<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login - {{ config('app.name', 'BrokerIoT') }}</title>

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

            <h2 class="text-2xl font-bold text-center text-gray-900">Login ke Akun Anda</h2>

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

            <form method="POST" action="{{ route('login.submit') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email"
                        class="block mt-1 pt-2 pb-2 pl-2 w-full rounded-md shadow-sm border-gray-300 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                </div>

                <div>
                    <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="block mt-1 pt-2 pb-2 pl-2 w-full rounded-md shadow-sm border-gray-300 focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember" class="inline-flex items-center">
                        <input id="remember" type="checkbox" name="remember"
                            class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>

                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Login
                    </button>
                </div>

                <div class="text-center pt-4">
                    <p class="text-sm text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register.show') }}"
                            class="font-medium text-primary-600 hover:text-primary-500">
                            Register di sini
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
