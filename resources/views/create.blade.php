<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Perangkat Baru</title>
</head>
<body>

    <h1>Buat Perangkat Baru</h1>
    <p>Masukkan nama yang mudah diingat untuk perangkat baru Anda (misalnya: "Sensor Suhu Kamar" atau "Lampu Garasi").</p>

    <a href="{{ route('dashboard') }}"><< Kembali ke Dasbor</a>
    
    <hr>

    <form action="{{ route('devices.store') }}" method="POST">
        @csrf

        <div>
            <label for="name">Nama Perangkat:</label><br>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            
            @error('name')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <br>
        <button type="submit">Buat Perangkat</button>
    </form>

</body>
</html>