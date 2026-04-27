<?php

namespace App\Http\Controllers;

use App\Models\StokBarang;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalProducts = StokBarang::count();
        $totalCategories = Kategori::count();
        $totalUsers = User::count();
        $lowStock = StokBarang::where('stok', '<=', 10)->count();

        // Data untuk grafik penjualan (contoh data dummy, bisa diganti dengan data transaksi riil)
        $salesChartData = [
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'data' => [150000, 230000, 180000, 290000, 320000, 410000, 380000],
        ];

        // Produk dengan stok menipis (<= 10)
        $lowStockProducts = StokBarang::with('kategori')
            ->where('stok', '<=', 10)
            ->orderBy('stok')
            ->limit(5)
            ->get();

        // Produk terbaru
        $latestProducts = StokBarang::with('kategori')
            ->latest()
            ->limit(5)
            ->get();

        // User terbaru (khusus admin)
        $latestUsers = User::latest()->limit(5)->get();

        return view('pages.admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalUsers',
            'lowStock',
            'salesChartData',
            'lowStockProducts',
            'latestProducts',
            'latestUsers'
        ));
    }

}