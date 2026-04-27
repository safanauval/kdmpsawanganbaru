<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokBarang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stok_barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'stok',
        'harga_beli',
        'harga_jual',
        'satuan',
        'deskripsi',
        'gambar',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Accessor untuk mendapatkan URL data gambar (base64).
     */
    public function getGambarUrlAttribute(): ?string
    {
        if (empty($this->gambar)) {
            return null;
        }

        // Deteksi MIME type dari binary data
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_buffer($finfo, $this->gambar);
        finfo_close($finfo);

        // Fallback jika gagal mendeteksi
        if (!$mime) {
            $mime = 'image/jpeg';
        }

        $base64 = base64_encode($this->gambar);
        return "data:{$mime};base64,{$base64}";
    }
}