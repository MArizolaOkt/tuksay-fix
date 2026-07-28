<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_belanja_po', function (Blueprint $table) {
            $table->foreignId('daftar_belanja_id')
                  ->constrained('daftar_belanjas')
                  ->onDelete('cascade');
            $table->foreignId('purchase_order_id')
                  ->constrained('purchase_orders')
                  ->onDelete('cascade');
            $table->primary(['daftar_belanja_id', 'purchase_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_belanja_po');
    }
};
