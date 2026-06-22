<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';

    protected $fillable = [
        'kode_anggota',
        'nama_anggota',
        'email_anggota',
        'telepon_anggota',
        'alamat_anggota',
        'tanggal_masuk',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    public function simpanan()
    {
        return $this->hasMany(Simpan::class, 'id_anggota', 'id_anggota');
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->tanggal_masuk;
    }
}