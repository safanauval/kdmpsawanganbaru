<?php

namespace App\Http\Controllers;

use App\Models\ProductDiscounts;
use App\Models\Setting;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index()
    {
        return view('pages.admin.settings.index', [
            'company_name'       => Setting::getValue('company_name', 'Koperasi'),
            'member_discount'     => Setting::getValue('member_discount', 0),
            'non_member_discount'  => Setting::getValue('non_member_discount', 0),
            'footer_text'        => Setting::getValue('footer_text', ''),
            'address'            => Setting::getValue('address', ''),
            'phone'              => Setting::getValue('phone', ''),
            'discountedProducts' => ProductDiscounts::with('stokBarang')->get(),
            'products'           => StokBarang::orderBy('nama_barang')->get(),
        ]);
    }

    /**
     * Update pengaturan
     */
    public function update(Request $request)
    {
        // Validasi
        $request->validate([
            'company_name'        => 'nullable|string|max:255',
            'member_discount'     => 'nullable|numeric|min:0|max:100',
            'non_member_discount' => 'nullable|numeric|min:0|max:100',
            'address'             => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'footer_text'         => 'nullable|string|max:255',
        ]);

        // Cara 1: Gunakan updateOrCreate (PASTI BERFUNGSI)
        $settings = [
            'company_name'        => $request->company_name,
            'member_discount'     => $request->member_discount,
            'non_member_discount' => $request->non_member_discount,
            'address'             => $request->address,
            'phone'               => $request->phone,
            'footer_text'         => $request->footer_text,
        ];

        foreach ($settings as $key => $value) {
            if ($value !== null) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    /**
     * Tambah diskon produk
     */
    public function addProductDiscount(Request $request)
    {
        $request->validate([
            'stok_barang_id' => 'required|exists:stok_barang,id',
            'harga_diskon'   => 'required|numeric|min:0|max:100',
        ]);

        ProductDiscounts::updateOrCreate(
            ['stok_barang_id' => $request->stok_barang_id],
            [
                'harga_diskon' => $request->harga_diskon,
                'is_active'    => true,
            ]
        );

        return redirect()->back()->with('success', 'Diskon produk berhasil ditambahkan.');
    }

    /**
     * Hapus diskon produk
     */
    public function removeProductDiscount($id)
    {
        ProductDiscounts::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Diskon produk berhasil dihapus.');
    }
}