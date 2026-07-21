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
        'jenis_simpanan',
        'jumlah',
        'payment_method',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi ke anggota
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    /**
     * Boot method untuk auto-update total_simpanan
     */
    protected static function boot()
    {
        parent::boot();

        // Sebelum membuat record baru
        static::creating(function ($simpan) {
            $simpan->total_simpanan = self::calculateNewTotal($simpan->id_anggota, $simpan->jumlah);
        });

        // Setelah record dibuat → update total untuk record ini dan record setelahnya (jika ada)
        static::created(function ($simpan) {
            self::recalculateAllTotals($simpan->id_anggota);
        });

        // Setelah record diupdate
        static::updated(function ($simpan) {
            self::recalculateAllTotals($simpan->id_anggota);
        });

        // Setelah record dihapus
        static::deleted(function ($simpan) {
            self::recalculateAllTotals($simpan->id_anggota);
        });
    }

    /**
     * Hitung total baru untuk record yang akan dibuat
     */
    private static function calculateNewTotal($anggotaId, $jumlahBaru): float
    {
        $lastRecord = self::where('id_anggota', $anggotaId)
            ->orderBy('id', 'desc')
            ->first();

        return ($lastRecord ? $lastRecord->total_simpanan : 0) + $jumlahBaru;
    }

    /**
     * Hitung ulang semua total_simpanan untuk anggota tertentu
     */
    private static function recalculateAllTotals($anggotaId)
    {
        $records = self::where('id_anggota', $anggotaId)
            ->orderBy('id', 'asc')
            ->get();

        $runningTotal = 0;
        foreach ($records as $record) {
            $runningTotal += $record->jumlah;
            // Update tanpa trigger event untuk menghindari infinite loop
            self::where('id', $record->id)->update(['total_simpanan' => $runningTotal]);
        }
    }

    // Accessor untuk format Rupiah
    public function getJumlahRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    public function getTotalSimpananRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->total_simpanan, 0, ',', '.');
    }
}