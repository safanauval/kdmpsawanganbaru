<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_tab', function (Blueprint $table) {
            $table->enum('tipe', ['toko', 'resto'])->default('toko');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_tab');
    }
};