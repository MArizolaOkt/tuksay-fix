<x-app-layout>
@section('title', 'Tren Harga Beli')
@section('page-title', 'Tren Harga Beli Produk')
@section('page-subtitle', 'Volatilitas dan tren perubahan harga beli per produk')

@section('header-actions')
    <a href="{{ route('finance.dashboard') }}" class="flex items-center gap-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors">
        ← Dashboard
    </a>
@endsection

@push('styles')
<style>
    .filter-card { transition: all 0.2s; }
</style>
@endpush

<div class="space-y-6">

    {{-- ─── Filter Panel ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <form method="GET" action="{{ route('finance.price-trend') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Pilih Produk</label>
                <select name="barang_id" id="barang_id"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="">— Semua Produk —</option>
                    @foreach($barangs as $barang)
                    <option value="{{ $barang->id }}" {{ $barangId == $barang->id ? 'selected' : '' }}>
                        {{ $barang->nama }} ({{ $barang->satuan }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Periode</label>
                <select name="days"
                        class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <option value="7"  {{ $days == 7  ? 'selected' : '' }}>7 Hari</option>
                    <option value="14" {{ $days == 14 ? 'selected' : '' }}>14 Hari</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari</option>
                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>60 Hari</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Hari</option>
                </select>
            </div>
            <button type="submit"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                Tampilkan
            </button>
        </form>
    </div>

    @if($barangId && $trendData->count() > 0)
    {{-- ─── Selected Product Info ───────────────────────────────────────── --}}
    @php
        $selectedBarang = $barangs->firstWhere('id', $barangId);
        $firstPrice     = $trendData->first()->harga_beli;
        $lastPrice      = $trendData->last()->harga_beli;
        $minPrice       = $trendData->min('harga_beli');
        $maxPrice       = $trendData->max('harga_beli');
        $changeAmt      = $lastPrice - $firstPrice;
        $changePct      = $firstPrice > 0 ? (($changeAmt / $firstPrice) * 100) : 0;
    @endphp

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Harga Awal</p>
            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($firstPrice, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($trendData->first()->tanggal)->format('d/m/Y') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Harga Terakhir</p>
            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($lastPrice, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($trendData->last()->tanggal)->format('d/m/Y') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Min / Max</p>
            <p class="text-sm font-bold text-gray-900">
                Rp {{ number_format($minPrice, 0, ',', '.') }}
                <span class="text-gray-300 mx-1">/</span>
                Rp {{ number_format($maxPrice, 0, ',', '.') }}
            </p>
        </div>
        <div class="{{ abs($changePct) > 10 ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }} rounded-2xl border p-4 shadow-sm">
            <p class="text-xs {{ abs($changePct) > 10 ? 'text-red-500' : 'text-emerald-600' }} uppercase tracking-wider mb-1">
                Perubahan {{ $days }} hari
            </p>
            <p class="text-xl font-bold {{ $changePct >= 0 ? 'text-red-600' : 'text-emerald-700' }}">
                {{ $changePct >= 0 ? '+' : '' }}{{ number_format($changePct, 1) }}%
            </p>
            <p class="text-xs {{ abs($changePct) > 10 ? 'text-red-500' : 'text-emerald-600' }} mt-0.5">
                @if(abs($changePct) > 20) 🔴 Volatilitas Tinggi
                @elseif(abs($changePct) > 10) 🟠 Perlu Perhatian
                @else ✓ Stabil
                @endif
            </p>
        </div>
    </div>

    {{-- ─── Chart ───────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-900">
                    Tren Harga Beli — {{ $selectedBarang?->nama }}
                </h3>
                <p class="text-xs text-gray-400">{{ $days }} hari terakhir · {{ $trendData->count() }} data poin</p>
            </div>
            @if($selectedBarang)
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-400">Harga Jual:</span>
                <span class="font-bold text-emerald-700">Rp {{ number_format($selectedBarang->harga_jual, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>
        <div style="position:relative; height:300px;">
            <canvas id="trendChart"></canvas>
        </div>
    </div>

    {{-- ─── Data Table ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-900">Riwayat Harga</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-right">Harga Beli</th>
                        <th class="px-6 py-3 text-right">Perubahan</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($trendData as $idx => $data)
                    @php
                        $prevData = $idx > 0 ? $trendData[$idx - 1] : null;
                        $diff     = $prevData ? $data->harga_beli - $prevData->harga_beli : 0;
                        $diffPct  = ($prevData && $prevData->harga_beli > 0) ? (($diff / $prevData->harga_beli) * 100) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-sm text-gray-700">
                            {{ \Carbon\Carbon::parse($data->tanggal)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-3 text-right font-semibold text-gray-900">
                            Rp {{ number_format($data->harga_beli, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-right text-sm">
                            @if($idx === 0)
                            <span class="text-gray-300">—</span>
                            @elseif($diff > 0)
                            <span class="text-red-600 font-semibold">+Rp {{ number_format($diff, 0, ',', '.') }} (+{{ number_format($diffPct, 1) }}%)</span>
                            @elseif($diff < 0)
                            <span class="text-emerald-600 font-semibold">Rp {{ number_format($diff, 0, ',', '.') }} ({{ number_format($diffPct, 1) }}%)</span>
                            @else
                            <span class="text-gray-400">Tidak berubah</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if($idx === 0)
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Awal</span>
                            @elseif(abs($diffPct) > 10)
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">⚠ Lonjakan</span>
                            @else
                            <span class="text-xs bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full">✓ Normal</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @elseif($barangId && $trendData->count() === 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-700 mb-1">Belum Ada Data Harga</h3>
        <p class="text-sm text-gray-400 mb-4">Produk ini belum memiliki riwayat harga beli dalam {{ $days }} hari terakhir.</p>
        <a href="{{ route('belanja.konsolidasi') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors">
            Input Harga Beli Sekarang
        </a>
    </div>

    @else
    {{-- ─── Empty State: No Product Selected ──────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
            </svg>
        </div>
        <h3 class="font-semibold text-gray-700 mb-1">Pilih produk untuk melihat tren harga</h3>
        <p class="text-sm text-gray-400">Gunakan filter di atas untuk memilih produk dan periode.</p>
    </div>
    @endif
</div>

@if($barangId && $trendData->count() > 0)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const trendData = @json($trendData);
const hargaJual = {{ $selectedBarang?->harga_jual ?? 0 }};

Chart.defaults.font.family = "'Inter', sans-serif";

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: trendData.map(d => {
            const date = new Date(d.tanggal);
            return date.toLocaleDateString('id-ID', { day:'numeric', month:'short' });
        }),
        datasets: [
            {
                label: 'Harga Beli',
                data: trendData.map(d => d.harga_beli),
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245,158,11,0.08)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#f59e0b',
                pointRadius: 5,
                pointHoverRadius: 8,
            },
            {
                label: 'Harga Jual',
                data: trendData.map(() => hargaJual),
                borderColor: '#10b981',
                borderWidth: 2,
                borderDash: [6, 4],
                fill: false,
                pointRadius: 0,
                tension: 0,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: { usePointStyle: true, boxWidth: 8, padding: 16 }
            },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } },
            y: {
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(v) }
            }
        }
    }
});
</script>
@endpush
@endif
</x-app-layout>
