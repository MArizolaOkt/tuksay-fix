<x-app-layout>
@section('title', $purchaseOrder->no_po)
@section('page-title', $purchaseOrder->no_po)
@section('page-subtitle', 'Detail Purchase Order')
@section('header-actions')
    @if($purchaseOrder->status === 'baru')
        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
            Edit PO
        </a>
    @endif
    @if($purchaseOrder->status === 'baru')
        <form method="POST" action="{{ route('logistik.generate') }}">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl transition-colors">
                Buat Surat Jalan
            </button>
        </form>
    @endif
@endsection

<div class="space-y-4">

    {{-- Alert: Surat Jalan belum dicetak --}}
    @if(!$suratJalanDicetak)
    <div class="flex items-center gap-3 px-5 py-4 bg-amber-50 border border-amber-200 rounded-2xl" id="alert-surat-jalan">
        <div class="flex-shrink-0">
            <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
        </div>
        <p class="text-sm font-medium text-amber-800">Surat jalan belum dicetak</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info Card --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi PO</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">No. PO</span>
                    <span class="font-mono font-semibold text-emerald-600">{{ $purchaseOrder->no_po }}</span>
                </div>
                @if($purchaseOrder->no_ref)
                <div class="flex justify-between">
                    <span class="text-gray-500">No. Ref</span>
                    <span class="font-medium text-gray-700">{{ $purchaseOrder->no_ref }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal PO</span>
                    <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($purchaseOrder->tanggal)->format('d/m/Y') }}</span>
                </div>
                {{-- Tanggal Kirim — Perubahan 1 SKILL.md --}}
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal Kirim</span>
                    <span class="font-medium {{ $purchaseOrder->tanggal_kirim ? 'text-gray-700' : 'text-gray-400 italic' }}">
                        {{ $purchaseOrder->tanggal_kirim ? \Carbon\Carbon::parse($purchaseOrder->tanggal_kirim)->format('d/m/Y') : '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Customer</span>
                    <a href="{{ route('customers.show', $purchaseOrder->customer) }}"
                       class="font-medium text-emerald-600 hover:underline">{{ $purchaseOrder->customer->nama }}</a>
                </div>
                @if($purchaseOrder->customer->isCatering())
                <div class="flex justify-between">
                    <span class="text-gray-500">Nama Event</span>
                    <span class="font-medium text-purple-700">🎉 {{ $purchaseOrder->nama_event ?? '-' }}</span>
                </div>
                @else
                <div class="flex justify-between">
                    <span class="text-gray-500">Outlet</span>
                    <span class="font-medium text-gray-700">{{ $purchaseOrder->outlet->nama_outlet ?? '-' }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Status</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                        {{ $purchaseOrder->status === 'baru' ? 'bg-blue-50 text-blue-700' :
                           ($purchaseOrder->status === 'proses' ? 'bg-amber-50 text-amber-700' :
                           ($purchaseOrder->status === 'menunggu_pembayaran' ? 'bg-orange-50 text-orange-700' : 'bg-emerald-50 text-emerald-700')) }}">
                        {{ $purchaseOrder->statusLabel() }}
                    </span>
                </div>
            </div>

            {{-- Status Flow Info (otomatis oleh sistem) --}}
            @if($purchaseOrder->status !== 'selesai')
            <div class="mt-5 pt-4 border-t border-gray-50">
                <p class="text-xs text-gray-400 mb-2">Alur Status (otomatis)</p>
                <div class="space-y-1.5">
                    @php
                        $steps = [
                            ['status' => 'baru', 'label' => 'Baru', 'desc' => 'PO dibuat'],
                            ['status' => 'proses', 'label' => 'Proses', 'desc' => 'Surat Jalan dibuat'],
                            ['status' => 'menunggu_pembayaran', 'label' => 'Menunggu Pembayaran', 'desc' => 'Invoice dibuat'],
                            ['status' => 'selesai', 'label' => 'Selesai', 'desc' => 'Pembayaran lunas'],
                        ];
                        $statusOrder = ['baru' => 0, 'proses' => 1, 'menunggu_pembayaran' => 2, 'selesai' => 3];
                        $currentIdx = $statusOrder[$purchaseOrder->status] ?? 0;
                    @endphp
                    @foreach($steps as $i => $step)
                        <div class="flex items-center gap-2 text-xs {{ $i <= $currentIdx ? 'text-emerald-700 font-medium' : 'text-gray-400' }}">
                            @if($i < $currentIdx)
                                <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            @elseif($i === $currentIdx)
                                <span class="w-3.5 h-3.5 flex items-center justify-center flex-shrink-0"><span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span></span>
                            @else
                                <span class="w-3.5 h-3.5 flex items-center justify-center flex-shrink-0"><span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span></span>
                            @endif
                            <span>{{ $step['label'] }} <span class="font-normal text-gray-400">— {{ $step['desc'] }}</span></span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Delete --}}
        @if($purchaseOrder->status === 'baru')
        <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}"
              onsubmit="return confirm('Yakin hapus PO {{ $purchaseOrder->no_po }}? Tindakan ini tidak bisa dibatalkan.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="w-full px-4 py-2.5 border border-red-200 text-red-600 hover:bg-red-50 text-sm font-medium rounded-xl transition-colors">
                Hapus PO
            </button>
        </form>
        @endif
    </div>

    {{-- Items --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="font-semibold text-gray-900">Item Produk ({{ $purchaseOrder->items->count() }})</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($purchaseOrder->items as $item)
                        @php
                            // Perubahan 4 — format qty+satuan: "10 Kg" bukan "10.000"
                            $qtyVal = (float)$item->qty;
                            $qtyFormatted = (fmod($qtyVal, 1) == 0)
                                ? (int)$qtyVal
                                : number_format($qtyVal, 1, '.', '');
                            $qtyDisplay = $qtyFormatted . ' ' . $item->barang->satuan;
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $item->barang->nama }}</p>
                            </td>
                            {{-- Perubahan 4: format qty "10 Kg" bukan "10.000" --}}
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                {{ $qtyDisplay }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
</div>
</x-app-layout>
