<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_belanjas', function (Blueprint $table) {
            $table->id();
            $table->string('no_db')->unique(); // Format: DB-{TANGGAL}-{NOMOR}
            $table->date('tanggal');           // Tanggal belanja (= tanggal_kirim PO)
            $table->decimal('total_modal', 15, 2)->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_belanjas');
    }
};
