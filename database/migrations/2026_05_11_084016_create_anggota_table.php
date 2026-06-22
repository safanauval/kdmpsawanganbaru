<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->id('id_anggota')->unique();
            $table->string('kode_anggota', 20)->unique();
            $table->string('nama_anggota', 80);
            $table->string('email_anggota', 100)->nullable()->unique();
            $table->string('telepon_anggota', 15)->nullable();
            $table->text('alamat_anggota')->nullable();
            $table->date('tanggal_masuk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
