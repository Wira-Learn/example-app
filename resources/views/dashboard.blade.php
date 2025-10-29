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
                    <div class="flex items-center space-x-2"> <a href="{{ route('devices.edit', $device) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Edit') }}
                        </a>

                        <form method="POST" action="{{ route('devices.destroy', $device) }}" onsubmit="return confirm('Anda yakin ingin menghapus perangkat ini?');" class="m-0">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>
                                {{ __('Hapus') }}
                            </x-danger-button>
                        </form>
                    </div>
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