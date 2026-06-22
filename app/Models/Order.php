<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected $casts = [
        'cart_items' => 'array',
        'total' => 'decimal:2',
    ];


    public function getTotalRupiahAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total, 0, ',', '.');
    }

    public function getTotalItemsAttribute(): int
    {
        return collect($this->cart_items)->sum('quantity');
    }


    public function getProductNamesAttribute(): string
    {
        return collect($this->cart_items)->pluck('name')->implode(', ');
    }


    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->translatedFormat('d M Y');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }


    public function scopeYesterday($query)
    {
        return $query->whereDate('created_at', today()->subDay());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}