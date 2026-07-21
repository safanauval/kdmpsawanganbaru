<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    use HasFactory;

    protected $table = 'angsuran';

    protected $fillable = [
        'pinjaman_id',
        'angsuran_ke',
        'jumlah_bayar',
        'sisa_pinjaman',
        'payment_method',
        'tanggal_bayar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
    ];

    /**
     * Relasi ke tabel pinjaman
     */
    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }

    /**
     * Format jumlah bayar ke Rupiah
     */
    public function getJumlahBayarRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }

    /**
     * Format tanggal bayar
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->tanggal_bayar->translatedFormat('d M Y');
    }
}