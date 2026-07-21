<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PinjamanController extends Controller
{
    public function index()
    {
        return view('pages.admin.pinjaman.index');
    }
}