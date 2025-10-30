<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dasbor IoT Anda</title>
</head>

<body>

    <div>
        <p>Halo, {{ Auth::user()->name }}! ({{ Auth::user()->email }})</p>

        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>

    <hr>
    <h1>Dasbor IoT Anda</h1>

    @if (session('new_device_credentials'))
        @php
            $credentials = session('new_device_credentials');
        @endphp
        <div style="border: 2px solid red; padding: 10px; margin-bottom: 20px;">
            <h2 style="color: red;">PENTING: Kredensial Perangkat Baru</h2>
            <p>Perangkat '<strong>{{ $credentials['name'] }}</strong>' telah berhasil dibuat. Harap simpan kredensial
                berikut di tempat yang aman. <strong>Anda tidak akan dapat melihat kata sandi ini lagi.</strong></p>
            <ul>
                <li><strong>Broker:</strong> <code>{{ config('mqtt.broker_host', 'mqtt.layanan-anda.com') }}</code></li>
                <li><strong>Port:</strong> <code>{{ config('mqtt.port_unsecure', 1883) }}</code> (atau
                    <code>{{ config('mqtt.port_secure', 8883) }}</code> untuk SSL)</li>
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
        <table border="1" cellpadding="5" cellspacing="0" style="margin-top: 15px; border-collapse: collapse;">
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
                            <a href="{{ route('devices.edit', $device) }}">Edit</a>

                            <form method="POST" action="{{ route('devices.destroy', $device) }}"
                                onsubmit="return confirm('Anda yakin ingin menghapus perangkat ini?');"
                                style="display: inline; margin-left: 10px;">
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

    <hr style="margin-top: 30px; margin-bottom: 20px;">

    <h2>Kontrol MQTT (Publish Pesan)</h2>
    <p>Gunakan form ini untuk mengirim pesan ke topic mana pun.</p>

    @if (session('mqtt_success'))
        <p style="color: green;">{{ session('mqtt_success') }}</p>
    @endif
    @if (session('mqtt_error'))
        <p style="color: red;">{{ session('mqtt_error') }}</p>
    @endif

    <form method="POST" action="{{ route('devices.publish') }}">
        @csrf
        <div>
            <label for="topic">Topic:</label><br>
            <input type="text" id="topic" name="topic" value="{{ old('topic') }}"
                placeholder="Contoh: user_123_dev_abc/cmd/in" style="width: 300px;" required>
            @error('topic')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>
        <br>
        <div>
            <label for="message">Message:</label><br>
            <input type="text" id="message" name="message" value="{{ old('message') }}"
                placeholder="Contoh: ON atau 1" style="width: 300px;" required>
            @error('message')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>
        <br>
        <button type="submit">Publish Pesan</button>
    </form>

</body>

</html>
