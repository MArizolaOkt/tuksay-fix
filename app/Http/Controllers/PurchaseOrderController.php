<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerOutlet;
use App\Models\Barang;
use App\Models\PurchaseOrder;
use App\Models\PoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    // CTRL-001: index - List active POs
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['customer', 'outlet'])
            ->orderBy('tanggal', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('no_po', 'like', '%' . $request->search . '%')
                ->orWhereHas('customer', fn($q) => $q->where('nama', 'like', '%' . $request->search . '%'));
        }

        $purchaseOrders = $query->paginate(15)->withQueryString();
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    // CTRL-001: create - Show form
    public function create()
    {
        $customers = Customer::with('outlets')->orderBy('nama')->get();
        $barangs   = Barang::with('hargaBelis')->orderBy('nama')->get(); // Perubahan 3: eager-load hargaBelis
        return view('purchase-orders.create', compact('customers', 'barangs'));
    }

    // CTRL-001: store - WORKFLOW-01: PO creation
    public function store(Request $request)
    {
        // Cek tipe customer untuk validasi kondisional
        $customer = Customer::find($request->customer_id);
        $isResto  = $customer && $customer->isResto();

        $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'customer_outlet_id' => $isResto ? 'required|exists:customer_outlets,id' : 'nullable',
            'nama_event'         => !$isResto ? 'required|string|max:255' : 'nullable|string|max:255',
            'tanggal'            => 'required|date',
            'tanggal_kirim'      => 'nullable|date|after_or_equal:tanggal', // Perubahan 1 — SKILL.md
            'no_ref'             => 'nullable|string|max:100',
            'items'              => 'required|array|min:1',
            'items.*.barang_id'  => 'required|exists:barangs,id',
            'items.*.qty'        => 'required|numeric|min:0.1',
        ]);

        DB::transaction(function () use ($request, $isResto) {
            $po = PurchaseOrder::create([
                'customer_id'        => $request->customer_id,
                'customer_outlet_id' => $isResto ? $request->customer_outlet_id : null,
                'nama_event'         => !$isResto ? $request->nama_event : null,
                'tanggal'            => $request->tanggal,
                'tanggal_kirim'      => $request->tanggal_kirim ?: null, // Perubahan 1 — SKILL.md
                'no_ref'             => $request->no_ref,
                'status'             => 'baru',
            ]);

            foreach ($request->items as $item) {
                PoItem::create([
                    'purchase_order_id' => $po->id,
                    'barang_id'         => $item['barang_id'],
                    'qty'               => $item['qty'],
                ]);
            }

            return $po;
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dibuat.');
    }

    // CTRL-001: show - Detail
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['customer', 'outlet', 'items.barang.hargaBelis']); // hargaBelis: Perubahan 3
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    // CTRL-001: edit - Edit form
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'baru') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'PO dengan status ' . $purchaseOrder->status . ' tidak dapat diedit.');
        }
        $purchaseOrder->load(['items.barang']);
        $customers = Customer::with('outlets')->orderBy('nama')->get();
        $barangs   = Barang::with('hargaBelis')->orderBy('nama')->get(); // Perubahan 3: eager-load hargaBelis
        return view('purchase-orders.edit', compact('purchaseOrder', 'customers', 'barangs'));
    }

    // CTRL-001: update
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Cek tipe customer untuk validasi kondisional
        $customer = Customer::find($request->customer_id);
        $isResto  = $customer && $customer->isResto();

        $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'customer_outlet_id' => $isResto ? 'required|exists:customer_outlets,id' : 'nullable',
            'nama_event'         => !$isResto ? 'required|string|max:255' : 'nullable|string|max:255',
            'tanggal'            => 'required|date',
            'tanggal_kirim'      => 'nullable|date|after_or_equal:tanggal', // Perubahan 1 — SKILL.md
            'no_ref'             => 'nullable|string|max:100',
            'items'              => 'required|array|min:1',
            'items.*.barang_id'  => 'required|exists:barangs,id',
            'items.*.qty'        => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($request, $purchaseOrder, $isResto) {
            $purchaseOrder->update([
                'customer_id'        => $request->customer_id,
                'customer_outlet_id' => $isResto ? $request->customer_outlet_id : null,
                'nama_event'         => !$isResto ? $request->nama_event : null,
                'tanggal'            => $request->tanggal,
                'tanggal_kirim'      => $request->tanggal_kirim ?: null, // Perubahan 1 — SKILL.md
                'no_ref'             => $request->no_ref,
            ]);

            $purchaseOrder->items()->delete();
            foreach ($request->items as $item) {
                PoItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'barang_id'         => $item['barang_id'],
                    'qty'               => $item['qty'],
                ]);
            }
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order berhasil diperbarui.');
    }

    // CTRL-001: updateStatus
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:baru,proses,selesai',
        ]);
        $purchaseOrder->update(['status' => $request->status]);
        return back()->with('success', 'Status PO berhasil diperbarui.');
    }

    // CTRL-001: destroy - Hapus PO (hanya status baru)
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'baru') {
            return back()->with('error', 'Hanya PO dengan status "baru" yang dapat dihapus.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus.');
    }
}
