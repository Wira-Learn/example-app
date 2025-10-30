<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pengaturan Broker MQTT
    |--------------------------------------------------------------------------
    |
    | Ini adalah pengaturan untuk koneksi ke broker MQTT
    | yang akan ditampilkan di dashboard pengguna.
    |
    */
    'broker_host'   => env('MQTT_BROKER_HOST', 'mqtt.layanan-anda.com'),
    'port_unsecure' => env('MQTT_PORT_UNSECURE', 1883),
    'port_secure'   => env('MQTT_PORT_SECURE', 8883),

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Mosquitto (Broker Lokal)
    |--------------------------------------------------------------------------
    |
    | Path ini digunakan oleh DeviceController untuk memanipulasi
    | file password dan ACL Mosquitto.
    |
    */
    'mosquitto'     => [
        'password_file' => env('MOSQUITTO_PASSWD_FILE', '/etc/mosquitto/passwordfile'),
        'acl_file'      => env('MOSQUITTO_ACL_FILE', '/etc/mosquitto/aclfile'),
    ],

];
