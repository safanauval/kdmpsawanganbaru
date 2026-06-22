<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('stok_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 20)->unique();
            $table->string('nama_barang', 100);
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
            $table->foreignId('gudang_id')->nullable()->constrained('gudang')->nullOnDelete();
            $table->integer('stok')->default(0);
            $table->decimal('harga_beli', 15, 0);
            $table->decimal('harga_jual', 15, 0);
            $table->string('satuan', 50)->default('pcs');
            $table->text('deskripsi')->nullable();
            $table->binary('gambar')->required();
            $table->timestamps();
            $table->softDeletes();
        });

        // Ubah tipe kolom gambar menjadi LONGBLOB (khusus MySQL)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE stok_barang MODIFY gambar LONGBLOB NULL");
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_barang');
    }
};