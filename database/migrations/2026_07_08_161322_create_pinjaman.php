<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Pinjaman
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjaman', 20)->unique();
            $table->unsignedBigInteger('id_anggota');
            $table->foreign('id_anggota')
                ->references('id_anggota')
                ->on('anggota')
                ->cascadeOnDelete();
            $table->decimal('jumlah_pinjaman', 15, 0);
            $table->decimal('bunga_persen', 5, 2)->default(2)->comment('Bunga per bulan dalam persen');
            $table->integer('tenor')->comment('Jumlah bulan angsuran');
            $table->decimal('angsuran_per_bulan', 15, 0);
            $table->decimal('total_pengembalian', 15, 0);
            $table->decimal('sisa_pinjaman', 15, 0);
            $table->enum('status', ['aktif', 'lunas', 'macet'])->default('aktif');
            $table->date('tanggal_pinjaman');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel Angsuran (Pembayaran Cicilan)
        Schema::create('angsuran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinjaman_id');
            $table->foreign('pinjaman_id')
                ->references('id')
                ->on('pinjaman')
                ->cascadeOnDelete();
            $table->integer('angsuran_ke');
            $table->decimal('jumlah_bayar', 15, 0);
            $table->decimal('sisa_pinjaman', 15, 0)->comment('Sisa pinjaman setelah pembayaran');
            $table->enum('payment_method', ['tunai', 'non-tunai'])->default('tunai');
            $table->date('tanggal_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsuran');
        Schema::dropIfExists('pinjaman');
    }
};