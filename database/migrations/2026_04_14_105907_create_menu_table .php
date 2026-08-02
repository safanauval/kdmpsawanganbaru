<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('menu', function (Blueprint $table) {
        $table->id();
        $table->string('kode_menu', 20)->unique();
        $table->string('nama_menu', 100);
        $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
        $table->integer('stok')->default(999);
        $table->decimal('harga_hpp', 15, 0)->default(0);
        $table->decimal('harga', 15, 0);
        $table->string('satuan', 50)->default('porsi');
        $table->integer('estimasi_waktu')->default(15);
        $table->text('deskripsi')->nullable();
        $table->binary('gambar')->required();
        $table->timestamps();
        $table->softDeletes();
    });

        // Ubah tipe kolom gambar menjadi LONGBLOB (khusus MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE menu MODIFY gambar LONGBLOB NULL");
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};