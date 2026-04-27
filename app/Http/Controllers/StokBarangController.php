<?php

namespace App\Http\Controllers;

use App\Models\StokBarang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class StokBarangController extends Controller
{
    public function index()
    {
        $stokBarang = StokBarang::with('kategori')->latest()->paginate(10);
        return view('pages.admin.stok-barang.index', compact('stokBarang'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('pages.admin.stok-barang.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|unique:stok_barang,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // validasi gambar
        ]);

        // Proses upload gambar -> simpan sebagai binary string
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $validated['gambar'] = file_get_contents($file->getRealPath());
        }

        StokBarang::create($validated);

        return redirect()->route('stok-barang.index')
            ->with('success', 'Stok barang berhasil ditambahkan.');
    }

    public function show(StokBarang $stokBarang)
    {
        return view('pages.admin.stok-barang.show', compact('stokBarang'));
    }

    public function edit(StokBarang $stokBarang)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('pages.admin.stok-barang.edit', compact('stokBarang', 'kategoris'));
    }

    public function update(Request $request, StokBarang $stokBarang)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|unique:stok_barang,kode_barang,' . $stokBarang->id,
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Jika ada file gambar baru, hapus gambar lama (binary) dan simpan yang baru
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $validated['gambar'] = file_get_contents($file->getRealPath());
        } else {
            // Jika tidak upload gambar baru, jangan ubah kolom gambar
            unset($validated['gambar']);
        }

        $stokBarang->update($validated);

        return redirect()->route('stok-barang.index')
            ->with('success', 'Stok barang berhasil diperbarui.');
    }

    public function destroy(StokBarang $stokBarang)
    {
        // Karena gambar disimpan di database, tidak perlu menghapus file fisik
        $stokBarang->delete();
        return redirect()->route('stok-barang.index')
            ->with('success', 'Stok barang berhasil dihapus.');
    }
}