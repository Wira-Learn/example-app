<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\MqttBrokerService; // <-- 1. IMPORT SERVICE BARU
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Hapus 'Process' dan 'File' karena sudah tidak dipakai di sini
use Illuminate\Support\Str;
// Kita akan butuh DB untuk Poin 4 (Transactions)
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    // 2. Buat properti untuk menyimpan service
    protected MqttBrokerService $brokerService;

    // 3. Gunakan constructor injection untuk memasukkan service
    // Laravel akan otomatis membuatkan MqttBrokerService untuk kita
    public function __construct(MqttBrokerService $brokerService)
    {
        $this->brokerService = $brokerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Menyimpan perangkat baru dan memprovisinya di Mosquitto.
     */
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
            // Kita bungkus semua logika kritis dalam satu transaksi.
            // Variabel $device perlu di-pass dengan 'use' agar bisa diakses di luar scope closure.
            $device = DB::transaction(function () use ($user, $deviceName, $mqtt_username, $mqtt_password) {

                // 2. Simpan ke Database
                $newDevice = Device::create([
                    'user_id' => $user->id,
                    'name' => $deviceName,
                    'mqtt_username' => $mqtt_username,
                ]);

                // 3. Panggil service untuk melakukan provisi
                // Jika ini gagal, Exception akan dilempar, dan
                // DB::transaction() akan otomatis me-rollback Device::create() di atas.
                $this->brokerService->provisionDevice($newDevice, $mqtt_password);

                return $newDevice; // Kembalikan device yang baru dibuat
            });
            // --- SELESAI TRANSAKSI (COMMIT) ---

        } catch (\Exception $e) {
            // 4. Penanganan Gagal
            // Kita tidak perlu $device->delete() lagi, karena rollback sudah otomatis!

            // Tampilkan error (lebih aman di production)
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device) // <-- Ubah parameter dari string $id
    {
        // Otorisasi: Pastikan pengguna hanya mengedit perangkat miliknya
        if ($device->user_id !== auth()->id()) {
            abort(403, 'TINDAKAN TIDAK DIIZINKAN');
        }

        // Tampilkan view 'edit' dan kirim data perangkat
        return view('devices.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device) // <-- Ubah parameter dari string $id
    {
        // 1. Otorisasi
        if ($device->user_id !== auth()->id()) {
            abort(403, 'TINDAKAN TIDAK DIIZINKAN');
        }

        // 2. Validasi (Sama seperti Poin 7, tapi kita lakukan sekarang)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // 3. Update Database
        // Kita hanya update nama, tidak update kredensial MQTT
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
                // Jika ini gagal, Exception akan dilempar, dan
                // DB::transaction() akan otomatis me-rollback $device->delete().
                $this->brokerService->deprovisionDevice($device);
            });
            // --- SELESAI TRANSAKSI (COMMIT) ---

        } catch (\Exception $e) {
            // 4. Penanganan Gagal
            // Rollback sudah otomatis terjadi.
            $errorMessage = env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan pada server.';
            return redirect()->route('dashboard')
                ->with('error', 'Gagal menghapus perangkat: ' . $errorMessage);
        }

        // --- Selesai (Jika Transaksi Berhasil) ---
        return redirect()->route('dashboard')
            ->with('success', "Perangkat '{$deviceName}' berhasil dihapus.");
    }


    /**
     * 5. HAPUS FUNGSI regenerateAclFile() DARI SINI
     *
     * private function regenerateAclFile() { ... }
     *
     * Fungsi ini sudah tidak ada lagi di controller.
     * Tugasnya sudah diambil alih oleh MqttBrokerService.
     */
}
