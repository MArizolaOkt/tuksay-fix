<x-app-layout>
@section('title', 'Detail Daftar Belanja — ' . $daftarBelanja->no_db)
@section('page-title', $daftarBelanja->no_db)
@section('page-subtitle', 'Detail record daftar belanja · ' . \Carbon\Carbon::parse($daftarBelanja->tanggal)->isoFormat('dddd, D MMMM Y'))
@section('header-actions')
    <div class="flex items-center gap-2">
        <a href="{{ route('belanja.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            Kembali
        </a>
        <a href="{{ route('belanja.konsolidasi', ['tanggal' => $daftarBelanja->tanggal->toDateString()]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Harga
        </a>
    </div>
@endsection

<div class="space-y-4">

    {{-- Header Info Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Kode DB --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Kode Daftar Belanja</p>
            <p class="text-xl font-bold font-mono text-emerald-600">{{ $daftarBelanja->no_db }}</p>
        </div>
        {{-- Tanggal --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Tanggal Belanja</p>
            <p class="text-xl font-bold text-gray-900">{{ $daftarBelanja->tanggal->format('d M Y') }}</p>
        </div>
        {{-- Total Modal --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Modal</p>
            <p class="text-xl font-bold text-gray-900">Rp {{ number_format($daftarBelanja->total_modal, 0, ',', '.') }}</p>
        </div>
        {{-- Margin --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Est. Margin Kotor</p>
            @php $margin = $daftarBelanja->marginPersen(); @endphp
            <p class="text-xl font-bold {{ $margin >= 30 ? 'text-emerald-600' : ($margin >= 15 ? 'text-amber-600' : 'text-red-600') }}">
                {{ number_format($margin, 1) }}%
            </p>
        </div>
    </div>

    {{-- Purchase Orders yang Terlibat --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Purchase Order Terlibat</h3>
                <p class="text-xs text-gray-400">{{ $daftarBelanja->purchaseOrders->count() }} PO pada tanggal ini</p>
            </div>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($daftarBelanja->purchaseOrders as $po)
                <div class="px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-sm font-semibold text-blue-600">{{ $po->no_po }}</span>
                        <span class="text-sm text-gray-700">
                            {{ $po->customer->nama }}
                            @if($po->outlet)
                                <span class="text-gray-400">· {{ $po->outlet->nama_outlet }}</span>
                            @endif
                            @if($po->nama_event)
                                <span class="text-gray-400">· {{ $po->nama_event }}</span>
                            @endif
                        </span>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $po->status === 'baru' ? 'bg-blue-100 text-blue-700' :
                           ($po->status === 'proses' ? 'bg-amber-100 text-amber-700' :
                           ($po->status === 'selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600')) }}">
                        {{ $po->statusLabel() }}
                    </span>
                </div>
            @empty
                <div class="px-6 py-4 text-sm text-gray-400">Tidak ada PO tercatat.</div>
            @endforelse
        </div>
    </div>

    {{-- Detail Item Belanja --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Detail Item Belanja</h3>
                <p class="text-xs text-gray-400">{{ $daftarBelanja->items->count() }} produk</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Breakdown Outlet</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Beli</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Harga Jual</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Modal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($daftarBelanja->items as $item)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $item->barang->nama }}</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    {{ $item->barang->satuan }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xl font-bold text-gray-900">{{ round((float)$item->total_qty, 2) }}</span>
                                <span class="text-xs text-gray-400 ml-1">{{ $item->barang->satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 hidden lg:table-cell max-w-xs">
                                @if($item->outlet_breakdown)
                                    <ul class="list-none space-y-1">
                                        @foreach(explode(' | ', $item->outlet_breakdown) as $breakdown)
                                            <li>{{ $breakdown }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($item->harga_beli)
                                    <span class="font-semibold text-gray-900">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 hidden sm:table-cell">
                                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold">
                                @php $totalModalItem = $item->totalModal(); @endphp
                                @if($totalModalItem !== null)
                                    <span class="text-gray-900">Rp {{ number_format($totalModalItem, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t border-gray-200">
                        <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-700 hidden lg:table-cell">Subtotal</td>
                        <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-700 lg:hidden">Subtotal</td>
                        <td class="px-6 py-4 text-right font-bold text-emerald-700 text-lg">
                            Rp {{ number_format($daftarBelanja->total_modal, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="bg-gray-50 border-t border-gray-100">
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-500 hidden lg:table-cell">Est. Revenue</td>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-500 lg:hidden">Est. Revenue</td>
                        <td class="px-6 py-3 text-right text-sm font-semibold text-gray-700">
                            Rp {{ number_format($daftarBelanja->total_revenue, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="bg-gray-50 border-t border-gray-100">
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-500 hidden lg:table-cell">Est. Margin Kotor</td>
                        <td colspan="3" class="px-6 py-3 text-right text-sm text-gray-500 lg:hidden">Est. Margin Kotor</td>
                        <td class="px-6 py-3 text-right text-sm font-bold {{ $margin >= 30 ? 'text-emerald-600' : ($margin >= 15 ? 'text-amber-600' : 'text-red-600') }}">
                            {{ number_format($margin, 1) }}%
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Metadata --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
        <div class="flex flex-wrap gap-4 text-xs text-gray-400">
            <span>Dibuat: {{ $daftarBelanja->created_at->isoFormat('D MMM Y, HH:mm') }}</span>
            <span>·</span>
            <span>Diperbarui: {{ $daftarBelanja->updated_at->isoFormat('D MMM Y, HH:mm') }}</span>
        </div>
    </div>

</div>
</x-app-layout>
