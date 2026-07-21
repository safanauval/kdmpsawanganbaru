<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('user_name', 50)->nullable();
            $table->unsignedBigInteger('id_anggota')->nullable();
            $table->foreign('id_anggota')
                ->references('id_anggota')
                ->on('anggota')
                ->nullOnDelete();
            $table->string('nama_pelanggan', 100)->nullable();
            $table->decimal('discount_amount', 12, 2)->nullable();
            $table->decimal('total', 12, 2);
            $table->enum('payment_method', ['tunai', 'non-tunai'])->default('tunai');
            $table->decimal('payment_amount', 12, 0)->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('snap_token')->nullable();
            $table->json('cart_items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};