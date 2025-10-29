<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dasbor IoT Anda</title>
</head>
<body>

    <h1>Dasbor IoT Anda</h1>
    <p>Halo, {{ Auth::user()->name }}!</p>
    <hr>

    @if (session('new_device_credentials'))
        @php
            $credentials = session('new_device_credentials');
        @php
        
        <div style="border: 2px solid red; padding: 10px; margin-bottom: 20px;">
            <h2 style="color: red;">PENTING: Kredensial Perangkat Baru</h2>
            <p>Perangkat '<strong>{{ $credentials['name'] }}</strong>' telah berhasil dibuat. Harap simpan kredensial berikut di tempat yang aman. <strong>Anda tidak akan dapat melihat kata sandi ini lagi.</strong></p>
            <ul>
                <li><strong>Broker:</strong> <code>mqtt.layanan-anda.com</code> (Ganti dengan alamat Anda)</li>
                <li><strong>Port:</strong> <code>1883</code> (atau <code>8883</code> untuk SSL)</li>
                <li><strong>Username:</strong> <code>{{ $credentials['username'] }}</code></li>
                <li><strong>Password:</strong> <code>{{ $credentials['password'] }}</code></li>
                <li><strong>Topic Publish (Contoh):</strong> <code>{{ $credentials['publish_topic'] }}</code></li>
                <li><strong>Topic Subscribe (Contoh):</strong> <code>{{ $credentials['subscribe_topic'] }}</code></li>
            </ul>
        </div>
    @endif

    @if (session('error'))
        <p style="color: red;"><strong>Error:</strong> {{ session('error') }}</p>
    @endif

    @if (session('success'))
        <p style="color: green;"><strong>Sukses:</strong> {{ session('success') }}</p>
    @endif


    <h2>Perangkat Anda</h2>

    <a href="{{ route('devices.create') }}">Buat Perangkat Baru</a>

    @if ($devices->count() > 0)
        <table border="1" cellpadding="5" cellspacing="0" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Perangkat</th>
                    <th>MQTT Username</th>
                    <th>Dibuat Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($devices as $device)
                    <tr>
                        <td>{{ $device->id }}</td>
                        <td>{{ $device->name }}</td>
                        <td><code>{{ $device->mqtt_username }}</code></td>
                        <td>{{ $device->created_at->format('d M Y') }}</td>
                        <td>
        <form action="{{ route('devices.destroy', $device) }}" method="POST" style="display:inline;" onsubmit="return confirm('Anda yakin ingin menghapus perangkat \'{{ $device->name }}\'? Ini tidak bisa dibatalkan.');">
            @csrf
            @method('DELETE')
            <button type="submit">Hapus</button>
        </form>
    </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Anda belum memiliki perangkat. Silakan buat satu!</p>
    @endif

</body>
</html>