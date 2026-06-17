<x-app-layout>
@section('title', 'Biaya Operasional')
@section('page-title', 'Biaya Operasional')
@section('page-subtitle', 'Kelola pengeluaran operasional bisnis')
@section('header-actions')
    <a href="{{ route('biaya-operasional.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Catat Biaya
    </a>
@endsection

<div class="space-y-4">
    {{-- Summary Card --}}
    <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl p-5 text-white shadow-sm">
        <p class="text-red-100 text-sm font-medium mb-1">Total Biaya Bulan Ini</p>
        <p class="text-3xl font-bold">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</p>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Biaya</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($biayaList as $biaya)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $biaya->nama_biaya }}</td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            @php
                                $badgeColor = match($biaya->kategori) {
                                    'Transport'   => 'bg-blue-100 text-blue-700',
                                    'Packaging'   => 'bg-purple-100 text-purple-700',
                                    'Komunikasi'  => 'bg-cyan-100 text-cyan-700',
                                    'Tak Terduga' => 'bg-red-100 text-red-700',
                                    default       => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                                {{ $biaya->kategori }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden sm:table-cell">
                            {{ \Carbon\Carbon::parse($biaya->tanggal)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($biaya->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('biaya-operasional.edit', $biaya) }}"
                                   class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('biaya-operasional.destroy', $biaya) }}"
                                      onsubmit="return confirm('Hapus biaya ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Belum ada data biaya operasional
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($biayaList->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">{{ $biayaList->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
