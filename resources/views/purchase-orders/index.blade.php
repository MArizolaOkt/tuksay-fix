<x-app-layout>
@section('title', 'Purchase Orders')
@section('page-title', 'Purchase Orders')
@section('page-subtitle', 'Daftar pesanan customer')
@section('header-actions')
    <a href="{{ route('purchase-orders.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat PO
    </a>
@endsection

<div class="space-y-4">

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari no. PO / customer..."
                   class="flex-1 min-w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <select name="status"
                    class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                <option value="baru" {{ request('status') === 'baru' ? 'selected' : '' }}>Baru</option>
                <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Proses</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
            <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('purchase-orders.index') }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. PO</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Outlet</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($purchaseOrders as $po)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('purchase-orders.show', $po) }}"
                               class="font-mono font-semibold text-emerald-600 hover:text-emerald-700 hover:underline">
                                {{ $po->no_po }}
                            </a>
                            @if($po->no_ref)
                                <p class="text-xs text-gray-400">Ref: {{ $po->no_ref }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            <a href="{{ route('customers.show', $po->customer) }}"
                               class="text-gray-700 hover:text-emerald-600 font-medium">{{ $po->customer->nama }}</a>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">
                            {{ $po->outlet->nama_outlet ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden sm:table-cell">
                            {{ \Carbon\Carbon::parse($po->tanggal)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $po->status === 'baru' ? 'bg-blue-50 text-blue-700 ring-1 ring-blue-200' :
                                   ($po->status === 'proses' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200') }}">
                                @if($po->status === 'baru') <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                @elseif($po->status === 'proses') <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                @else <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                @endif
                                {{ ucfirst($po->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('purchase-orders.show', $po) }}"
                                   class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    Detail
                                </a>
                                @if($po->status === 'baru')
                                    <a href="{{ route('purchase-orders.edit', $po) }}"
                                       class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                        Edit
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Tidak ada Purchase Order ditemukan.
                            <a href="{{ route('purchase-orders.create') }}" class="text-emerald-600 hover:underline">Buat PO baru →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($purchaseOrders->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">{{ $purchaseOrders->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
