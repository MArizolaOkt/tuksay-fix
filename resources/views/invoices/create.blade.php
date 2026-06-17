<x-app-layout>
@section('title', 'Buat Invoice')
@section('page-title', 'Buat Invoice')
@section('page-subtitle', 'Generate invoice dari PO yang sudah diproses')

<div class="max-w-3xl">
    <form method="POST" action="{{ route('invoices.generate') }}"
          x-data="{ selectedCustomer: null, selectedPOs: [] }"
          class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="font-semibold text-gray-900">Detail Invoice</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" x-model="selectedCustomer" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Pilih customer...</option>
                        @foreach($availablePOs->groupBy('customer_id') as $custId => $cPOs)
                            <option value="{{ $custId }}">{{ $cPOs->first()->customer->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Invoice <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', today()->toDateString()) }}" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Pilih PO yang akan di-Invoice</h3>

            @if($availablePOs->isEmpty())
                <div class="py-8 text-center text-gray-400 text-sm">
                    Tidak ada PO berstatus "Proses". Pastikan sudah ada Surat Jalan yang dibuat.
                </div>
            @else
                <div class="space-y-3">
                    @php $posByCustomer = $availablePOs->groupBy('customer_id'); @endphp
                    @foreach($posByCustomer as $custId => $cPOs)
                        <div x-show="selectedCustomer == '{{ $custId }}' || !selectedCustomer">
                            @foreach($cPOs as $po)
                                @php
                                    $totalPO = $po->items->sum(fn($i) => $i->qty * $i->barang->harga_jual);
                                @endphp
                                <label class="flex items-start gap-4 p-4 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-emerald-300 transition-colors mb-3">
                                    <input type="checkbox" name="purchase_order_ids[]" value="{{ $po->id }}"
                                           class="mt-0.5 text-emerald-600 focus:ring-emerald-500 rounded">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-mono font-semibold text-emerald-600">{{ $po->no_po }}</span>
                                            <span class="text-xs px-2 py-0.5 bg-amber-50 text-amber-700 rounded-full">Proses</span>
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $po->customer->nama }} — {{ $po->outlet->nama_outlet ?? '-' }}</p>
                                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($po->tanggal)->format('d M Y') }}</p>
                                    </div>
                                    <div class="text-right flex-shrink-0">
                                        <p class="font-bold text-gray-900">Rp {{ number_format($totalPO, 0, ',', '.') }}</p>
                                        <p class="text-xs text-gray-400">{{ $po->items->count() }} item</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($availablePOs->isNotEmpty())
        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                Generate Invoice
            </button>
            <a href="{{ route('invoices.index') }}"
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Batal
            </a>
        </div>
        @endif
    </form>
</div>
</x-app-layout>
