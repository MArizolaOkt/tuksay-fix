<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_belis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('harga_beli', 15, 2);
            $table->unique(['barang_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_belis');
    }
};
