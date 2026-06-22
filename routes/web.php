<?php

use App\Http\Controllers\{
    AnggotaController,
    DashboardController,
    GudangController,
    KategoriController,
    MidtransCallbackController,
    ProfileController,
    RiwayatTransaksiController,
    RiwayatTransaksiKsrController,
    SettingController,
    SimpananController,
    StokBarangController,
    StokBarangKsrController,
    UserController,
};
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // ========== RUTE ADMIN ==========
    Route::prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Resource CRUD untuk admin
        Route::resource('stok-barang', StokBarangController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('gudang', GudangController::class);

        // Users (hanya index & update role)
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::put('users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');

        // Riwayat Transaksi
        Route::get('riwayat-transaksi', [RiwayatTransaksiController::class, 'index'])->name('riwayat-transaksi.index');
        Route::get('riwayat-transaksi/cetak', [RiwayatTransaksiController::class, 'cetak'])->name('riwayat-transaksi.cetak');

        // Anggota
        Route::get('anggota', [AnggotaController::class, 'index'])->name('anggota.index');

        // Simpanan
        Route::get('simpanan', [SimpananController::class, 'index'])->name('simpanan.index');

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/discount', [SettingController::class, 'addProductDiscount'])->name('settings.discount.add');
        Route::delete('settings/discount/{id}', [SettingController::class, 'removeProductDiscount'])->name('settings.discount.remove');

        // Gudang
        Route::get('gudang', [GudangController::class, 'index'])->name('gudang.index');

        // Admin Kasir (jika diperlukan)
        Route::get('kasir', fn() => view('pages.admin.kasir.index'))->name('admin-kasir');
    });

    // ========== RUTE KASIR ==========
    Route::prefix('kasir')->group(function () {
        Route::get('/', fn() => view('pages.kasir.index'))->name('kasir');

        // Kasir hanya perlu melihat stok barang (read-only)
        Route::get('stok-barang', [StokBarangKsrController::class, 'index'])->name('stok-barang.kasir.index');
        Route::get('stok-barang/{stokBarang}', [StokBarangKsrController::class, 'show'])->name('stok-barang.kasir.show');

        // Riwayat Transaksi
        Route::get('riwayat-transaksi', [RiwayatTransaksiKsrController::class, 'index'])->name('riwayat-transaksi.kasir.index');
        Route::get('riwayat-transaksi/cetak', [RiwayatTransaksiKsrController::class, 'cetak'])->name('riwayat-transaksi.kasir.cetak');
    });

    // Rute umum
    Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handle'])->name('midtrans.callback');
});

require __DIR__ . '/settings.php';