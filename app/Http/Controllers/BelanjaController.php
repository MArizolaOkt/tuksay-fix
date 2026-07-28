<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DaftarBelanja;
use App\Models\DaftarBelanjaItem;
use App\Models\HargaBeli;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BelanjaController extends Controller
{
    /**
     * CTRL-002: List semua record Daftar Belanja, diurutkan tanggal descending.
     */
    public function index(Request $request)
    {
        $query = DaftarBelanja::withCount('purchaseOrders')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('search')) {
            $query->where('no_db', 'like', '%' . $request->search . '%');
        }

        $daftarBelanjas = $query->paginate(15)->withQueryString();

        $totalRecord = DaftarBelanja::count();

        return view('belanja.index', compact('daftarBelanjas', 'totalRecord'));
    }

    /**
     * CTRL-002: Detail satu record Daftar Belanja.
     */
    public function show(DaftarBelanja $daftarBelanja)
    {
        $daftarBelanja->load([
            'items.barang',
            'purchaseOrders.customer',
            'purchaseOrders.outlet',
        ]);

        return view('belanja.show', compact('daftarBelanja'));
    }

    /**
     * CTRL-002: Konsolidasi belanja dari semua PO berstatus 'baru'/'proses'
     * Aggregasi total qty per barang, breakdown per outlet
     */
    public function konsolidasi(Request $request)
    {
        $tanggalDiminta = $request->get('tanggal', today()->toDateString());
        $isAutoFallback = false;

        // Cek apakah ada PO pada tanggal yang diminta
        $adaPoHariIni = PurchaseOrder::whereIn('status', ['baru', 'proses'])
            ->whereDate('tanggal_kirim', $tanggalDiminta)
            ->exists();

        // Jika tidak ada PO dan ini adalah load pertama (bukan dari form submit),
        // cari tanggal terdekat yang punya PO
        if (!$adaPoHariIni && !$request->has('tanggal')) {
            $activeDates = PurchaseOrder::whereIn('status', ['baru', 'proses'])
                ->whereNotNull('tanggal_kirim')
                ->distinct()
                ->pluck('tanggal_kirim');

            $tanggalFallback = $activeDates->sortBy(function ($date) use ($tanggalDiminta) {
                $timestamp = $date instanceof \Carbon\Carbon ? $date->timestamp : strtotime($date);
                return abs($timestamp - strtotime($tanggalDiminta));
            })->first();

            if ($tanggalFallback) {
                $tanggal = $tanggalFallback instanceof \Carbon\Carbon ? $tanggalFallback->toDateString() : $tanggalFallback;
                $isAutoFallback = true;
            } else {
                $tanggal = $tanggalDiminta;
            }
        } else {
            $tanggal = $tanggalDiminta;
        }

        // Ambil semua PO berstatus 'baru'/'proses' pada tanggal tersebut
        // LEFT JOIN agar PO catering (tanpa outlet) tetap masuk
        $konsolidasi = DB::table('po_items')
            ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('barangs', 'po_items.barang_id', '=', 'barangs.id')
            ->join('customers', 'purchase_orders.customer_id', '=', 'customers.id')
            ->leftJoin('customer_outlets', 'purchase_orders.customer_outlet_id', '=', 'customer_outlets.id')
            ->where('purchase_orders.tanggal_kirim', $tanggal)
            ->whereIn('purchase_orders.status', ['baru', 'proses'])
            ->select(
                'barangs.id as barang_id',
                'barangs.nama as barang_nama',
                'barangs.satuan',
                'barangs.harga_jual',
                DB::raw('SUM(po_items.qty) as total_qty'),
                DB::raw('GROUP_CONCAT(
                    COALESCE(customer_outlets.nama_outlet, customers.nama), \': \',
                    CAST(ROUND(po_items.qty, 2) + 0 AS CHAR), \' \', barangs.satuan
                    ORDER BY COALESCE(customer_outlets.nama_outlet, customers.nama)
                    SEPARATOR \' | \'
                ) as outlet_breakdown')
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
            $item->harga_jual = $item->harga_beli ? $item->harga_beli / 0.7 : 0;
            return $item;
        });

        // Summary totals
        $totalModal   = $konsolidasi->whereNotNull('harga_beli')->sum('total_modal');
        $totalRevenue = $konsolidasi->sum(fn($i) => $i->total_qty * $i->harga_jual);

        // Cek apakah sudah ada record DB untuk tanggal ini
        $recordBelanja = DaftarBelanja::whereDate('tanggal', $tanggal)->latest()->first();

        return view('belanja.konsolidasi', compact(
            'konsolidasi',
            'tanggal',
            'totalModal',
            'totalRevenue',
            'isAutoFallback',
            'recordBelanja'
        ));
    }

    /**
     * CTRL-002: Input/update harga beli (batch) + simpan record Daftar Belanja
     */
    public function inputHarga(Request $request)
    {
        $request->validate([
            'tanggal'           => 'required|date',
            'harga'             => 'required|array',
            'harga.*.barang_id' => 'required|exists:barangs,id',
            'harga.*.harga_beli' => 'required|numeric|min:0',
        ]);

        $tanggal = $request->tanggal;

        DB::transaction(function () use ($request, $tanggal) {
            // 1. Simpan/update harga beli (perilaku lama)
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

            // 2. Ambil semua PO aktif pada tanggal ini
            $posAktif = PurchaseOrder::whereIn('status', ['baru', 'proses'])
                ->whereDate('tanggal_kirim', $tanggal)
                ->pluck('id');

            if ($posAktif->isEmpty()) {
                return; // Tidak ada PO, skip pembuatan record
            }

            // 3. Ambil konsolidasi item dengan harga beli terbaru
            $konsolidasi = DB::table('po_items')
                ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
                ->join('barangs', 'po_items.barang_id', '=', 'barangs.id')
                ->join('customers', 'purchase_orders.customer_id', '=', 'customers.id')
                ->leftJoin('customer_outlets', 'purchase_orders.customer_outlet_id', '=', 'customer_outlets.id')
                ->where('purchase_orders.tanggal_kirim', $tanggal)
                ->whereIn('purchase_orders.status', ['baru', 'proses'])
                ->select(
                    'barangs.id as barang_id',
                    'barangs.harga_jual',
                    DB::raw('SUM(po_items.qty) as total_qty'),
                    DB::raw('GROUP_CONCAT(
                        COALESCE(customer_outlets.nama_outlet, customers.nama), \': \',
                        CAST(ROUND(po_items.qty, 2) + 0 AS CHAR), \' \', barangs.satuan
                        ORDER BY COALESCE(customer_outlets.nama_outlet, customers.nama)
                        SEPARATOR \' | \'
                    ) as outlet_breakdown')
                )
                ->groupBy('barangs.id', 'barangs.harga_jual')
                ->get();

            // 4. Ambil harga beli yang baru saja disimpan
            $hargaBeliMap = HargaBeli::where('tanggal', $tanggal)
                ->pluck('harga_beli', 'barang_id');

            // 5. Hitung totals
            $totalModal   = 0;
            $totalRevenue = 0;

            foreach ($konsolidasi as $item) {
                $hargaBeli = $hargaBeliMap->get($item->barang_id) ?? 0;
                if ($hargaBeli > 0) {
                    $totalModal += $hargaBeli * $item->total_qty;
                }
                $hargaJual = $hargaBeli > 0 ? $hargaBeli / 0.7 : 0;
                $totalRevenue += $hargaJual * $item->total_qty;
                
                // Set harga_jual pada item agar tersimpan di DaftarBelanjaItem
                $item->harga_jual = $hargaJual;
            }

            // 6. Buat atau update record DaftarBelanja untuk tanggal ini
            $daftarBelanja = DaftarBelanja::firstOrCreate(
                ['tanggal' => $tanggal],
                ['total_modal' => $totalModal, 'total_revenue' => $totalRevenue]
            );

            // Jika sudah ada, update totals
            if (!$daftarBelanja->wasRecentlyCreated) {
                $daftarBelanja->update([
                    'total_modal'   => $totalModal,
                    'total_revenue' => $totalRevenue,
                ]);
            }

            // 7. Sync PO yang terlibat ke pivot table
            $daftarBelanja->purchaseOrders()->sync($posAktif->toArray());

            // 8. Hapus dan recreate items (supaya selalu fresh)
            $daftarBelanja->items()->delete();

            foreach ($konsolidasi as $item) {
                DaftarBelanjaItem::create([
                    'daftar_belanja_id' => $daftarBelanja->id,
                    'barang_id'         => $item->barang_id,
                    'total_qty'         => $item->total_qty,
                    'harga_beli'        => $hargaBeliMap->get($item->barang_id),
                    'harga_jual'        => $item->harga_jual,
                    'outlet_breakdown'  => $item->outlet_breakdown,
                ]);
            }
        });

        return redirect()->route('belanja.konsolidasi', ['tanggal' => $tanggal])
            ->with('success', 'Harga beli berhasil disimpan dan Daftar Belanja telah direkam.');
    }
}
