<x-app-layout>
@section('title', 'Laporan P&L')
@section('page-title', 'Laporan Profit & Loss')
@section('page-subtitle', 'Laporan laba rugi bulanan dengan breakdown OPEX')

@section('header-actions')
    <form method="GET" action="{{ route('finance.pl') }}" class="flex items-center gap-2">
        <input type="month" name="month" value="{{ $month }}"
               class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
               onchange="this.form.submit()">
    </form>
    <a href="{{ route('finance.dashboard') }}" class="flex items-center gap-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition-colors">
        ← Dashboard
    </a>
@endsection

<div class="max-w-4xl space-y-6">

    {{-- ─── P&L Header ─────────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-emerald-700 to-emerald-600 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Laporan P&L</h2>
                <p class="text-emerald-200 text-sm mt-1">
                    Periode: {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-emerald-200">Net Profit</p>
                <p class="text-3xl font-bold {{ $netProfit >= 0 ? 'text-white' : 'text-red-300' }}">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </p>
                <p class="text-sm {{ $netMarginPct >= 0 ? 'text-emerald-200' : 'text-red-300' }}">
                    Net Margin: {{ number_format($netMarginPct, 1) }}%
                </p>
            </div>
        </div>
    </div>

    {{-- ─── P&L Table ───────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-900">Rincian P&L</h3>
        </div>

        <div class="divide-y divide-gray-100">
            {{-- Revenue --}}
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-10 bg-emerald-500 rounded-full"></div>
                    <div>
                        <p class="font-semibold text-gray-900">Gross Revenue</p>
                        <p class="text-xs text-gray-400">Total penjualan (PO selesai × harga jual)</p>
                    </div>
                </div>
                <p class="text-xl font-bold text-emerald-700">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
            </div>

            {{-- COGS --}}
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-10 bg-amber-400 rounded-full"></div>
                    <div>
                        <p class="font-semibold text-gray-900">COGS (Harga Pokok Penjualan)</p>
                        <p class="text-xs text-gray-400">Modal beli (qty × harga beli) dari tabel harga_belis</p>
                    </div>
                </div>
                <p class="text-xl font-bold text-amber-700">– Rp {{ number_format($cogs, 0, ',', '.') }}</p>
            </div>

            {{-- Gross Profit --}}
            <div class="flex items-center justify-between px-6 py-4 bg-blue-50">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-10 bg-blue-500 rounded-full"></div>
                    <div>
                        <p class="font-bold text-gray-900">Gross Profit</p>
                        <p class="text-xs text-gray-500">
                            Gross Margin: <span class="font-semibold {{ $marginPct >= 25 ? 'text-emerald-600' : ($marginPct >= 10 ? 'text-amber-600' : 'text-red-600') }}">{{ number_format($marginPct, 1) }}%</span>
                        </p>
                    </div>
                </div>
                <p class="text-xl font-bold {{ $grossProfit >= 0 ? 'text-blue-700' : 'text-red-600' }}">
                    Rp {{ number_format($grossProfit, 0, ',', '.') }}
                </p>
            </div>

            {{-- OPEX Breakdown --}}
            <div class="px-6 py-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-1 h-6 bg-purple-400 rounded-full"></div>
                    <p class="font-semibold text-gray-900">OPEX (Biaya Operasional)</p>
                </div>
                @if($opexBreakdown->count() > 0)
                <div class="ml-4 space-y-2">
                    @foreach($opexBreakdown as $opex)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-purple-300 rounded-full"></span>
                            <span class="text-gray-600">{{ $opex->kategori }}</span>
                            <span class="text-xs text-gray-400">({{ $opex->count }} transaksi)</span>
                        </div>
                        <span class="font-semibold text-purple-700">– Rp {{ number_format($opex->total, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="flex items-center justify-between text-sm pt-2 border-t border-gray-100 mt-2">
                        <span class="font-semibold text-gray-700">Total OPEX</span>
                        <span class="font-bold text-purple-700">– Rp {{ number_format($totalOpex, 0, ',', '.') }}</span>
                    </div>
                </div>
                @else
                <p class="ml-4 text-sm text-gray-400 italic">Belum ada biaya operasional bulan ini.</p>
                @endif
            </div>

            {{-- Net Profit --}}
            <div class="flex items-center justify-between px-6 py-5 {{ $netProfit >= 0 ? 'bg-emerald-50' : 'bg-red-50' }}">
                <div class="flex items-center gap-3">
                    <div class="w-1 h-12 {{ $netProfit >= 0 ? 'bg-emerald-600' : 'bg-red-500' }} rounded-full"></div>
                    <div>
                        <p class="text-lg font-bold {{ $netProfit >= 0 ? 'text-emerald-900' : 'text-red-900' }}">NET PROFIT</p>
                        <p class="text-sm {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                            Net Margin: {{ number_format($netMarginPct, 1) }}%
                        </p>
                    </div>
                </div>
                <p class="text-3xl font-black {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- ─── Quick Actions ───────────────────────────────────────────────── --}}
    <div class="flex gap-3 flex-wrap">
        <a href="{{ route('finance.margin') }}" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Analisis Margin per Produk
        </a>
        <a href="{{ route('finance.price-trend') }}" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Tren Harga Beli
        </a>
        <a href="{{ route('biaya-operasional.create') }}" class="flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white rounded-xl text-sm font-medium hover:bg-purple-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Input Biaya Operasional
        </a>
    </div>
</div>
</x-app-layout>
