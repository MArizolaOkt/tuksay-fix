<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Customer;
use App\Models\HargaBeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * CTRL-004: index - List semua invoice
     */
    public function index(Request $request)
    {
        $query = Invoice::with('customer')
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('no_invoice', 'like', '%' . $request->search . '%')
                ->orWhereHas('customer', fn($q) => $q->where('nama', 'like', '%' . $request->search . '%'));
        }

        $invoices = $query->paginate(15)->withQueryString();

        $totalTagihan  = Invoice::where('status', 'terbit')->sum('total_tagihan');
        $totalLunas    = Invoice::where('status', 'lunas')->sum('total_tagihan');

        return view('invoices.index', compact('invoices', 'totalTagihan', 'totalLunas'));
    }

    /**
     * CTRL-004: show create form — select customer + tanggal
     */
    public function create()
    {
        // PO berstatus proses yang belum di-invoice
        $availablePOs = PurchaseOrder::with(['customer', 'outlet', 'items.barang'])
            ->where('status', 'proses')
            ->orderByDesc('tanggal')
            ->get();

        return view('invoices.create', compact('availablePOs'));
    }

    /**
     * CTRL-004: generate - Generate invoice dari satu/banyak PO
     */
    public function generate(Request $request)
    {
        $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'tanggal'           => 'required|date',
            'purchase_order_ids' => 'required|array|min:1',
            'purchase_order_ids.*' => 'exists:purchase_orders,id',
        ]);

        $pos = PurchaseOrder::with(['items.barang'])
            ->whereIn('id', $request->purchase_order_ids)
            ->where('customer_id', $request->customer_id)
            ->where('status', 'proses')
            ->get();

        if ($pos->isEmpty()) {
            return back()->with('error', 'Tidak ada PO yang valid untuk di-invoice.');
        }

        DB::transaction(function () use ($request, $pos) {
            // Hitung total tagihan: SUM(qty × harga_jual)
            $totalTagihan = 0;
            foreach ($pos as $po) {
                foreach ($po->items as $item) {
                    $totalTagihan += $item->qty * $item->barang->harga_jual;
                }
            }

            $invoice = Invoice::create([
                'customer_id'   => $request->customer_id,
                'tanggal'       => $request->tanggal,
                'total_tagihan' => $totalTagihan,
                'status'        => 'terbit',
            ]);

            // Update status PO ke selesai
            $pos->each(fn($po) => $po->update(['status' => 'selesai']));
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    /**
     * CTRL-004: show - Detail invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('customer');

        // Ambil PO terkait (customer + status selesai di tanggal invoice)
        $pos = PurchaseOrder::with(['items.barang', 'outlet'])
            ->where('customer_id', $invoice->customer_id)
            ->where('status', 'selesai')
            ->orderBy('tanggal')
            ->get();

        return view('invoices.show', compact('invoice', 'pos'));
    }

    /**
     * CTRL-004: print - Print view A4
     */
    public function print(Invoice $invoice)
    {
        $invoice->load('customer');

        $pos = PurchaseOrder::with(['items.barang', 'outlet'])
            ->where('customer_id', $invoice->customer_id)
            ->where('status', 'selesai')
            ->orderBy('tanggal')
            ->get();

        return view('invoices.print', compact('invoice', 'pos'));
    }

    /**
     * CTRL-004: markLunas - Ubah status invoice ke lunas
     */
    public function markLunas(Invoice $invoice)
    {
        if ($invoice->status === 'lunas') {
            return back()->with('info', 'Invoice sudah berstatus lunas.');
        }

        $invoice->update(['status' => 'lunas']);

        return back()->with('success', 'Invoice berhasil ditandai sebagai lunas.');
    }
}
