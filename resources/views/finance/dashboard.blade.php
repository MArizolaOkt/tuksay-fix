<x-app-layout>
@section('title', 'Finance Dashboard')
@section('page-title', 'Dashboard Analitik Keuangan')
@section('page-subtitle', 'KPI, tren revenue, dan analisis gross profit')

@section('header-actions')
    <form method="GET" action="{{ route('finance.dashboard') }}" class="flex items-center gap-2">
        <label class="text-sm text-gray-600 font-medium">Periode:</label>
        <select name="days" onchange="this.form.submit()"
                class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
            <option value="7"  {{ $days == 7  ? 'selected' : '' }}>7 Hari</option>
            <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari</option>
            <option value="60" {{ $days == 60 ? 'selected' : '' }}>60 Hari</option>
            <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Hari</option>
        </select>
    </form>
@endsection

@push('styles')
<style>
    .kpi-card { transition: transform 0.2s, box-shadow 0.2s; }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1); }
    @keyframes pulse-badge { 0%,100% { opacity:1; } 50% { opacity:0.6; } }
    .alert-badge { animation: pulse-badge 2s infinite; }
</style>
@endpush

<div class="space-y-6">

    {{-- ─── KPI Cards (ANALYTICS-001) ──────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="kpi-card bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-emerald-50 rounded-xl">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-400 font-medium bg-gray-50 px-2 py-0.5 rounded-full">{{ $days }}h</span>
            </div>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Gross Revenue</p>
            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</p>
        </div>

        <div class="kpi-card bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-amber-50 rounded-xl">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">COGS (Modal Beli)</p>
            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($cogs, 0, ',', '.') }}</p>
        </div>

        <div class="kpi-card bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 {{ $grossProfit >= 0 ? 'bg-blue-50' : 'bg-red-50' }} rounded-xl">
                    <svg class="w-5 h-5 {{ $grossProfit >= 0 ? 'text-blue-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <span class="text-xs font-bold {{ $marginPct >= 25 ? 'text-emerald-600 bg-emerald-50' : ($marginPct >= 10 ? 'text-amber-600 bg-amber-50' : 'text-red-600 bg-red-50') }} px-2 py-0.5 rounded-full">
                    {{ number_format($marginPct, 1) }}%
                </span>
            </div>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Gross Profit</p>
            <p class="text-xl font-bold {{ $grossProfit >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                Rp {{ number_format($grossProfit, 0, ',', '.') }}
            </p>
        </div>

        <div class="kpi-card bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-2xl p-5 shadow-sm text-white">
            <div class="flex items-start justify-between mb-3">
                <div class="p-2 bg-white/20 rounded-xl">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <span class="text-xs text-emerald-200 bg-white/10 px-2 py-0.5 rounded-full font-medium">Margin</span>
            </div>
            <p class="text-xs text-emerald-200 font-medium uppercase tracking-wider mb-1">Gross Margin</p>
            <p class="text-xl font-bold">{{ number_format($marginPct, 1) }}%</p>
            <p class="text-xs text-emerald-300 mt-1">Dari Gross Revenue periode ini</p>
        </div>
    </div>

    {{-- ─── ANALYTICS-003: Price Alerts ────────────────────────────────── --}}
    @if(count($alerts) > 0)
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-amber-600 alert-badge" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <h3 class="font-semibold text-amber-800 text-sm">⚠ Alerts Aktif ({{ count($alerts) }})</h3>
            <a href="{{ route('finance.price-trend') }}" class="ml-auto text-xs text-amber-700 hover:underline">Lihat Tren Harga →</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($alerts as $alert)
            <div class="flex items-center gap-3 bg-white rounded-xl px-3 py-2.5 border {{ $alert['type'] === 'danger' ? 'border-red-200' : 'border-amber-200' }}">
                <span class="text-lg">{{ $alert['type'] === 'danger' ? '🔴' : ($alert['type'] === 'margin' ? '🟡' : '🟠') }}</span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $alert['barang'] }}</p>
                    @if($alert['type'] === 'margin')
                        <p class="text-xs text-amber-700">Margin rendah: {{ $alert['change'] }}%</p>
                    @else
                        <p class="text-xs {{ $alert['change'] > 0 ? 'text-red-700' : 'text-blue-700' }}">
                            Harga {{ $alert['change'] > 0 ? 'naik' : 'turun' }} {{ abs($alert['change']) }}% (7 hari)
                        </p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── ANALYTICS-002: Charts ───────────────────────────────────────── --}}

    {{-- Revenue Harian Chart --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="mb-4">
            <h3 class="font-semibold text-gray-900">Tren Revenue Harian</h3>
            <p class="text-xs text-gray-400">{{ $days }} hari terakhir</p>
        </div>
        <div style="position:relative; height:220px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Bottom Charts: Top Produk + P&L Summary --}}
    {{-- OPEX Doughnut dihapus — SKILL.md Perubahan 5 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="font-semibold text-gray-900">Top Produk by Revenue</h3>
                <p class="text-xs text-gray-400">{{ $days }} hari terakhir</p>
            </div>
            <div style="position:relative; height:280px;">
                <canvas id="topProdukChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="font-semibold text-gray-900">Ringkasan P&L</h3>
                <p class="text-xs text-gray-400">Periode {{ $days }} hari terakhir</p>
            </div>
            <div class="space-y-4">
                @php
                    $bars = [
                        ['label' => 'Gross Revenue', 'val' => $grossRevenue, 'color' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'ratio' => 100],
                        ['label' => 'COGS', 'val' => $cogs, 'color' => 'bg-amber-400', 'text' => 'text-amber-700', 'ratio' => $grossRevenue > 0 ? min(($cogs/$grossRevenue)*100,100) : 0],
                        ['label' => 'Gross Profit', 'val' => $grossProfit, 'color' => $grossProfit>=0 ? 'bg-blue-500' : 'bg-red-500', 'text' => $grossProfit>=0 ? 'text-blue-700' : 'text-red-600', 'ratio' => $grossRevenue > 0 ? min((abs($grossProfit)/$grossRevenue)*100,100) : 0],
                    ];
                @endphp
                @foreach($bars as $bar)
                <div>
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="text-gray-600 font-medium">{{ $bar['label'] }}</span>
                        <span class="font-bold {{ $bar['text'] }}">Rp {{ number_format($bar['val'], 0, ',', '.') }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-2 {{ $bar['color'] }} rounded-full transition-all duration-700" style="width:{{ $bar['ratio'] }}%"></div>
                    </div>
                </div>
                @endforeach
                <div class="pt-4 border-t border-gray-100">
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-bold text-gray-800 text-base">Gross Profit</span>
                        <span class="text-2xl font-bold {{ $grossProfit >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                            Rp {{ number_format($grossProfit, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('finance.pl') }}" class="text-center text-xs py-2 bg-emerald-50 text-emerald-700 rounded-xl hover:bg-emerald-100 font-medium transition-colors">
                            Laporan P&L →
                        </a>
                        <a href="{{ route('finance.margin') }}" class="text-center text-xs py-2 bg-purple-50 text-purple-700 rounded-xl hover:bg-purple-100 font-medium transition-colors">
                            Analisis Margin →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const revenueHarian = @json($revenueHarian);
const opexKategori  = @json($opexKategori);
const topProduk     = @json($topProduk);
const COLORS = ['#10b981','#f59e0b','#6366f1','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#84cc16'];

Chart.defaults.font.family = "'Inter', sans-serif";

// 1. Line: Revenue Harian
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: revenueHarian.map(r => {
            const d = new Date(r.tanggal);
            return d.toLocaleDateString('id-ID', { day:'numeric', month:'short' });
        }),
        datasets: [{
            label: 'Revenue',
            data: revenueHarian.map(r => r.revenue),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,0.08)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#10b981',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } }
        },
        scales: {
            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } },
            y: {
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID',{notation:'compact'}).format(v) }
            }
        }
    }
});

// 2. Horizontal Bar: Top Produk — (OPEX Doughnut dihapus SKILL.md Perubahan 5)
if (topProduk.length > 0) {
    new Chart(document.getElementById('topProdukChart'), {
        type: 'bar',
        data: {
            labels: topProduk.map(p => p.nama),
            datasets: [{ label: 'Revenue', data: topProduk.map(p => p.revenue), backgroundColor: topProduk.map((_,i) => COLORS[i%COLORS.length]), borderRadius: 4, borderSkipped: false }]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) } }
            },
            scales: {
                x: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID',{notation:'compact'}).format(v) } },
                y: { grid: { display: false } }
            }
        }
    });
} else {
    document.getElementById('topProdukChart').parentElement.innerHTML = '<div class="h-full flex items-center justify-center text-gray-400 text-sm">Belum ada data produk</div>';
}
</script>
@endpush
</x-app-layout>
