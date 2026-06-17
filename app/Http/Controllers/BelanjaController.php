<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\HargaBeli;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BelanjaController extends Controller
{
    /**
     * CTRL-002: Konsolidasi belanja dari semua PO berstatus 'baru'
     * Aggregasi total qty per barang, breakdown per outlet
     */
    public function konsolidasi(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());

        // Ambil semua PO berstatus 'baru' pada tanggal tersebut
        $konsolidasi = DB::table('po_items')
            ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('barangs', 'po_items.barang_id', '=', 'barangs.id')
            ->join('customer_outlets', 'purchase_orders.customer_outlet_id', '=', 'customer_outlets.id')
            ->where('purchase_orders.tanggal', $tanggal)
            ->whereIn('purchase_orders.status', ['baru', 'proses'])
            ->select(
                'barangs.id as barang_id',
                'barangs.nama as barang_nama',
                'barangs.satuan',
                'barangs.harga_jual',
                DB::raw('SUM(po_items.qty) as total_qty'),
                DB::raw('GROUP_CONCAT(customer_outlets.nama_outlet, ": ", CAST(po_items.qty AS CHAR) ORDER BY customer_outlets.nama_outlet SEPARATOR " | ") as outlet_breakdown')
            )
            ->groupBy('barangs.id', 'barangs.nama', 'barangs.satuan', 'barangs.harga_jual')
            ->orderBy('barangs.nama')
            ->get();

        // Ambil harga beli hari ini jika ada
        $hargaBeliHariIni = HargaBeli::where('tanggal', $tanggal)
            ->pluck('harga_beli', 'barang_id');

        // Attach harga beli ke konsolidasi
        $konsolidasi = $konsolidasi->map(function ($item) use ($hargaBeliHariIni) {
            $item->harga_beli = $hargaBeliHariIni->get($item->barang_id, null);
            $item->total_modal = $item->harga_beli ? $item->harga_beli * $item->total_qty : null;
            return $item;
        });

        // Summary totals
        $totalModal = $konsolidasi->whereNotNull('harga_beli')->sum('total_modal');
        $totalRevenue = $konsolidasi->sum(fn($i) => $i->total_qty * $i->harga_jual);

        return view('belanja.konsolidasi', compact(
            'konsolidasi',
            'tanggal',
            'totalModal',
            'totalRevenue'
        ));
    }

    /**
     * CTRL-002: Input/update harga beli (batch)
     */
    public function inputHarga(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'harga'            => 'required|array',
            'harga.*.barang_id' => 'required|exists:barangs,id',
            'harga.*.harga_beli' => 'required|numeric|min:0',
        ]);

        $tanggal = $request->tanggal;

        DB::transaction(function () use ($request, $tanggal) {
            foreach ($request->harga as $entry) {
                HargaBeli::updateOrCreate(
                    [
                        'barang_id' => $entry['barang_id'],
                        'tanggal'   => $tanggal,
                    ],
                    [
                        'harga_beli' => $entry['harga_beli'],
                    ]
                );
            }
        });

        return redirect()->route('belanja.konsolidasi', ['tanggal' => $tanggal])
            ->with('success', 'Harga beli berhasil disimpan.');
    }
}
