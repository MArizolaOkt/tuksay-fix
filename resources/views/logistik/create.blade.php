<x-app-layout>
@section('title', 'Buat Surat Jalan')
@section('page-title', 'Buat Surat Jalan')
@section('page-subtitle', 'Pilih PO berstatus "Baru" untuk dibuat Surat Jalan')

<div class="max-w-2xl">
    <form method="POST" action="{{ route('logistik.generate') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Pilih Purchase Order</h3>

            @if($availablePOs->isEmpty())
                <div class="py-8 text-center text-gray-400">
                    <p class="text-sm">Tidak ada PO berstatus "Baru" yang tersedia.</p>
                    <a href="{{ route('purchase-orders.create') }}" class="mt-2 inline-block text-sm text-emerald-600 hover:underline">Buat PO baru →</a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($availablePOs as $po)
                        <label class="flex items-start gap-4 p-4 border-2 border-gray-100 rounded-xl cursor-pointer hover:border-emerald-300 transition-colors peer-checked:border-emerald-500"
                               x-data="{ selected: false }">
                            <input type="radio" name="purchase_order_id" value="{{ $po->id }}" required
                                   class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-mono font-semibold text-emerald-600">{{ $po->no_po }}</span>
                                    <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full">Baru</span>
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ $po->customer->nama }}</p>
                                <p class="text-xs text-gray-500">{{ $po->outlet->nama_outlet ?? '-' }} — {{ \Carbon\Carbon::parse($po->tanggal)->format('d M Y') }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        @if($availablePOs->isNotEmpty())
        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                Generate Surat Jalan
            </button>
            <a href="{{ route('logistik.index') }}"
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Batal
            </a>
        </div>
        @endif
    </form>
</div>
</x-app-layout>
