<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PurchaseOrder;
// BiayaOperasional dihapus — SKILL.md Perubahan 5
use App\Models\HargaBeli;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceReportController extends Controller
{
    /**
     * CTRL-005 / ANALYTICS-001,002: Dashboard KPI + Chart data
     */
    public function dashboard(Request $request)
    {
        $days = $request->get('days', 30);
        $from = now()->subDays($days)->startOfDay();
        $to   = now()->endOfDay();

        // ANALYTICS-001: KPI
        // Gross Revenue = SUM(qty × harga_jual) untuk PO selesai
        $grossRevenue = DB::table('po_items')
            ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('barangs', 'po_items.barang_id', '=', 'barangs.id')
            ->where('purchase_orders.status', 'selesai')
            ->whereBetween('purchase_orders.tanggal', [$from->toDateString(), $to->toDateString()])
            ->sum(DB::raw('po_items.qty * barangs.harga_jual'));

        // COGS = SUM(qty × harga_beli) matched by barang & tanggal
        $cogs = DB::table('po_items')
            ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('harga_belis', function ($join) {
                $join->on('po_items.barang_id', '=', 'harga_belis.barang_id')
                     ->on('purchase_orders.tanggal', '=', 'harga_belis.tanggal');
            })
            ->where('purchase_orders.status', 'selesai')
            ->whereBetween('purchase_orders.tanggal', [$from->toDateString(), $to->toDateString()])
            ->sum(DB::raw('po_items.qty * harga_belis.harga_beli'));

        // OPEX dihapus — SKILL.md Perubahan 5
        $grossProfit  = $grossRevenue - $cogs;
        $netProfit    = $grossProfit; // Net Profit = Gross Profit (OPEX dihapus)
        $marginPct    = $grossRevenue > 0 ? ($grossProfit / $grossRevenue) * 100 : 0;

        // BEP dihapus — SKILL.md Perubahan 5
        $bepDaily     = 0;

        // ANALYTICS-002: Chart.js data
        // Line chart: Revenue harian 30 hari terakhir
        $revenueHarian = DB::table('po_items')
            ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('barangs', 'po_items.barang_id', '=', 'barangs.id')
            ->where('purchase_orders.status', 'selesai')
            ->whereBetween('purchase_orders.tanggal', [$from->toDateString(), $to->toDateString()])
            ->select(
                'purchase_orders.tanggal as tanggal',
                DB::raw('SUM(po_items.qty * barangs.harga_jual) as revenue')
            )
            ->groupBy('purchase_orders.tanggal')
            ->orderBy('purchase_orders.tanggal')
            ->get();

        // Doughnut OPEX dihapus — SKILL.md Perubahan 5
        $opexKategori = collect();

        // Horizontal Bar: Top 10 produk by revenue
        $topProduk = DB::table('po_items')
            ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('barangs', 'po_items.barang_id', '=', 'barangs.id')
            ->where('purchase_orders.status', 'selesai')
            ->whereBetween('purchase_orders.tanggal', [$from->toDateString(), $to->toDateString()])
            ->select('barangs.nama', DB::raw('SUM(po_items.qty * barangs.harga_jual) as revenue'))
            ->groupBy('barangs.nama')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // ANALYTICS-003: Price volatility alerts
        $alerts = $this->getPriceAlerts();

        $opex = 0; // OPEX dihapus — SKILL.md Perubahan 5
        return view('finance.dashboard', compact(
            'grossRevenue', 'cogs', 'grossProfit', 'netProfit', 'marginPct',
            'opex', 'bepDaily', 'days',
            'revenueHarian', 'opexKategori', 'topProduk', 'alerts'
        ));
    }

    /**
     * ANALYTICS-003: Price volatility alerts
     */
    private function getPriceAlerts(): array
    {
        $alerts = [];
        $since  = now()->subDays(7)->toDateString();
        $today  = now()->toDateString();

        $barangs = Barang::all();

        foreach ($barangs as $barang) {
            $hargaBelis = HargaBeli::where('barang_id', $barang->id)
                ->whereBetween('tanggal', [$since, $today])
                ->orderBy('tanggal')
                ->get();

            if ($hargaBelis->count() >= 2) {
                $first = $hargaBelis->first()->harga_beli;
                $last  = $hargaBelis->last()->harga_beli;
                $change = $first > 0 ? (($last - $first) / $first) * 100 : 0;

                if (abs($change) > 10) {
                    $alerts[] = [
                        'barang'  => $barang->nama,
                        'change'  => round($change, 1),
                        'type'    => abs($change) > 20 ? 'danger' : 'warning',
                        'harga_lama' => $first,
                        'harga_baru' => $last,
                    ];
                }
            }

            // ANALYTICS-003: Low margin alert (<25%)
            $latestHarga = HargaBeli::where('barang_id', $barang->id)
                ->orderByDesc('tanggal')
                ->first();

            if ($latestHarga && $barang->harga_jual > 0) {
                $margin = (($barang->harga_jual - $latestHarga->harga_beli) / $barang->harga_jual) * 100;
                if ($margin < 25) {
                    $alerts[] = [
                        'barang'  => $barang->nama,
                        'change'  => round($margin, 1),
                        'type'    => 'margin',
                        'harga_lama' => $latestHarga->harga_beli,
                        'harga_baru' => $barang->harga_jual,
                    ];
                }
            }
        }

        return $alerts;
    }

    /**
     * ANALYTICS-003: Price trend / volatility
     */
    public function priceTrend(Request $request)
    {
        $barangId = $request->get('barang_id');
        $days     = $request->get('days', 30);

        $barangs  = Barang::orderBy('nama')->get();

        $trendData = [];
        if ($barangId) {
            $trendData = HargaBeli::where('barang_id', $barangId)
                ->where('tanggal', '>=', now()->subDays($days)->toDateString())
                ->orderBy('tanggal')
                ->get();
        }

        return view('finance.price-trend', compact('barangs', 'barangId', 'trendData', 'days'));
    }

    /**
     * ANALYTICS-004: P&L Report
     */
    public function plReport(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $from = "{$year}-{$mon}-01";
        $to   = Carbon::parse($from)->endOfMonth()->toDateString();

        // Revenue
        $revenue = DB::table('po_items')
            ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('barangs', 'po_items.barang_id', '=', 'barangs.id')
            ->where('purchase_orders.status', 'selesai')
            ->whereBetween('purchase_orders.tanggal', [$from, $to])
            ->sum(DB::raw('po_items.qty * barangs.harga_jual'));

        // COGS
        $cogs = DB::table('po_items')
            ->join('purchase_orders', 'po_items.purchase_order_id', '=', 'purchase_orders.id')
            ->join('harga_belis', function ($join) {
                $join->on('po_items.barang_id', '=', 'harga_belis.barang_id')
                     ->on('purchase_orders.tanggal', '=', 'harga_belis.tanggal');
            })
            ->where('purchase_orders.status', 'selesai')
            ->whereBetween('purchase_orders.tanggal', [$from, $to])
            ->sum(DB::raw('po_items.qty * harga_belis.harga_beli'));

        // OPEX breakdown dihapus — SKILL.md Perubahan 5
        $opexBreakdown = collect();
        $totalOpex    = 0;
        $grossProfit  = $revenue - $cogs;
        $netProfit    = $grossProfit; // Net Profit = Gross Profit (OPEX dihapus)
        $marginPct    = $revenue > 0 ? ($grossProfit / $revenue) * 100 : 0;
        $netMarginPct = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;

        return view('finance.pl', compact(
            'revenue', 'cogs', 'grossProfit', 'totalOpex', 'netProfit',
            'marginPct', 'netMarginPct', 'opexBreakdown', 'month'
        ));
    }

    /**
     * ANALYTICS-005: Margin analysis per produk
     */
    public function marginAnalysis(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());

        $margins = DB::table('barangs')
            ->leftJoin('harga_belis', function ($join) use ($tanggal) {
                $join->on('harga_belis.barang_id', '=', 'barangs.id')
                     ->where('harga_belis.tanggal', '=', $tanggal);
            })
            ->select(
                'barangs.id',
                'barangs.nama',
                'barangs.satuan',
                'barangs.harga_jual',
                'harga_belis.harga_beli',
                DB::raw('CASE WHEN harga_belis.harga_beli IS NOT NULL AND barangs.harga_jual > 0
                          THEN ROUND(((barangs.harga_jual - harga_belis.harga_beli) / barangs.harga_jual) * 100, 2)
                          ELSE NULL END as margin_pct'),
                DB::raw('CASE WHEN harga_belis.harga_beli IS NOT NULL
                          THEN barangs.harga_jual - harga_belis.harga_beli
                          ELSE NULL END as margin_rp')
            )
            ->orderBy('barangs.nama')
            ->get();

        return view('finance.margin', compact('margins', 'tanggal'));
    }
}
