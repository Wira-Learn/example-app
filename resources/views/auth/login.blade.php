<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>

    <h1>Halaman Login</h1>

    @if ($errors->any())
    <div style="color: red;">
        <strong>Whoops! Ada masalah dengan input Anda.</strong>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf <div>
            <label for="email">Email</label>
            <br>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <br>

        <div>
            <label for="password">Password</label>
            <br>
            <input id="password" type="password" name="password" required>
        </div>
        <br>

        <div>
            <label for="remember">
                <input id="remember" type="checkbox" name="remember">
                Ingat saya
            </label>
        </div>
        <br>

        <div>
            <button type="submit">
                Login
            </button>
        </div>
    </form>
    <br>
    <a href="{{ route('register.show') }}">Belum punya akun? Register di sini</a>
    <br>
    <a href="/">Kembali ke Halaman Utama</a>

</body>

</html>