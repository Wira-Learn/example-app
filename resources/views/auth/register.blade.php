<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>

<body>

    <h1>Halaman Register</h1>

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

    <form method="POST" action="{{ route('register.submit') }}">
        @csrf <div>
            <label for="name">Nama</label>
            <br>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
        </div>
        <br>

        <div>
            <label for="email">Email</label>
            <br>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <br>

        <div>
            <label for="password">Password</label>
            <br>
            <input id="password" type="password" name="password" required>
        </div>
        <br>

        <div>
            <label for="password_confirmation">Konfirmasi Password</label>
            <br>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>
        <br>

        <div>
            <button type="submit">
                Register
            </button>
        </div>
    </form>
    <br>
    <a href="{{ route('login.show') }}">Sudah punya akun? Login di sini</a>
    <br>
    <a href="/">Kembali ke Halaman Utama</a>

</body>

</html>
