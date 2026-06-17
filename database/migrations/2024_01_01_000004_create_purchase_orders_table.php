<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique();
            $table->string('no_ref')->nullable();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('customer_outlet_id')->constrained('customer_outlets')->onDelete('restrict');
            $table->date('tanggal');
            $table->enum('status', ['baru', 'proses', 'selesai'])->default('baru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
