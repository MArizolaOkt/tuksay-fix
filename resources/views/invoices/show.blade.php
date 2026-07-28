<x-app-layout>
@section('title', $invoice->no_invoice)
@section('page-title', $invoice->no_invoice)
@section('page-subtitle', 'Detail Invoice')
@section('header-actions')
    <a href="{{ route('invoices.print', $invoice) }}" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl transition-colors">
        🖨️ Print Invoice
    </a>
    @if($invoice->status === 'terbit')
        <form method="POST" action="{{ route('invoices.lunas', $invoice) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
                ✓ Tandai Lunas
            </button>
        </form>
    @endif
@endsection

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ─── Panel Kiri: Info Invoice ─── --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi Invoice</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">No. Invoice</span>
                    <span class="font-mono font-semibold text-purple-600">{{ $invoice->no_invoice }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Customer</span>
                    <a href="{{ route('customers.show', $invoice->customer) }}"
                       class="font-medium text-emerald-600 hover:underline">{{ $invoice->customer->nama }}</a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Payment</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $invoice->customer->payment_method === 'CASH' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $invoice->customer->payment_method }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Status</span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                        {{ $invoice->status === 'terbit' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jml. PO</span>
                    <span class="font-semibold text-gray-700">{{ $pos->count() }} PO</span>
                </div>
                <div class="pt-3 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Total Tagihan</span>
                        <span class="text-xl font-bold text-purple-700">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar PO (ringkas) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Purchase Orders</h3>
            <div class="space-y-2">
                @foreach($pos as $po)
                    @php
                        $poTotal = $po->items->sum(fn($item) => $item->qty * $item->harga_jual);
                    @endphp
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <p class="text-xs font-semibold text-emerald-700">{{ $po->no_po }}</p>
                            <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }}
                                @if($po->outlet) · {{ $po->outlet->nama_outlet }} @endif
                            </p>
                        </div>
                        <span class="text-xs font-bold text-gray-700">Rp {{ number_format($poTotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ─── Panel Kanan: Detail Item per PO ─── --}}
    <div class="lg:col-span-2 space-y-4">
        @foreach($pos as $poIndex => $po)
            @php
                $poSubtotal = $po->items->sum(fn($item) => $item->qty * $item->harga_jual);
                $itemNo = 1;
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Card Header PO --}}
                <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold">
                            {{ $poIndex + 1 }}
                        </span>
                        <div>
                            <p class="text-sm font-bold text-emerald-800">{{ $po->no_po }}</p>
                            @if($po->outlet)
                                <p class="text-xs text-emerald-600">{{ $po->outlet->nama_outlet }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-emerald-600">Tanggal PO</p>
                        <p class="text-sm font-semibold text-emerald-800">{{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }}</p>
                    </div>
                </div>

                {{-- Tabel Item PO --}}
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase w-8">No</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Nama Barang</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase w-20">QTY</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase w-16">Satuan</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase w-28">Harga Satuan</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase w-28">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($po->items as $item)
                            @php $subtotal = $item->qty * $item->harga_jual; @endphp
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $itemNo++ }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $item->barang->nama }}</p>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700">
                                    {{ rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }}
                                </td>
                                <td class="px-4 py-3 text-center text-gray-500 text-xs">{{ $item->barang->satuan ?? '-' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">
                                    Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-emerald-50/50 border-t border-emerald-100">
                            <td colspan="5" class="px-4 py-3 text-right text-xs font-semibold text-emerald-700">
                                Subtotal PO
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-emerald-700">
                                Rp {{ number_format($poSubtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach

        {{-- Grand Total Box --}}
        <div class="bg-white rounded-2xl shadow-sm border border-purple-100 px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Tagihan</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $pos->count() }} Purchase Order</p>
                </div>
                <span class="text-2xl font-bold text-purple-700">
                    Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
