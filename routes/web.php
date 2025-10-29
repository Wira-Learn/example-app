<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeviceController;

Route::middleware(['auth'])->group(function () {
    // Rute untuk menampilkan form dan menyimpan perangkat baru
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    // Tambahkan rute lain sesuai kebutuhan (index, show, destroy)
});

// Rute Dashboard Utama
Route::get('/dashboard', function () {
    // Ambil perangkat hanya untuk pengguna yang sedang login
    $devices = auth()->user()->devices;

    return view('dashboard', ['devices' => $devices]);
})->middleware(['auth'])->name('dashboard');


// Rute untuk mengelola Perangkat (Devices)
Route::middleware(['auth'])->group(function () {
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');

    // Nanti Anda bisa tambahkan ini:
    // Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');

    // TAMBAHKAN BARIS INI
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
});
