<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_operasionals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_biaya');
            $table->enum('kategori', ['Transport', 'Packaging', 'Komunikasi', 'Tak Terduga', 'Lain-lain']);
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_operasionals');
    }
};
