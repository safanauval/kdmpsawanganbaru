<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Kategori;

class MenuController extends Controller
{
    /**
     * Menampilkan halaman kelola menu resto.
     */
    public function index()
    {
        // Mengambil data kategori jika diperlukan untuk filter atau informasi tambahan
        $kategoris = Kategori::orderBy('nama')->get();

        return view('pages.kasir.menu.index', compact('kategoris'));
    }
}