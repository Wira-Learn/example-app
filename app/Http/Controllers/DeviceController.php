<?php

namespace App\Http{namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\MqttBrokerService; // <-- Sudah ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // <-- Sudah ada
use Illuminate\Support\Facades\Log; // <-- Tambahkan Log Facade
use Illuminate\Support\Facades\Process; // <-- Sudah ada

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

        $user = Auth::user();
        $mqtt_username = 'user_' . $user->id . '_dev_' . Str::random(8);
        $mqtt_password = Str::random(16); // Password ini HANYA untuk device
        $deviceName = $request->name;

        // =================================================================
        // !! CATATAN PENTING PERMISSIONS !!
        // =================================================================
        // Method MqttBrokerService->provisionDevice() di bawah ini
        // menjalankan perintah `sudo mosquitto_passwd` dan `sudo systemctl reload mosquitto`.
        // Pastikan pengguna web server Anda (misal www-data atau nginx)
        // memiliki izin sudo TANPA PASSWORD untuk menjalankan kedua perintah
        // tersebut. Konfigurasi ini dilakukan di file /etc/sudoers atau
        // file di /etc/sudoers.d/.
        // Contoh baris di sudoers (GANTI www-data SESUAI USER WEBSERVER ANDA):
        // www-data ALL=(ALL) NOPASSWD: /usr/bin/mosquitto_passwd
        // www-data ALL=(ALL) NOPASSWD: /bin/systemctl reload mosquitto
        //
        // Jika permissions ini tidak benar, proses di bawah akan GAGAL
        // dan device TIDAK AKAN tersimpan di database karena transaksi di-rollback.
        // =================================================================

        try {
            $device = DB::transaction(function () use ($user, $deviceName, $mqtt_username, $mqtt_password) {

                // Simpan ke Database
                $newDevice = Device::create([
                    'user_id' => $user->id,
                    'name' => $deviceName,
                    'mqtt_username' => $mqtt_username,
                ]);

                // Panggil service untuk melakukan provisi di Mosquitto
                $this->brokerService->provisionDevice($newDevice, $mqtt_password);

                return $newDevice;
            });

        } catch (\Illuminate\Process\Exceptions\ProcessFailedException $e) {
            // Tangkap error spesifik dari Process
            Log::error("Gagal menjalankan perintah Mosquitto: " . $e->getMessage(), ['output' => $e->result->errorOutput()]);
            return back()->with('error', 'Gagal memprovisi perangkat di broker MQTT. Cek log server untuk detail. Kemungkinan masalah permissions sudo.');
        } catch (\Exception $e) {
            // Tangkap error lainnya (misal DB)
            Log::error("Gagal membuat perangkat: " . $e->getMessage());
            $errorMessage = env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan pada server saat membuat perangkat.';
            return back()->with('error', $errorMessage);
        }

