<x-app-layout>
@section('title', 'Logistik — Surat Jalan')
@section('page-title', 'Surat Jalan')
@section('page-subtitle', 'Manajemen pengiriman ke customer')
@section('header-actions')
    <a href="{{ route('logistik.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Surat Jalan
    </a>
@endsection

<div class="space-y-4">
    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari No. SJ / customer..."
                   class="flex-1 min-w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                   class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Filter
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. SJ</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Outlet</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($suratJalans as $sj)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono font-semibold text-amber-600">{{ $sj->no_sj }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 hidden md:table-cell">{{ $sj->customer->nama }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden lg:table-cell">{{ $sj->outlet->nama_outlet ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden sm:table-cell">
                            {{ \Carbon\Carbon::parse($sj->tanggal)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('logistik.show', $sj) }}"
                                   class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    Detail
                                </a>
                                <a href="{{ route('logistik.print', $sj) }}" target="_blank"
                                   class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                    Print
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Belum ada Surat Jalan.
                            <a href="{{ route('logistik.create') }}" class="text-emerald-600 hover:underline">Buat sekarang →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($suratJalans->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">{{ $suratJalans->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
