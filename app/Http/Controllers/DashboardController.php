<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\StokBarang;
use App\Models\Kategori;
use App\Models\User;
use App\Models\Order;
use App\Charts\WeeklyBuyersChart;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(WeeklyBuyersChart $chart)
    {
        // Statistik utama
        $totalTransaction = Order::where('payment_status', 'paid')->count();
        $totalCategories = Kategori::count();
        $totalAnggota = Anggota::count();
        $inStock = StokBarang::where('stok', '>', 10)->count();
        $lowStock = StokBarang::where('stok', '<=', 10)->where('stok', '>', 0)->count();
        $outOfStock = StokBarang::where('stok', '=', 0)->count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total');
        $totalExpenses = 0; // Placeholder, bisa dihitung dari pembelian atau biaya lainnya
        $inStockProducts = StokBarang::with('kategori')->where('stok', '>', 10)->orderBy('stok')->get();
        $lowStockProducts = StokBarang::with('kategori')->where('stok', '<=', 10)->where('stok', '>', 0)->orderBy('stok')->get();
        $outOfStockProducts = StokBarang::with('kategori')->where('stok', '=', 0)->orderBy('stok')->get();
        $latestProducts = StokBarang::with('kategori')->latest()->limit(3)->get();
        $latestUsers = User::latest()->limit(3)->get();

        // 🔧 BUILDER CHART & KIRIM KE VIEW
        $chart = $chart->build();  // <-- Tambahkan ini

        return view('pages.admin.dashboard', compact(
            'totalTransaction',
            'totalCategories',
            'totalAnggota',
            'inStock',
            'lowStock',
            'outOfStock',
            'totalRevenue',
            'totalExpenses',
            'inStockProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'latestProducts',
            'latestUsers',
            'chart'  // <-- Sertakan chart
        ));
    }

    // Method chart() ini sebenarnya tidak diperlukan lagi, 
    // karena chart sudah di-handle di index.
    // Bisa dihapus atau digunakan untuk halaman chart terpisah.
    public function chart(WeeklyBuyersChart $chart)
    {
        return view('pages.dashboard', ['chart' => $chart->build()]);
    }
}