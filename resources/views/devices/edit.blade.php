<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Perangkat: {{ $device->name }}</title>
</head>

<body>

    <div>
        <p>Halo, {{ Auth::user()->name }}!</p>

        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
        <br>
        <a href="{{ route('dashboard') }}">
            << Kembali ke Dasbor</a>
    </div>

    <hr>

    <h1>Edit Perangkat: {{ $device->name }}</h1>

    <hr>

    <form method="POST" action="{{ route('devices.update', $device) }}">
        @csrf @method('PATCH') <div>
            <label for="name">Nama Perangkat:</label><br>
            <input type="text" id="name" name="name" value="{{ old('name', $device->name) }}" required autofocus>

            @error('name')
            <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <br>

        <div>
            <a href="{{ route('dashboard') }}">Batal</a>

            <button type="submit" style="margin-left: 10px;">
                Simpan Perubahan
            </button>
        </div>
    </form>

</body>

</html>