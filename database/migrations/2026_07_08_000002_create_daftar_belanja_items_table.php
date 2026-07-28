<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_belanja_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daftar_belanja_id')
                  ->constrained('daftar_belanjas')
                  ->onDelete('cascade');
            $table->foreignId('barang_id')
                  ->constrained('barangs')
                  ->onDelete('restrict');
            $table->decimal('total_qty', 10, 3);
            $table->decimal('harga_beli', 15, 2)->nullable();
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->text('outlet_breakdown')->nullable(); // e.g. "Outlet A: 2 kg | Outlet B: 3 kg"
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_belanja_items');
    }
};
