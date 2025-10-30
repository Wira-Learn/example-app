<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selamat Datang di Portal IoT</title>
</head>

<body>
    <div style="text-align: center; padding-top: 50px;">
        <h1>Selamat Datang di Portal IoT Anda</h1>
        <p>Silakan login atau register untuk melanjutkan.</p>

        @if (Route::has('login'))
            <div>
                @auth
                    <a href="{{ url('/dashboard') }}">Ke Dashboard</a>
                @else
                    <a href="{{ route('login.show') }}">Login</a>
                    <br><br>
                    @if (Route::has('register.show'))
                        <a href="{{ route('register.show') }}">Register</a>
                    @endif
                @endauth
            </div>
        @endif

    </div>
</body>

</html>
