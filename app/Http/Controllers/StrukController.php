<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StrukController extends Controller
{
    // Method untuk menampilkan view cetak
    public function cetak($orderId)
    {
        $order = Order::where('order_id', $orderId)->firstOrFail();
        return view('pages.admin.struk-pdf', compact('order'));
    }


    // Method untuk generate PDF
    public function pdf($orderId)
    {
        $order = Order::where('order_id', $orderId)->firstOrFail();

        // Load view untuk PDF
        $pdf = Pdf::loadView('pages.admin.struk-pdf', compact('order'));

        // Stream ke browser untuk di-download atau ditampilkan
        return $pdf->stream('struk-' . $orderId . '.pdf');

        // Atau untuk download langsung:
        // return $pdf->download('struk-' . $orderId . '.pdf');
    }
}