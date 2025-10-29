<?php

namespace App\Http\Controllers;


use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
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
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     //
    // }

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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Menghapus perangkat dari database dan Mosquitto.
     */
    public function destroy(Device $device)
    {
        // 1. Otorisasi: Pastikan pengguna hanya menghapus perangkat miliknya.
        // Ini sangat penting untuk keamanan!
        if ($device->user_id !== auth()->id()) {
            abort(403, 'TINDAKAN TIDAK DIIZINKAN');
        }

        // Simpan username MQTT sebelum kita hapus perangkat dari DB
        $mqtt_username = $device->mqtt_username;

        try {
            // 2. Hapus dari Database
            $device->delete();

            // 3. Hapus pengguna dari Mosquitto Password File
            // Kita menggunakan flag -D (Delete)
            $passwordCmd = "sudo mosquitto_passwd -D /etc/mosquitto/passwordfile {$mqtt_username}";
            Process::run($passwordCmd)->throw();

            // 4. Regenerasi dan Tulis Ulang File ACL
            // Metode ini (dari langkah sebelumnya) secara otomatis akan
            // mengabaikan perangkat yang sudah dihapus dari DB.
            $this->regenerateAclFile();

            // 5. Reload layanan Mosquitto
            Process::run('sudo systemctl reload mosquitto')->throw();
        } catch (\Exception $e) {
            // Jika gagal, kembalikan dengan pesan error
            // (Dalam skenario produksi, Anda mungkin ingin me-restore 
            //  data $device jika langkah Mosquitto gagal)
            return redirect()->route('dashboard')
                ->with('error', 'Gagal menghapus perangkat: ' . $e->getMessage());
        }

        // --- Selesai ---
        return redirect()->route('dashboard')
            ->with('success', "Perangkat '{$device->name}' berhasil dihapus.");
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
        $mqtt_password = Str::random(16); // Password kuat

        // 2. Simpan ke Database
        $device = Device::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'mqtt_username' => $mqtt_username,
        ]);

        // --- INTI LOGIKA MOSQUITTO ---

        try {
            // 3. Tambahkan pengguna ke Mosquitto Password File
            // Kita perlu 'sudo' karena file ini dimiliki oleh root/mosquitto
            $passwordCmd = "sudo mosquitto_passwd -b /etc/mosquitto/passwordfile {$mqtt_username} {$mqtt_password}";
            Process::run($passwordCmd)->throw();

            // 4. Regenerasi dan Tulis Ulang File ACL
            $this->regenerateAclFile();

            // 5. Reload layanan Mosquitto
            Process::run('sudo systemctl reload mosquitto')->throw();
        } catch (\Exception $e) {
            // Jika gagal, hapus perangkat dari DB agar konsisten
            $device->delete();
            // Kembalikan dengan pesan error
            return back()->with('error', 'Gagal memprovisi perangkat: ' . $e->getMessage());
        }

        // --- Selesai ---

        // Tampilkan kredensial kepada pengguna SEKALI SAJA
        // Gunakan flash session untuk ini
        return redirect()->route('dashboard') // Ganti ke rute dashboard Anda
            ->with('success', 'Perangkat berhasil dibuat!')
            ->with('new_device_credentials', [
                'name' => $device->name,
                'username' => $mqtt_username,
                'password' => $mqtt_password, // Tampilkan ini sekali saja!
                'publish_topic' => "{$mqtt_username}/data/out",
                'subscribe_topic' => "{$mqtt_username}/cmd/in",
            ]);
    }

    /**
     * Helper private untuk meregenerasi seluruh file ACL.
     */
    private function regenerateAclFile()
    {
        $aclContent = "";

        // Dapatkan *semua* perangkat dari database
        $allDevices = Device::all();

        foreach ($allDevices as $device) {
            $username = $device->mqtt_username;

            // Terapkan aturan ACL "terisolasi" per pengguna
            // Setiap perangkat hanya bisa publish/subscribe ke topiknya sendiri
            // yang diawali dengan username MQTT-nya.
            $aclContent .= "user {$username}\n";
            $aclContent .= "topic readwrite {$username}/#\n\n";
        }

        // Tulis ulang seluruh file ACL
        // Kita butuh 'sudo' untuk menulis ke /etc/mosquitto/
        // Cara yang lebih baik adalah menggunakan 'tee' seperti di bawah ini
        $tempFilePath = storage_path('app/temp_aclfile');
        File::put($tempFilePath, $aclContent);

        // Gunakan 'tee' dengan 'sudo' untuk menulis file sebagai root
        Process::run("sudo tee /etc/mosquitto/aclfile < {$tempFilePath}")->throw();

        // Setel kepemilikan yang benar
        Process::run("sudo chown mosquitto:mosquitto /etc/mosquitto/aclfile")->throw();

        // Hapus file sementara
        File::delete($tempFilePath);
    }
}
