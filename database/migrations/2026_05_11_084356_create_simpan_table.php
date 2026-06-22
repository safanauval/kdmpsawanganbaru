<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // database/migrations/xxxx_create_simpans_table.php
        Schema::create('simpan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anggota')->nullable();
            $table->foreign('id_anggota')
                ->references('id_anggota')
                ->on('anggota')
                ->nullOnDelete();
            $table->enum('jenis_simpan', ['pokok', 'wajib']);
            $table->decimal('jumlah', 15, 0);
            $table->string('metode_pembayaran')->default('tunai');
            $table->date('tanggal');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('simpan');
    }
};
