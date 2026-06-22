<x-app-layout>
@section('title', $customer->nama)
@section('page-title', $customer->nama)
@section('page-subtitle', $customer->nama_perusahaan)
@section('header-actions')
    <a href="{{ route('customers.edit', $customer) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit Customer
    </a>
@endsection

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info Customer --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 rounded-2xl {{ $customer->isCatering() ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center font-bold text-2xl">
                    {{ strtoupper(substr($customer->nama, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 text-lg">{{ $customer->nama }}</h2>
                    <p class="text-gray-500 text-sm">{{ $customer->nama_perusahaan }}</p>
                </div>
            </div>

            <div class="space-y-3 text-sm">
                {{-- Badge Tipe --}}
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    @if($customer->isCatering())
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                            🍽️ Catering
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                            🏪 Resto
                        </span>
                    @endif
                </div>

                <div class="flex gap-3">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-gray-600">{{ $customer->alamat }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $customer->payment_method === 'CASH' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $customer->payment_method }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right side: Outlets (Resto) / Purchase Orders --}}
    <div class="lg:col-span-2 space-y-6">

        @if($customer->isResto())
        {{-- Outlets — hanya untuk Resto --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100" x-data="{ addingOutlet: false, newOutlet: '' }">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <h3 class="font-semibold text-gray-900">Outlet ({{ $customer->outlets->count() }})</h3>
                <button @click="addingOutlet = !addingOutlet"
                        class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    + Tambah Outlet
                </button>
            </div>

            {{-- Add Outlet Form --}}
            <div x-show="addingOutlet" x-transition class="px-6 py-4 border-b border-gray-50 bg-emerald-50/50">
                <form method="POST" action="{{ route('customer-outlets.store', $customer) }}" class="flex gap-3">
                    @csrf
                    <input type="text" name="nama_outlet" x-model="newOutlet" required
                           placeholder="Nama outlet baru..."
                           class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <button type="submit"
                            class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors">
                        Tambah
                    </button>
                    <button type="button" @click="addingOutlet = false"
                            class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                </form>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse($customer->outlets as $outlet)
                    <div class="px-6 py-3 flex items-center gap-3" x-data="{ editing: false, name: '{{ $outlet->nama_outlet }}' }">
                        <div class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <span x-show="!editing" class="text-sm text-gray-700">{{ $outlet->nama_outlet }}</span>
                            <form x-show="editing" method="POST" action="{{ route('customer-outlets.update', [$customer, $outlet]) }}" class="flex gap-2">
                                @csrf @method('PATCH')
                                <input type="text" name="nama_outlet" :value="name"
                                       class="flex-1 px-3 py-1 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <button type="submit" class="text-xs px-3 py-1 bg-emerald-600 text-white rounded-lg">Simpan</button>
                                <button type="button" @click="editing=false" class="text-xs px-3 py-1 bg-gray-100 text-gray-600 rounded-lg">Batal</button>
                            </form>
                        </div>
                        <div class="flex gap-2" x-show="!editing">
                            <button @click="editing=true"
                                    class="text-xs text-blue-600 hover:text-blue-700 font-medium">Edit</button>
                            <form method="POST" action="{{ route('customer-outlets.destroy', [$customer, $outlet]) }}"
                                  onsubmit="return confirm('Hapus outlet ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-600 font-medium">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-4 text-sm text-gray-400 text-center">Belum ada outlet</div>
                @endforelse
            </div>
        </div>
        @else
        {{-- Info Catering --}}
        <div class="bg-purple-50 rounded-2xl border border-purple-100 p-5">
            <div class="flex items-start gap-3">
                <span class="text-2xl">🍽️</span>
                <div>
                    <p class="text-sm font-semibold text-purple-800">Customer Catering</p>
                    <p class="text-sm text-purple-600 mt-0.5">Customer ini tidak memiliki outlet tetap. Setiap Purchase Order memiliki nama event yang berbeda-beda.</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Purchase Orders --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <h3 class="font-semibold text-gray-900">Purchase Orders ({{ $customer->purchaseOrders->count() }})</h3>
                <a href="{{ route('purchase-orders.create') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">+ Buat PO</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($customer->purchaseOrders->take(5) as $po)
                    <div class="px-6 py-3 flex items-center justify-between">
                        <div>
                            <a href="{{ route('purchase-orders.show', $po) }}"
                               class="text-sm font-medium text-emerald-600 hover:underline">{{ $po->no_po }}</a>
                            @if($customer->isCatering())
                                <p class="text-xs text-gray-400">
                                    🎉 {{ $po->nama_event ?? '-' }} — {{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }}
                                </p>
                            @else
                                <p class="text-xs text-gray-400">
                                    {{ $po->outlet->nama_outlet ?? '-' }} — {{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $po->status === 'baru' ? 'bg-blue-50 text-blue-700' : ($po->status === 'proses' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700') }}">
                            {{ ucfirst($po->status) }}
                        </span>
                    </div>
                @empty
                    <div class="px-6 py-4 text-sm text-gray-400 text-center">Belum ada PO</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
</x-app-layout>
