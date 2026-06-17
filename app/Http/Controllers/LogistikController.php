<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\SuratJalan;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogistikController extends Controller
{
    /**
     * CTRL-003: index - List semua surat jalan
     */
    public function index(Request $request)
    {
        $query = SuratJalan::with(['customer', 'outlet'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $query->where('no_sj', 'like', '%' . $request->search . '%')
                ->orWhereHas('customer', fn($q) => $q->where('nama', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }

        $suratJalans = $query->paginate(15)->withQueryString();

        return view('logistik.index', compact('suratJalans'));
    }

    /**
     * CTRL-003: generate - Buat SJ dari PO yang dipilih
     */
    public function generate(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
        ]);

        $po = PurchaseOrder::with(['customer', 'outlet', 'items.barang'])->findOrFail($request->purchase_order_id);

        if ($po->status !== 'baru') {
            return back()->with('error', 'Surat Jalan hanya bisa dibuat dari PO berstatus "baru".');
        }

        DB::transaction(function () use ($po) {
            $sj = SuratJalan::create([
                'customer_id'        => $po->customer_id,
                'customer_outlet_id' => $po->customer_outlet_id,
                'tanggal'            => $po->tanggal,
            ]);

            // Update status PO ke proses
            $po->update(['status' => 'proses']);
        });

        return redirect()->route('logistik.index')
            ->with('success', 'Surat Jalan berhasil dibuat. Status PO diubah ke "proses".');
    }

    /**
     * CTRL-003: show - Detail surat jalan
     */
    public function show(SuratJalan $suratJalan)
    {
        $suratJalan->load(['customer', 'outlet']);

        // Ambil PO yang relevan (customer + outlet + tanggal sama, status proses)
        $pos = PurchaseOrder::with(['items.barang'])
            ->where('customer_id', $suratJalan->customer_id)
            ->where('customer_outlet_id', $suratJalan->customer_outlet_id)
            ->where('tanggal', $suratJalan->tanggal)
            ->whereIn('status', ['proses', 'selesai'])
            ->get();

        return view('logistik.show', compact('suratJalan', 'pos'));
    }

    /**
     * CTRL-003: print - Print view A4
     */
    public function print(SuratJalan $suratJalan)
    {
        $suratJalan->load(['customer', 'outlet']);

        $pos = PurchaseOrder::with(['items.barang'])
            ->where('customer_id', $suratJalan->customer_id)
            ->where('customer_outlet_id', $suratJalan->customer_outlet_id)
            ->where('tanggal', $suratJalan->tanggal)
            ->whereIn('status', ['proses', 'selesai'])
            ->get();

        return view('logistik.print', compact('suratJalan', 'pos'));
    }

    /**
     * Show form for creating SJ - select PO
     */
    public function create()
    {
        $availablePOs = PurchaseOrder::with(['customer', 'outlet'])
            ->where('status', 'baru')
            ->orderByDesc('tanggal')
            ->get();

        return view('logistik.create', compact('availablePOs'));
    }
}
