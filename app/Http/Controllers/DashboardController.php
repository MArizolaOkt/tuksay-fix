<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\BiayaOperasional;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today()->toDateString();
        $monthStart = today()->startOfMonth()->toDateString();
        $monthEnd   = today()->endOfMonth()->toDateString();

        // PO Hari ini
        $poHariIni = PurchaseOrder::where('tanggal', $today)->count();
        $poProses  = PurchaseOrder::where('status', 'proses')->count();
        $poBaru    = PurchaseOrder::where('status', 'baru')->count();

        // Invoice terbit (belum lunas)
        $invoiceTerbit = Invoice::where('status', 'terbit')->sum('total_tagihan');

        // Revenue bulan ini
        $revenueBulanIni = DB::table('po_items')
            ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('barangs', 'po_items.barang_id', '=', 'barangs.id')
            ->where('purchase_orders.status', 'selesai')
            ->whereBetween('purchase_orders.tanggal', [$monthStart, $monthEnd])
            ->sum(DB::raw('po_items.qty * barangs.harga_jual'));

        // Biaya operasional bulan ini
        $opexBulanIni = BiayaOperasional::whereBetween('tanggal', [$monthStart, $monthEnd])
            ->sum('jumlah');

        // Total customer
        $totalCustomers = Customer::count();

        // Aktivitas terbaru (PO terbaru)
        $recentPOs = PurchaseOrder::with(['customer', 'outlet'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'poHariIni', 'poProses', 'poBaru',
            'invoiceTerbit', 'revenueBulanIni', 'opexBulanIni',
            'totalCustomers', 'recentPOs'
        ));
    }
}
