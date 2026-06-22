<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Ubah customer_outlet_id menjadi nullable (untuk customer Catering yang tidak punya outlet)
            $table->foreignId('customer_outlet_id')->nullable()->change();

            // Tambah kolom nama_event (hanya diisi untuk customer Catering)
            $table->string('nama_event')->nullable()->after('customer_outlet_id');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('nama_event');
            $table->foreignId('customer_outlet_id')->nullable(false)->change();
        });
    }
};
