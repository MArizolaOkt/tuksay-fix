<x-app-layout>
@section('title', 'Daftar Belanja')
@section('page-title', 'Daftar Belanja')
@section('page-subtitle', 'Riwayat semua record belanja harian')
@section('header-actions')
    <a href="{{ route('belanja.konsolidasi') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Input Belanja Baru
    </a>
@endsection

<div class="space-y-4">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Summary Stat --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wide font-medium">Total Record Belanja</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalRecord) }}</p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari No. DB..."
                   class="flex-1 min-w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                   class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Filter
            </button>
            @if(request('search') || request('tanggal'))
                <a href="{{ route('belanja.index') }}"
                   class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Riwayat Daftar Belanja</h3>
            <span class="text-sm text-gray-400">{{ $daftarBelanjas->total() }} record</span>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. DB</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Tanggal</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Jumlah PO</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Total Modal</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Est. Revenue</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider hidden xl:table-cell">Margin</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($daftarBelanjas as $db)
                    @php
                        $margin = (float)$db->total_revenue > 0
                            ? (((float)$db->total_revenue - (float)$db->total_modal) / (float)$db->total_revenue) * 100
                            : 0;
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('belanja.show', $db) }}"
                               class="font-mono font-semibold text-emerald-600 hover:text-emerald-700 hover:underline">
                                {{ $db->no_db }}
                            </a>
                            <p class="text-xs text-gray-400 mt-0.5 sm:hidden">
                                {{ \Carbon\Carbon::parse($db->tanggal)->format('d M Y') }}
                            </p>
                        </td>
                        <td class="px-6 py-4 text-gray-600 hidden sm:table-cell">
                            {{ \Carbon\Carbon::parse($db->tanggal)->isoFormat('dddd, D MMM Y') }}
                        </td>
                        <td class="px-6 py-4 text-center hidden md:table-cell">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                                {{ $db->purchase_orders_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-800 hidden lg:table-cell">
                            @if((float)$db->total_modal > 0)
                                Rp {{ number_format($db->total_modal, 0, ',', '.') }}
                            @else
                                <span class="text-gray-300 font-normal">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-gray-600 hidden lg:table-cell">
                            Rp {{ number_format($db->total_revenue, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center hidden xl:table-cell">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $margin >= 30 ? 'bg-emerald-100 text-emerald-700' : ($margin >= 15 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ number_format($margin, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('belanja.show', $db) }}"
                                   class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    Detail
                                </a>
                                <a href="{{ route('belanja.konsolidasi', ['tanggal' => $db->tanggal->toDateString()]) }}"
                                   class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                    Edit Harga
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm text-gray-400">Belum ada record daftar belanja.</p>
                            <a href="{{ route('belanja.konsolidasi') }}" class="mt-2 inline-block text-sm text-emerald-600 hover:underline">
                                Input belanja pertama →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($daftarBelanjas->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">
                {{ $daftarBelanjas->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>
