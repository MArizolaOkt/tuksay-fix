<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            // Ubah customer_outlet_id menjadi nullable (untuk customer Catering yang tidak punya outlet)
            $table->foreignId('customer_outlet_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->foreignId('customer_outlet_id')->nullable(false)->change();
        });
    }
};
