<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class AdminKasirController extends Controller
{
    public function index()
    {
        return view('pages.admin.kasir.index');
    }

}
