<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum status: tambahkan 'menunggu_pembayaran' setelah 'proses'
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('baru', 'proses', 'menunggu_pembayaran', 'selesai') NOT NULL DEFAULT 'baru'");
    }

    public function down(): void
    {
        // Kembalikan ke enum semula
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('baru', 'proses', 'selesai') NOT NULL DEFAULT 'baru'");
    }
};
