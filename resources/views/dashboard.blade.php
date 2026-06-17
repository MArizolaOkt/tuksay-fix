<x-app-layout>
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional hari ini')

<div class="space-y-6">

    {{-- ─── KPI Cards Row 1 ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Revenue Bulan Ini --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="p-2.5 bg-emerald-50 rounded-xl">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Bulan Ini</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($revenueBulanIni, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 mt-1">Gross Revenue</p>
        </div>

        {{-- Invoice Belum Lunas --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="p-2.5 bg-amber-50 rounded-xl">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Piutang</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($invoiceTerbit, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 mt-1">Invoice Belum Lunas</p>
        </div>

        {{-- Biaya Operasional --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="p-2.5 bg-red-50 rounded-xl">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-red-500 bg-red-50 px-2 py-1 rounded-full">Bulan Ini</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($opexBulanIni, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 mt-1">Biaya Operasional</p>
        </div>

        {{-- Total Customers --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-4">
                <div class="p-2.5 bg-blue-50 rounded-xl">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalCustomers }}</p>
            <p class="text-sm text-gray-500 mt-1">Customer Aktif</p>
        </div>
    </div>

    {{-- ─── PO Status Summary ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-4xl font-bold">{{ $poBaru }}</p>
            <p class="text-blue-100 mt-1 text-sm font-medium">PO Baru (menunggu)</p>
            <a href="{{ route('purchase-orders.index', ['status' => 'baru']) }}"
               class="inline-flex items-center gap-1 mt-3 text-xs text-blue-200 hover:text-white transition-colors">
                Lihat semua →
            </a>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-4xl font-bold">{{ $poProses }}</p>
            <p class="text-amber-100 mt-1 text-sm font-medium">PO Dalam Proses</p>
            <a href="{{ route('purchase-orders.index', ['status' => 'proses']) }}"
               class="inline-flex items-center gap-1 mt-3 text-xs text-amber-200 hover:text-white transition-colors">
                Lihat semua →
            </a>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl p-5 text-white shadow-sm">
            <p class="text-4xl font-bold">{{ $poHariIni }}</p>
            <p class="text-emerald-100 mt-1 text-sm font-medium">PO Hari Ini</p>
            <a href="{{ route('purchase-orders.index') }}"
               class="inline-flex items-center gap-1 mt-3 text-xs text-emerald-200 hover:text-white transition-colors">
                Buat PO baru →
            </a>
        </div>
    </div>

    {{-- ─── Aktivitas Terbaru + Quick Actions ────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- Recent POs --}}
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <h3 class="font-semibold text-gray-900">Purchase Order Terbaru</h3>
                <a href="{{ route('purchase-orders.index') }}"
                   class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Lihat semua</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentPOs as $po)
                    <div class="px-6 py-4 flex items-center gap-4 hover:bg-gray-50/50 transition-colors">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-xs font-bold
                            {{ $po->status === 'baru' ? 'bg-blue-100 text-blue-700' : ($po->status === 'proses' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700') }}">
                            {{ $po->status === 'baru' ? 'B' : ($po->status === 'proses' ? 'P' : 'S') }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $po->no_po }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $po->customer->nama }} — {{ $po->outlet->nama_outlet ?? '-' }}</p>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $po->status === 'baru' ? 'bg-blue-50 text-blue-700' : ($po->status === 'proses' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
                                {{ ucfirst($po->status) }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">
                        Belum ada Purchase Order
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="font-semibold text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('purchase-orders.create') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-emerald-50 text-gray-700 hover:text-emerald-700 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 group-hover:bg-emerald-200 flex items-center justify-center text-emerald-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Buat Purchase Order</span>
                </a>
                <a href="{{ route('belanja.konsolidasi') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-blue-50 text-gray-700 hover:text-blue-700 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center text-blue-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Konsolidasi Belanja</span>
                </a>
                <a href="{{ route('logistik.create') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-amber-50 text-gray-700 hover:text-amber-700 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center text-amber-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Buat Surat Jalan</span>
                </a>
                <a href="{{ route('invoices.create') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-purple-50 text-gray-700 hover:text-purple-700 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 group-hover:bg-purple-200 flex items-center justify-center text-purple-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Buat Invoice</span>
                </a>
                <a href="{{ route('finance.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-teal-50 text-gray-700 hover:text-teal-700 transition-all group">
                    <div class="w-8 h-8 rounded-lg bg-teal-100 group-hover:bg-teal-200 flex items-center justify-center text-teal-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium">Laporan Keuangan</span>
                </a>
            </div>
        </div>
    </div>

</div>
</x-app-layout>
