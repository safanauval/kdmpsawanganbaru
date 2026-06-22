<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiwayatTransaksiKsrController extends Controller
{
    public function index()
    {
        return view('pages.kasir.riwayat-transaksi.index');
    }
    public function cetak()
    {
        return view('pages.kasir.riwayat-transaksi.cetak');
    }
}