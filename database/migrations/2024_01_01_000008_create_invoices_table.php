<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->date('tanggal');
            $table->decimal('total_tagihan', 15, 2)->default(0);
            $table->enum('status', ['terbit', 'lunas'])->default('terbit');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
