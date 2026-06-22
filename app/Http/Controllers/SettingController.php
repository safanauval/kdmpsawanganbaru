<?php

namespace App\Http\Controllers;

use App\Models\ProductDiscounts;
use App\Models\Setting;
use App\Models\ProductDiscount;
use App\Models\StokBarang;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil pengaturan yang sudah ada
        $memberDiscount = Setting::getValue('member_discount', 0);
        $nonMemberDiscount = Setting::getValue('non_member_discount', 0);
        $address = Setting::getValue('address', '');
        $phone = Setting::getValue('phone', '');

        // Produk yang sudah diberi diskon
        $discountedProducts = ProductDiscounts::with('stokBarang')->get();

        // Semua produk (untuk menambah diskon baru)
        $products = StokBarang::orderBy('nama_barang')->get();

        return view('pages.admin.settings.index', compact(
            'memberDiscount',
            'nonMemberDiscount',
            'address',
            'phone',
            'discountedProducts',
            'products'
        ));
    }

    // Update diskon & info toko
    public function update(Request $request)
    {
        $request->validate([
            'member_discount' => 'required|numeric|min:0|max:100',
            'non_member_discount' => 'required|numeric|min:0|max:100',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
        ]);

        Setting::setValue('member_discount', $request->member_discount);
        Setting::setValue('non_member_discount', $request->non_member_discount);
        Setting::setValue('address', $request->address);
        Setting::setValue('phone', $request->phone);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    // Tambah diskon produk
    public function addProductDiscount(Request $request)
    {
        $request->validate([
            'stok_barang_id' => 'required|exists:stok_barang,id',
            'harga_diskon' => 'required|numeric|min:0|max:100',
        ]);

        ProductDiscounts::updateOrCreate(
            ['stok_barang_id' => $request->stok_barang_id],
            ['harga_diskon' => $request->harga_diskon, 'is_active' => true]
        );

        return redirect()->back()->with('success', 'Diskon produk berhasil ditambahkan.');
    }

    // Hapus diskon produk
    public function removeProductDiscount($id)
    {
        ProductDiscounts::destroy($id);
        return redirect()->back()->with('success', 'Diskon produk dihapus.');
    }
}