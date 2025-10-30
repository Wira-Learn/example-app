<?php
namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\MqttBrokerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process; // <-- 1. TAMBAHKAN IMPORT INI
use Illuminate\Support\Str;

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
        return redirect()->route('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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

        $user          = Auth::user();
        $mqtt_username = 'user_' . $user->id . '_dev_' . Str::random(8);
        $mqtt_password = Str::random(16);
        $deviceName    = $request->name;

        // =================================================================
        // !! PERINGATAN WAJIB: KONFIGURASI SUDOERS !!
        // =================================================================
        // Kode ini akan GAGAL (dan device tidak tersimpan) jika
        // pengguna web server (misal: www-data) tidak punya izin
        // 'sudo' TANPA password untuk 2 perintah di MqttBrokerService.
        //
        // PASTIKAN Anda telah mengedit file 'sudoers' di server Anda:
        // `sudo visudo`
        //
        // Tambahkan baris-baris ini (ganti www-data jika perlu):
        // www-data ALL=(ALL) NOPASSWD: /usr/bin/mosquitto_passwd
        // www-data ALL=(ALL) NOPASSWD: /bin/systemctl reload mosquitto
        // =================================================================

        try {
            $device = DB::transaction(function () use ($user, $deviceName, $mqtt_username, $mqtt_password) {
                $newDevice = Device::create([
                    'user_id'       => $user->id,
                    'name'          => $deviceName,
                    'mqtt_username' => $mqtt_username,
                ]);

                // Panggil service untuk provisi
                $this->brokerService->provisionDevice($newDevice, $mqtt_password);

                return $newDevice;
            });
        } catch (\Illuminate\Process\Exceptions\ProcessFailedException $e) {
            // Ini adalah error jika perintah `sudo` gagal
            Log::error("GAGAL PROVISI DEVICE (PERIKSA SUDOERS!): " . $e->getMessage(), [
                'command'      => $e->result->command(),
                'error_output' => $e->result->errorOutput(),
            ]);
            return back()->with('error', 'Gagal memprovisi perangkat di broker MQTT. Periksa log server. Kemungkinan besar ini adalah masalah izin `sudo` untuk user web server.');
        } catch (\Exception $e) {
            // Error umum lainnya (misal: koneksi database)
            Log::error("Gagal membuat perangkat: " . $e->getMessage());
            $errorMessage = env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan pada server saat membuat perangkat.';
            return back()->with('error', $errorMessage);
        }

        // --- Selesai (Jika Transaksi Berhasil) ---
        return redirect()->route('dashboard')
            ->with('success', 'Perangkat berhasil dibuat!')
            ->with('new_device_credentials', [
                'name'          => $device->name,
                'username'      => $mqtt_username,
                'password'      => $mqtt_password,
                'publish_topic' => "{$mqtt_username}/data/out",
                'subscribe_topic' => "{$mqtt_username}/cmd/in",
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Tidak digunakan
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Device $device)
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }
        return view('devices.edit', compact('device'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Device $device)
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $device->update($validated);
        } catch (\Exception $e) {
            Log::error("Gagal update perangkat {$device->id}: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui nama perangkat.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Nama perangkat berhasil diperbarui.');
    }

    /**
     * Menghapus perangkat dari database dan Mosquitto.
     */
    public function destroy(Device $device)
    {
        if ($device->user_id !== auth()->id()) {
            abort(403);
        }

        $deviceName = $device->name;

        // Peringatan: Pastikan izin sudoers juga sudah diatur untuk `mosquitto_passwd -D ...`

        try {
            DB::transaction(function () use ($device) {
                // Hapus dari Database
                $device->delete();
                // Hapus provisi dari Mosquitto
                $this->brokerService->deprovisionDevice($device);
            });
        } catch (\Illuminate\Process\Exceptions\ProcessFailedException $e) {
            Log::error("GAGAL DEPROVISI DEVICE (PERIKSA SUDOERS!): " . $e->getMessage(), [
                'command'      => $e->result->command(),
                'error_output' => $e->result->errorOutput(),
            ]);
            return redirect()->route('dashboard')
                ->with('error', "GAGAL menghapus perangkat '{$deviceName}' dari broker MQTT (Mungkin masalah izin `sudo`). Perangkat TIDAK dihapus dari database. Harap perbaiki izin server dan coba lagi.");
        } catch (\Exception $e) {
            Log::error("Gagal menghapus perangkat {$device->id}: " . $e->getMessage());
            $errorMessage = env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan pada server saat menghapus perangkat.';
            return redirect()->route('dashboard')
                ->with('error', $errorMessage);
        }

        return redirect()->route('dashboard')
            ->with('success', "Perangkat '{$deviceName}' berhasil dihapus dari database dan broker MQTT.");
    }

    /**
     * Mem-publish pesan MQTT menggunakan mosquitto_pub.
     */
    public function publishMessage(Request $request)
    {
        $validated = $request->validate([
            'topic'   => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        $brokerHost = config('mqtt.broker_host');
        $brokerPort = config('mqtt.port_unsecure');

        try {
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
                '-r', // Retained message
            ];

            // =================================================================
            // !! KODE INI SEKARANG AKTIF !!
            // =================================================================
            // Karena `allow_anonymous false`, kita WAJIB pakai autentikasi.
            // Pastikan Anda sudah mengatur user panel di server dan di .env
            // (Lihat Daftar Perbaikan poin 2)
            // =================================================================

            $panelUser = env('MQTT_PANEL_USERNAME');
            $panelPass = env('MQTT_PANEL_PASSWORD');

            if ($panelUser && $panelPass) {
                $command[] = '-u';
                $command[] = $panelUser;
                $command[] = '-P';
                $command[] = $panelPass;
                Log::info('Mencoba publish MQTT dengan user panel: ' . $panelUser);
            } else {
                // Jika .env tidak diatur, ini PASTI GAGAL
                Log::error('MQTT_PANEL_USERNAME atau MQTT_PANEL_PASSWORD tidak diatur di .env. Publish MQTT akan gagal karena allow_anonymous false.');
                return back()->with('mqtt_error', 'Autentikasi panel MQTT tidak diatur di file .env. Publish gagal.');
            }

            // Jalankan proses
            $result = Process::path(dirname(shell_exec('which mosquitto_pub')))
                ->timeout(10)
                ->run($command);

            if ($result->successful()) {
                return back()->with('mqtt_success', 'Pesan berhasil di-publish!');
            } else {
                Log::error('Gagal publish MQTT:', ['command' => implode(' ', $command), 'error' => $result->errorOutput()]);
                return back()->with('mqtt_error', 'Gagal publish pesan. Cek log server. Error: ' . $result->exitCode() . ' - ' . Str::limit($result->errorOutput(), 100));
            }
        } catch (\Exception $e) {
            Log::error('Exception saat publish MQTT: ' . $e->getMessage());
            return back()->with('mqtt_error', 'Gagal menjalankan perintah publish: ' . $e->getMessage());
        }
    }
}
