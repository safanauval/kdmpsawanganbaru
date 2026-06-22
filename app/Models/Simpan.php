<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simpan extends Model
{
    use HasFactory;

    protected $table = 'simpan';

    protected $fillable = [
        'id_anggota',
        'nama_anggota',
        'jenis',
        'jumlah',
        'metode_pembayaran',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function getJumlahRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->tanggal;
    }
}