<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menu';

    protected $fillable = [
        'kode_menu',
        'nama_menu',
        'kategori_id',
        'stok',
        'harga_hpp',
        'harga',
        'satuan',
        'estimasi_waktu',
        'deskripsi',
        'gambar',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected $casts = [
        'stok'           => 'integer',
        'harga_hpp'      => 'decimal:0',
        'harga'          => 'decimal:0',
        'estimasi_waktu' => 'integer',
    ];

    /**
     * Relasi ke Model Kategori.
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Accessor untuk mengonversi binary gambar (BLOB) ke Base64 Data URL.
     * Penggunaan di view Blade: $item->gambar_url
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