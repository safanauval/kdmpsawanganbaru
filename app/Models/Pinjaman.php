<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjaman';

    protected $fillable = [
        'kode_pinjaman',
        'id_anggota',
        'jumlah_pinjaman',
        'bunga_persen',
        'tenor',
        'angsuran_per_bulan',
        'total_pengembalian',
        'sisa_pinjaman',
        'status',
        'tanggal_pinjaman',
        'tanggal_jatuh_tempo',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pinjaman'    => 'date',
        'tanggal_jatuh_tempo' => 'date',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function angsurans()
    {
        return $this->hasMany(Angsuran::class, 'pinjaman_id');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->tanggal_pinjaman->translatedFormat('d M Y');
    }

    public function getJumlahRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_pinjaman, 0, ',', '.');
    }
}