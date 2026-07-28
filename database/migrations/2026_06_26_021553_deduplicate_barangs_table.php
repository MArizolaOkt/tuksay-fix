<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Find duplicates: for each nama, keep the row with the smallest id.
        $duplicates = DB::table('barangs')
            ->select('nama', DB::raw('MIN(id) as keep_id'))
            ->groupBy('nama')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            // Get all IDs for this nama except the one we keep
            $idsToRemove = DB::table('barangs')
                ->where('nama', $dup->nama)
                ->where('id', '!=', $dup->keep_id)
                ->pluck('id');

            foreach ($idsToRemove as $oldId) {
                // Reassign po_items from duplicate to the kept barang
                DB::table('po_items')
                    ->where('barang_id', $oldId)
                    ->update(['barang_id' => $dup->keep_id]);

                // Reassign harga_belis — handle unique constraint (barang_id, tanggal)
                // by keeping the existing record and deleting the conflicting one
                $hargaBelis = DB::table('harga_belis')
                    ->where('barang_id', $oldId)
                    ->get();

                foreach ($hargaBelis as $hb) {
                    $exists = DB::table('harga_belis')
                        ->where('barang_id', $dup->keep_id)
                        ->where('tanggal', $hb->tanggal)
                        ->exists();

                    if ($exists) {
                        // Already has a price for this date on the kept barang, delete the dup
                        DB::table('harga_belis')->where('id', $hb->id)->delete();
                    } else {
                        // Move it over
                        DB::table('harga_belis')
                            ->where('id', $hb->id)
                            ->update(['barang_id' => $dup->keep_id]);
                    }
                }

                // Delete the duplicate barang
                DB::table('barangs')->where('id', $oldId)->delete();
            }
        }

        // 2. Add unique index to prevent future duplicates
        Schema::table('barangs', function (Blueprint $table) {
            $table->unique('nama');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropUnique(['nama']);
        });
    }
};
