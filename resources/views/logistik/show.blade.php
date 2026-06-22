<x-app-layout>
@section('title', $suratJalan->no_sj)
@section('page-title', $suratJalan->no_sj)
@section('page-subtitle', 'Detail Surat Jalan')
@section('header-actions')
    <a href="{{ route('logistik.print', $suratJalan) }}" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Surat Jalan
    </a>
@endsection

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Informasi SJ</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">No. SJ</span>
                    <span class="font-mono font-semibold text-amber-600">{{ $suratJalan->no_sj }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($suratJalan->tanggal)->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Customer</span>
                    <a href="{{ route('customers.show', $suratJalan->customer) }}"
                       class="font-medium text-emerald-600 hover:underline">{{ $suratJalan->customer->nama }}</a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Outlet</span>
                    <span class="font-medium text-gray-700">{{ $suratJalan->outlet->nama_outlet ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Alamat</span>
                    <span class="text-right text-gray-600 max-w-32">{{ $suratJalan->customer->alamat }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="font-semibold text-gray-900">Detail Item (Dari Purchase Order)</h3>
            </div>
            @foreach($pos as $po)
                <div class="px-6 py-3 bg-emerald-50/30 border-b border-gray-50">
                    <span class="text-xs font-semibold text-emerald-700">PO: {{ $po->no_po }}</span>
                </div>
                @foreach($po->items as $item)
                    <div class="px-6 py-3 flex items-center justify-between text-sm border-b border-gray-50">
                        <div>
                            <p class="font-medium text-gray-900">{{ $item->barang->nama }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-semibold text-gray-900">{{ number_format($item->qty, 3) }}</span>
                            <span class="text-gray-400 ml-1">{{ $item->barang->satuan }}</span>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
</x-app-layout>