        // --- Selesai (Jika Transaksi Berhasil) ---
        return redirect()->route('dashboard')
            ->with('success', 'Perangkat berhasil dibuat!')
            ->with('new_device_credentials', [
                'name' => $device->name,
                'username' => $mqtt_username,
                'password' => $mqtt_password, // Kirim password ke view (hanya sekali ini)
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

        // =================================================================
        // !! CATATAN PENTING PERMISSIONS (Sama seperti store) !!
        // =================================================================
        // Method MqttBrokerService->deprovisionDevice() juga menjalankan
        // `sudo mosquitto_passwd -D` dan `sudo systemctl reload mosquitto`.
        // Pastikan permissions sudo sudah benar.
        // =================================================================

        try {
            DB::transaction(function () use ($device) {
                // Hapus dari Database
                $device->delete();

                // Panggil service untuk menghapus provisi dari Mosquitto
                $this->brokerService->deprovisionDevice($device);
            });

        } catch (\Illuminate\Process\Exceptions\ProcessFailedException $e) {
            Log::error("Gagal menjalankan perintah Mosquitto saat hapus: " . $e->getMessage(), ['output' => $e->result->errorOutput()]);
            // JANGAN ROLLBACK delete() di DB jika hanya gagal di MQTT
             return redirect()->route('dashboard')
                 ->with('warning', "Perangkat '{$deviceName}' dihapus dari database, tetapi GAGAL dihapus dari broker MQTT. Cek log server. Kemungkinan masalah permissions sudo.");
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
            'topic' => 'required|string|max:255',
            'message' => 'required|string|max:255',
        ]);

        $brokerHost = config('mqtt.broker_host');
        $brokerPort = config('mqtt.port_unsecure');

        // =================================================================
        // !! PERHATIAN: Pilihan Autentikasi MQTT Publish !!
        // =================================================================
        // Karena kita TIDAK menyimpan password device di database, web server
        // tidak bisa login sebagai device tersebut untuk publish.
        // Anda punya beberapa pilihan:
        //
        // OPSI 1: Mosquitto Allow Anonymous (Mudah untuk tes, tidak aman)
        //    - Edit file konfigurasi mosquitto (biasanya /etc/mosquitto/mosquitto.conf)
        //    - Tambahkan baris: `allow_anonymous true`
        //    - Tambahkan baris: `listener [PORT] [IP_ADDRESS_WEBSERVER]` (misal: listener 1883 127.0.0.1)
        //    - Restart mosquitto: `sudo systemctl restart mosquitto`
        //    - Biarkan kode di bawah apa adanya (tanpa -u dan -P).
        //
        // OPSI 2: Buat User Khusus Panel Web (Lebih aman)
        //    - Buat user baru di Mosquitto khusus untuk panel web ini:
        //      `sudo mosquitto_passwd -b /etc/mosquitto/passwordfile nama_user_panel password_panel`
        //    - Tambahkan ACL untuk user ini di file ACL (/etc/mosquitto/aclfile):
        //      ```
        //      user nama_user_panel
        //      topic write #  # Atau batasi ke topic tertentu jika perlu
        //      ```
        //    - Reload mosquitto: `sudo systemctl reload mosquitto`
        //    - Tambahkan kredensial ini ke file `.env`:
        //      ```
        //      MQTT_PANEL_USERNAME=nama_user_panel
        //      MQTT_PANEL_PASSWORD=password_panel
        //      ```
        //    - Gunakan kode `$command[] = '-u'; ...` di bawah ini.
        //
        // OPSI 3: Gunakan Library PHP MQTT Client (Paling Fleksibel, Perlu Installasi)
        //    - Install library seperti `php-mqtt/client`: `composer require php-mqtt/client`
        //    - Ubah kode ini untuk menggunakan library tersebut. Perlu penyesuaian logika.
        // =================================================================

        try {
            $command = [
                'mosquitto_pub',
                '-h', $brokerHost,
                '-p', $brokerPort,
                '-t', $validated['topic'],
                '-m', $validated['message'],
                '-r', // Retained message
            ];

            // --- UNCOMMENT Bagian ini jika Anda memilih OPSI 2 (User Khusus Panel) ---
             $panelUser = env('MQTT_PANEL_USERNAME');
             $panelPass = env('MQTT_PANEL_PASSWORD');

             if ($panelUser && $panelPass) {
                 $command[] = '-u';
                 $command[] = $panelUser;
                 $command[] = '-P';
                 $command[] = $panelPass;
                 Log::info('Mencoba publish MQTT dengan user panel.');
             } else {
                 Log::warning('Mencoba publish MQTT tanpa autentikasi (anonymous). Pastikan broker mengizinkannya dari localhost.');
             }
            // --- END UNCOMMENT ---


            // Jalankan proses
            // $result = Process::run($command); // Versi lama
             // Versi baru dengan timeout dan path eksplisit (lebih aman)
             $result = Process::path(dirname(shell_exec('which mosquitto_pub'))) // Cari path mosquitto_pub
                 ->timeout(10) // Timeout 10 detik
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