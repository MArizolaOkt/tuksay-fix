<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Perubahan 1 — SKILL.md: Tambah tanggal_kirim (nullable, karena mungkin belum diketahui)
            $table->date('tanggal_kirim')->nullable()->after('tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('tanggal_kirim');
        });
    }
};
