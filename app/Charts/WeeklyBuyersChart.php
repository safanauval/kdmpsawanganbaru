<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;
use App\Models\Order;
use Carbon\Carbon;

class WeeklyBuyersChart
{
    public function build(): LarapexChart
    {
        // 1. Tentukan tanggal terakhir ada transaksi sukses
        $latestPaidDate = Order::where('payment_status', 'paid')
            ->max('created_at');

        // Fallback jika belum ada transaksi
        $endDate = $latestPaidDate
            ? Carbon::parse($latestPaidDate)->endOfDay()
            : Carbon::now()->endOfDay();

        $startDate = $endDate->copy()->subDays(6)->startOfDay();

        $labels = [];
        $data = [];

        // 2. Hitung jumlah order per hari
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $labels[] = $date->translatedFormat('D'); // Sen, Sel, ...

            $count = Order::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->count();

            $buyersCount[] = (int) $count;
        }

        // 3. Bangun area chart
        return (new LarapexChart)->areaChart()
            ->setHeight(250)
            ->setTitle('Jumlah Pembeli Mingguan')
            ->setSubtitle('Transaksi sukses dalam 7 hari terakhir')
            ->setDataset([['name' => 'Pembeli', 'data' => $buyersCount]])
            ->setLabels($labels)
            ->setColors(['#10b981'])
            ->setMarkers(['#10b981'], 5, 5)
            ->setGrid('#e5e7eb', 0.3)
            ->setStroke(3);
    }
}