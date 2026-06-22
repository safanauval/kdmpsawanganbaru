<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDiscounts extends Model
{
    protected $fillable = ['stok_barang_id', 'harga_diskon', 'is_active'];

    public function stokBarang()
    {
        return $this->belongsTo(StokBarang::class);
    }
}