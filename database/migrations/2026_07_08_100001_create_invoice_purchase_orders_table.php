<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->unique(['invoice_id', 'purchase_order_id']);
            $table->timestamps();
        });

        // Migrasi data lama: asosiasikan invoice dengan PO customer yang sudah di-invoice
        // Untuk setiap invoice, cari PO yang berstatus 'menunggu_pembayaran' atau 'selesai'
        // dari customer yang sama, lalu masukkan ke tabel pivot.
        $invoices = DB::table('invoices')->get();
        foreach ($invoices as $invoice) {
            $pos = DB::table('purchase_orders')
                ->where('customer_id', $invoice->customer_id)
                ->whereIn('status', ['menunggu_pembayaran', 'selesai'])
                ->get();

            foreach ($pos as $po) {
                // Cegah duplikat jika ada beberapa invoice untuk customer yang sama
                $exists = DB::table('invoice_purchase_orders')
                    ->where('purchase_order_id', $po->id)
                    ->exists();

                if (!$exists) {
                    DB::table('invoice_purchase_orders')->insert([
                        'invoice_id'         => $invoice->id,
                        'purchase_order_id'  => $po->id,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_purchase_orders');
    }
};
