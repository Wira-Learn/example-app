<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\MqttBrokerService; // <-- Sudah ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // <-- Sudah ada
use Illuminate\Support\Facades\Process; // <-- 1. TAMBAHKAN IMPORT INI

class DeviceController extends Controller
{
    protected MqttBrokerService $brokerService;

    public function __construct(MqttBrokerService $brokerService)
    {
        $this->brokerService = $brokerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Arahkan saja ke dashboard, karena daftar device ada di sana
        return redirect()->route('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     * 2. TAMBAHKAN METHOD 'create' INI
     */
    public function create()
    {
        // Tampilkan view 'create.blade.php' yang tadi kita buat
        return view('create');
    }

    /**
     * Menyimpan perangkat baru dan memprovisinya di Mosquitto.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // 1. Hasilkan Kredensial Unik
        $mqtt_username = 'user_' . $user->id . '_dev_' . Str::random(8);
        $mqtt_password = Str::random(16);
        $deviceName = $request->name; // Simpan nama untuk flash message

        try {
            // --- MULAI TRANSAKSI ---
            $device = DB::transaction(function () use ($user, $deviceName, $mqtt_username, $mqtt_password) {

                // 2. Simpan ke Database
                $newDevice = Device::create([
                    'user_id' => $user->id,
                    'name' => $deviceName,
                    'mqtt_username' => $mqtt_username,
                ]);

                // 3. Panggil service untuk melakukan provisi
                $this->brokerService->provisionDevice($newDevice, $mqtt_password);

                return $newDevice; // Kembalikan device yang baru dibuat
            });
            // --- SELESAI TRANSAKSI (COMMIT) ---

        } catch (\Exception $e) {
            // 4. Penanganan Gagal
            $errorMessage = env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan pada server.';
            return back()->with('error', 'Gagal memprovisi perangkat: ' . $errorMessage);
        }

        // --- Selesai (Jika Transaksi Berhasil) ---
        return redirect()->route('dashboard')
            ->with('success', 'Perangkat berhasil dibuat!')
            ->with('new_device_credentials', [
                'name' => $device->name,
                'username' => $mqtt_username,
                'password' => $mqtt_password,
                'publish_topic' => "{$mqtt_username}/data/out",
                'subscribe_topic' => "{$mqtt_username}/cmd/in",
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Tidak kita gunakan saat ini, bisa diabaikan
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        // Otorisasi: Pastikan pengguna hanya mengedit perangkat miliknya
        if ($device->user_id !== auth()->id()) {
            abort(403, 'TINDAKAN TIDAK DIIZINKAN');
        }

        // Tampilkan view 'edit' dan kirim data perangkat
        // (Nama view-nya kita ganti jadi 'devices.edit' agar rapi)
        return view('devices.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device)
    {
        // 1. Otorisasi
        if ($device->user_id !== auth()->id()) {
            abort(403, 'TINDAKAN TIDAK DIIZINKAN');
        }

        // 2. Validasi
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // 3. Update Database
        $device->update($validated);

        // 4. Redirect kembali ke dashboard
        return redirect()->route('dashboard')
            ->with('success', 'Nama perangkat berhasil diperbarui.');
    }

    /**
     * Menghapus perangkat dari database dan Mosquitto.
     */
    public function destroy(Device $device)
    {
        // 1. Otorisasi
        if ($device->user_id !== auth()->id()) {
            abort(403, 'TINDAKAN TIDAK DIIZINKAN');
        }

        $deviceName = $device->name; // Simpan nama untuk pesan sukses

        try {
            // --- MULAI TRANSAKSI ---
            DB::transaction(function () use ($device) {
                // 2. Hapus dari Database
                $device->delete();

                // 3. Panggil service untuk menghapus provisi
                $this->brokerService->deprovisionDevice($device);
            });
            // --- SELESAI TRANSAKSI (COMMIT) ---

        } catch (\Exception $e) {
            // 4. Penanganan Gagal
            $errorMessage = env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan pada server.';
            return redirect()->route('dashboard')
                ->with('error', 'Gagal menghapus perangkat: ' . $errorMessage);
        }

        // --- Selesai (Jika Transaksi Berhasil) ---
        return redirect()->route('dashboard')
            ->with('success', "Perangkat '{$deviceName}' berhasil dihapus.");
    }


    /**
     * 3. TAMBAHKAN METHOD 'publishMessage' BARU INI
     *
     * Mem-publish pesan MQTT menggunakan mosquitto_pub.
     * Ini adalah cara sederhana tanpa library MQTT client di PHP.
     */
    public function publishMessage(Request $request)
    {
        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        // Ambil info broker dari config
        $brokerHost = config('mqtt.broker_host');
        $brokerPort = config('mqtt.port_unsecure');

        // Ambil username dan password dari salah satu device milik user
        // Ini HANYA untuk OTORISASI publish.
        // Asumsinya, user boleh publish ke topic mana saja setelah login.
        // Jika Anda ingin membatasi, logikanya harus lebih kompleks.
        $device = Auth::user()->devices()->first();

        // Jika user tidak punya device, dia tidak bisa publish
        if (!$device) {
            return back()->with('mqtt_error', 'Anda harus memiliki setidaknya satu perangkat untuk mem-publish pesan.');
        }

        // Kita perlu mengambil password asli. 
        // Password tidak disimpan di DB, jadi kita tidak bisa mengambilnya.
        //
        // =================================================================
        // !! PERHATIAN !!
        // =================================================================
        // Kode di MqttBrokerService menggunakan `mosquitto_passwd` yang
        // mengenkripsi password. Kita tidak bisa mengambil password aslinya
        // dari database (karena memang tidak disimpan).
        //
        // Solusi Sederhana (Untuk Saat Ini):
        // Kita asumsikan broker Mosquitto Anda mengizinkan koneksi dari localhost
        // tanpa autentikasi (misalnya allow_anonymous true), atau Anda memiliki
        // satu user khusus 'admin_panel' untuk publish.
        //
        // Solusi Sebenarnya (Lebih Rumit):
        // 1. Menggunakan library PHP MQTT Client (seperti php-mqtt/client)
        // 2. Menyimpan password device di database (TIDAK DISARANKAN)
        // 3. Mengubah MqttBrokerService agar menyimpan password mentah 
        //    sementara di cache/session saat device dibuat.
        //
        // Mari kita gunakan Solusi Sederhana untuk sekarang:
        // Kita akan publish TANPA username/password, dengan asumsi 
        // koneksi dari localhost (tempat web server berjalan) diizinkan.
        // =================================================================

        try {
            // Perintah mosquitto_pub sederhana tanpa autentikasi
            $command = [
                'mosquitto_pub',
                '-h',
                $brokerHost,
                '-p',
                $brokerPort,
                '-t',
                $validated['topic'],
                '-m',
                $validated['message'],
                '-r', // Menandakan pesan sebagai 'retained'
            ];

            // Jika Anda punya user khusus untuk panel ini, tambahkan:
            // $command[] = '-u';
            // $command[] = 'username_panel';
            // $command[] = '-P';
            // $command[] = 'password_panel';

            // Jalankan proses
            $result = Process::run($command);

            if ($result->successful()) {
                return back()->with('mqtt_success', 'Pesan berhasil di-publish!');
            } else {
                // Tampilkan error jika gagal
                return back()->with('mqtt_error', 'Gagal publish pesan: ' . $result->errorOutput());
            }
        } catch (\Exception $e) {
            return back()->with('mqtt_error', 'Gagal menjalankan perintah publish: ' . $e->getMessage());
        }
    }
}
