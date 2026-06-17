<x-app-layout>
@section('title', $invoice->no_invoice)
@section('page-title', $invoice->no_invoice)
@section('page-subtitle', 'Detail Invoice')
@section('header-actions')
    <a href="{{ route('invoices.print', $invoice) }}" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl transition-colors">
        Print Invoice
    </a>
    @if($invoice->status === 'terbit')
        <form method="POST" action="{{ route('invoices.lunas', $invoice) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
                Tandai Lunas
            </button>
        </form>
    @endif
@endsection

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
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
                <div class="pt-3 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Total Tagihan</span>
                        <span class="text-xl font-bold text-purple-700">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="font-semibold text-gray-900">Detail Item</h3>
            </div>
            @foreach($pos as $po)
                <div class="px-6 py-3 bg-emerald-50/30 border-b border-gray-50 flex items-center justify-between">
                    <span class="text-xs font-semibold text-emerald-700">{{ $po->no_po }} — {{ $po->outlet->nama_outlet ?? '-' }}</span>
                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }}</span>
                </div>
                @foreach($po->items as $item)
                    @php $subtotal = $item->qty * $item->barang->harga_jual; @endphp
                    <div class="px-6 py-3 flex items-center justify-between text-sm border-b border-gray-50">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->barang->nama }}</p>
                            <p class="text-xs text-gray-400">{{ number_format($item->qty, 3) }} {{ $item->barang->satuan }} × Rp {{ number_format($item->barang->harga_jual, 0, ',', '.') }}</p>
                        </div>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            @endforeach
            <div class="px-6 py-4 bg-gray-50 flex items-center justify-between">
                <span class="font-bold text-gray-700">TOTAL TAGIHAN</span>
                <span class="text-xl font-bold text-purple-700">Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
