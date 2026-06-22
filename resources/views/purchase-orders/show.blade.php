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
                           ($purchaseOrder->status === 'proses' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
                        {{ ucfirst($purchaseOrder->status) }}
                    </span>
                </div>
            </div>

            {{-- Status Update (only baru → proses adalah via SJ) --}}
            @if($purchaseOrder->status !== 'selesai')
            <div class="mt-5 pt-4 border-t border-gray-50">
                <p class="text-xs text-gray-400 mb-3">Ubah Status</p>
                <form method="POST" action="{{ route('purchase-orders.status', $purchaseOrder) }}">
                    @csrf @method('PATCH')
                    <div class="flex gap-2">
                        <select name="status" class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="baru" {{ $purchaseOrder->status === 'baru' ? 'selected' : '' }}>Baru</option>
                            <option value="proses" {{ $purchaseOrder->status === 'proses' ? 'selected' : '' }}>Proses</option>
                            <option value="selesai" {{ $purchaseOrder->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                        <button type="submit"
                                class="px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                            Update
                        </button>
                    </div>
                </form>
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
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Harga Jual</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @php $total = 0; @endphp
                    @foreach($purchaseOrder->items as $item)
                        @php
                            $subtotal = $item->qty * $item->barang->harga_jual;
                            $total += $subtotal;
                            // Perubahan 4 — format qty+satuan: "10 Kg" bukan "10.000"
                            $qtyVal = (float)$item->qty;
                            $qtyFormatted = (fmod($qtyVal, 1) == 0)
                                ? (int)$qtyVal
                                : number_format($qtyVal, 1, '.', '');
                            $qtyDisplay = $qtyFormatted . ' ' . $item->barang->satuan;
                            // Perubahan 3 — cek margin
                            $hargaBeli = $item->barang->hargaBelis()->orderByDesc('tanggal')->first();
                            $marginNegatif = $hargaBeli && ($hargaBeli->harga_beli > $item->barang->harga_jual);
                            $marginPct = ($hargaBeli && $item->barang->harga_jual > 0)
                                ? (($item->barang->harga_jual - $hargaBeli->harga_beli) / $item->barang->harga_jual) * 100
                                : null;
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $item->barang->nama }}</p>
                                @if($marginNegatif)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full mt-1">
                                        ⚠️ Margin negatif ({{ number_format($marginPct, 1) }}%)
                                    </span>
                                @elseif($marginPct !== null && $marginPct < 25)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full mt-1">
                                        ⚠️ Margin rendah ({{ number_format($marginPct, 1) }}%)
                                    </span>
                                @endif
                            </td>
                            {{-- Perubahan 4: format qty "10 Kg" bukan "10.000" --}}
                            <td class="px-6 py-4 text-right font-semibold text-gray-900">
                                {{ $qtyDisplay }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600 hidden sm:table-cell">
                                Rp {{ number_format($item->barang->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-gray-900 hidden sm:table-cell">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 border-t border-gray-200">
                        <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-700">Total Nilai</td>
                        <td class="px-6 py-4 text-right font-bold text-lg text-emerald-700">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
