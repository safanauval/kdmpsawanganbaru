<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Kategori;

class AdminMenuController extends Controller
{
    /**
     * Menampilkan halaman kelola menu resto.
     */
    public function index()
    {
        // Mengambil data kategori jika diperlukan untuk filter atau informasi tambahan
        $kategoris = Kategori::orderBy('nama')->get();

        return view('pages.admin.menu.index', compact('kategoris'));
    }
}