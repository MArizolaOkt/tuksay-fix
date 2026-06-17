<x-app-layout>
@section('title', 'Belanja Harian')
@section('page-title', 'Belanja Harian')
@section('page-subtitle', 'Konsolidasi kebutuhan belanja berdasarkan PO')

<div class="space-y-4">

    {{-- Date Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <label class="text-sm font-medium text-gray-700">Tanggal:</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}"
                   class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
                Tampilkan
            </button>
            <span class="text-sm text-gray-400">{{ $konsolidasi->count() }} produk</span>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Modal Belanja</p>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalModal, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Est. Revenue</p>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Est. Margin Kotor</p>
            @php $margin = $totalRevenue > 0 ? (($totalRevenue - $totalModal) / $totalRevenue) * 100 : 0; @endphp
            <p class="text-2xl font-bold {{ $margin < 25 ? 'text-red-600' : 'text-emerald-600' }}">
                {{ number_format($margin, 1) }}%
            </p>
        </div>
    </div>

    {{-- Konsolidasi Table + Harga Beli Form --}}
    @if($konsolidasi->isNotEmpty())
    <form method="POST" action="{{ route('belanja.input-harga') }}" id="form-harga">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Kebutuhan Belanja — {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</h3>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Semua Harga
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Qty</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Breakdown Outlet</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Jual</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-emerald-600 uppercase tracking-wider bg-emerald-50/50">Harga Beli</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Modal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($konsolidasi as $i => $item)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <input type="hidden" name="harga[{{ $i }}][barang_id]" value="{{ $item->barang_id }}">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $item->barang_nama }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ $item->satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xl font-bold text-gray-900">{{ number_format($item->total_qty, 3) }}</span>
                                <span class="text-xs text-gray-400 ml-1">{{ $item->satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 hidden lg:table-cell max-w-xs">
                                {{ $item->outlet_breakdown }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600">
                                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 bg-emerald-50/30">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                    <input type="number"
                                           name="harga[{{ $i }}][harga_beli]"
                                           value="{{ $item->harga_beli ?? '' }}"
                                           min="0" step="100"
                                           class="w-full pl-8 pr-3 py-2 border {{ $item->harga_beli ? 'border-emerald-300 bg-emerald-50' : 'border-gray-200' }} rounded-lg text-sm text-right focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                           placeholder="0">
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold">
                                @if($item->total_modal)
                                    <span class="text-gray-900">Rp {{ number_format($item->total_modal, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-200">
                            <td colspan="5" class="px-6 py-4 text-right font-semibold text-gray-700">Total Modal Hari Ini</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-700 text-lg">
                                Rp {{ number_format($totalModal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </form>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-sm">Tidak ada PO aktif pada tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}</p>
            <a href="{{ route('purchase-orders.create') }}" class="mt-2 inline-block text-sm text-emerald-600 hover:underline">Buat PO baru →</a>
        </div>
    @endif
</div>
</x-app-layout>
