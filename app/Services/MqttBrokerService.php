<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

/**
 * Kelas ini bertanggung jawab untuk semua interaksi
 * dengan broker Mosquitto di level sistem.
 */
class MqttBrokerService
{
    protected string $passwordFile;
    protected string $aclFile;

    public function __construct()
    {
        // Ambil path dari file konfigurasi yang kita buat di Poin 2
        $this->passwordFile = config('mqtt.mosquitto.password_file');
        $this->aclFile = config('mqtt.mosquitto.acl_file');
    }

    /**
     * Memprovisi perangkat baru di Mosquitto.
     * Menambahkan user, meregenerasi ACL, dan me-reload broker.
     *
     * @throws \Illuminate\Process\Exceptions\ProcessFailedException
     */
    public function provisionDevice(Device $device, string $password): void
    {
        // 1. Tambahkan pengguna ke Mosquitto Password File
        $passwordCmd = "sudo mosquitto_passwd -b {$this->passwordFile} {$device->mqtt_username} {$password}";
        Process::run($passwordCmd)->throw(); // ->throw() akan melempar Exception jika gagal

        // 2. Regenerasi dan Tulis Ulang File ACL
        $this->regenerateAclFile();

        // 3. Reload layanan Mosquitto
        Process::run('sudo systemctl reload mosquitto')->throw();
    }

    /**
     * Menghapus provisi perangkat dari Mosquitto.
     * Menghapus user, meregenerasi ACL, dan me-reload broker.
     *
     * @throws \Illuminate\Process\Exceptions\ProcessFailedException
     */
    public function deprovisionDevice(Device $device): void
    {
        // 1. Hapus pengguna dari Mosquitto Password File
        $passwordCmd = "sudo mosquitto_passwd -D {$this->passwordFile} {$device->mqtt_username}";
        Process::run($passwordCmd)->throw();

        // 2. Regenerasi dan Tulis Ulang File ACL
        $this->regenerateAclFile();

        // 3. Reload layanan Mosquitto
        Process::run('sudo systemctl reload mosquitto')->throw();
    }

    /**
     * Helper private untuk meregenerasi seluruh file ACL.
     * Ini adalah fungsi yang sama persis dari controller,
     * sekarang dipindahkan ke sini.
     *
     * @throws \Illuminate\Process\Exceptions\ProcessFailedException
     */
    private function regenerateAclFile(): void
    {
        $aclContent = "";

        // Dapatkan *semua* perangkat dari database
        $allDevices = Device::all();

        foreach ($allDevices as $device) {
            $username = $device->mqtt_username;

            $aclContent .= "user {$username}\n";
            $aclContent .= "topic readwrite {$username}/#\n\n";
        }

        $tempFilePath = storage_path('app/temp_aclfile');
        File::put($tempFilePath, $aclContent);

        // Gunakan 'tee' dengan 'sudo' untuk menulis file sebagai root
        Process::run("sudo tee {$this->aclFile} < {$tempFilePath}")->throw();

        // Setel kepemilikan yang benar
        Process::run("sudo chown mosquitto:mosquitto {$this->aclFile}")->throw();

        // Hapus file sementara
        File::delete($tempFilePath);
    }
}
