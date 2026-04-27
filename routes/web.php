<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', fn() => view('pages.admin.dashboard'))->name('admin.dashboard');
    Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // {{ HALAMAN ADMIN }}

    // 1. stok Barang
    Route::get('/admin/stok-barang', [App\Http\Controllers\StokBarangController::class, 'index'])->name('stok-barang.index');
    Route::resource('stok-barang', App\Http\Controllers\StokBarangController::class);
    Route::get('/stok-barang', function () {
        return view('pages.admin.stok-barang.index');
    })->name('stok-barang.index');

    // 2. kategori
    Route::get('/admin/kategori', [App\Http\Controllers\KategoriController::class, 'index'])->name('kategori.index');
    Route::resource('kategori', App\Http\Controllers\KategoriController::class);
    Route::get('/admin/kategori', function () {
        return view('pages.admin.kategori.index');
    })->name('kategori.index');

    // 3. Users
    Route::get('/admin/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::middleware('can:admin')->group(function () {
        Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::put('/admin/users/{user}/role', [App\Http\Controllers\UserController::class, 'updateRole'])->name('users.update-role');
    });

    // 4. Riwayat Transaksi
    Route::get('/admin/riwayat-transaksi', [App\Http\Controllers\RiwayatTransaksiController::class, 'index'])->name('riwayat-transaksi.index');

    // {{ HALAMAN KASIR }}
    Route::get('/kasir', function () {
        return view('pages.kasir.index');
    })->name('kasir');
});

require __DIR__ . '/settings.php';
