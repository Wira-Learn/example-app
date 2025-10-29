<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AuthController; // <-- 1. Tambahkan import AuthController

// Rute untuk halaman utama
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// --- RUTE UNTUK TAMU (Guest) ---
// Rute ini hanya bisa diakses jika pengguna BELUM login
Route::middleware(['guest'])->group(function () {
    // Menampilkan form login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.show');
    // Memproses data login
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    // Menampilkan form register
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register.show');
    // Memproses data register
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// --- RUTE UNTUK PENGGUNA YANG SUDAH LOGIN ---
// Rute ini hanya bisa diakses jika pengguna SUDAH login
Route::middleware(['auth'])->group(function () {

    // Rute Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rute Dashboard
    Route::get('/dashboard', function () {
        // Ambil perangkat hanya untuk pengguna yang sedang login
        $devices = auth()->user()->devices;
        return view('dashboard', ['devices' => $devices]);
    })->name('dashboard');

    // Rute CRUD Devices
    // Menampilkan form tambah device
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    // Menyimpan device baru
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');

    // Menampilkan form edit device
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    // Menyimpan update device
    Route::patch('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');

    // Menghapus device
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');

    // Rute untuk publish MQTT (sudah ada di controller)
    Route::post('/devices/publish', [DeviceController::class, 'publishMessage'])->name('devices.publish');
});
