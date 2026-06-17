<x-app-layout>
@section('title', 'Analisis Margin Produk')
@section('page-title', 'Analisis Margin per Produk')
@section('page-subtitle', 'Margin kotor per item berdasarkan harga beli dan harga jual')

@section('header-actions')
    <form method="GET" action="{{ route('finance.margin') }}" class="flex items-center gap-2">
        <label class="text-sm text-gray-600 font-medium">Tanggal harga beli:</label>
        <input type="date" name="tanggal" value="{{ $tanggal }}"
               class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
               onchange="this.form.submit()">
    </form>
@endsection

<div class="space-y-6">

    {{-- ─── Summary Cards ───────────────────────────────────────────────── --}}
    @php
        $withHarga      = $margins->whereNotNull('harga_beli');
        $avgMargin      = $withHarga->count() > 0 ? $withHarga->avg('margin_pct') : 0;
        $lowMargin      = $withHarga->where('margin_pct', '<', 25)->count();
        $goodMargin     = $withHarga->where('margin_pct', '>=', 25)->count();
        $noHarga        = $margins->whereNull('harga_beli')->count();
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Total Produk</p>
            <p class="text-2xl font-bold text-gray-900">{{ $margins->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Avg Margin</p>
            <p class="text-2xl font-bold {{ $avgMargin >= 25 ? 'text-emerald-600' : ($avgMargin >= 10 ? 'text-amber-600' : 'text-red-600') }}">
                {{ number_format($avgMargin, 1) }}%
            </p>
        </div>
        <div class="bg-emerald-50 rounded-2xl border border-emerald-100 p-4 shadow-sm">
            <p class="text-xs text-emerald-600 uppercase tracking-wider mb-1">Margin Sehat (≥25%)</p>
            <p class="text-2xl font-bold text-emerald-700">{{ $goodMargin }}</p>
        </div>
        <div class="bg-amber-50 rounded-2xl border border-amber-100 p-4 shadow-sm">
            <p class="text-xs text-amber-600 uppercase tracking-wider mb-1">Margin Rendah (<25%)</p>
            <p class="text-2xl font-bold text-amber-700">{{ $lowMargin }}</p>
        </div>
    </div>

    {{-- ─── Margin Table ────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-900">Margin per Produk</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    Berdasarkan harga beli tanggal: <strong>{{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</strong>
                    @if($noHarga > 0)
                    <span class="ml-2 text-amber-600">⚠ {{ $noHarga }} produk belum ada harga beli</span>
                    @endif
                </p>
            </div>
            <a href="{{ route('belanja.konsolidasi') }}"
               class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg hover:bg-amber-100 transition-colors font-medium">
                Input Harga Beli →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Produk</th>
                        <th class="px-6 py-3 text-center">Satuan</th>
                        <th class="px-6 py-3 text-right">Harga Jual</th>
                        <th class="px-6 py-3 text-right">Harga Beli</th>
                        <th class="px-6 py-3 text-right">Margin (Rp)</th>
                        <th class="px-6 py-3 text-center">Margin (%)</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($margins as $item)
                    @php
                        $isLow    = $item->margin_pct !== null && $item->margin_pct < 25;
                        $isGood   = $item->margin_pct !== null && $item->margin_pct >= 25;
                        $noData   = $item->harga_beli === null;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ $isLow && !$noData ? 'bg-amber-50/30' : '' }}">
                        <td class="px-6 py-3.5">
                            <p class="font-semibold text-gray-900 text-sm">{{ $item->nama }}</p>
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $item->satuan }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            @if($noData)
                            <span class="text-xs text-gray-400 italic">Belum input</span>
                            @else
                            <span class="text-sm text-gray-700">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            @if($noData)
                            <span class="text-gray-300">—</span>
                            @else
                            <span class="text-sm font-semibold {{ $item->margin_rp >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                Rp {{ number_format($item->margin_rp, 0, ',', '.') }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            @if($noData)
                            <span class="text-gray-300">—</span>
                            @else
                            @php
                                $pct = $item->margin_pct;
                                $barW = max(0, min(100, $pct));
                            @endphp
                            <div class="flex items-center gap-2 justify-end">
                                <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-1.5 rounded-full {{ $pct >= 25 ? 'bg-emerald-500' : ($pct >= 10 ? 'bg-amber-400' : 'bg-red-500') }}"
                                         style="width:{{ $barW }}%"></div>
                                </div>
                                <span class="text-sm font-bold {{ $pct >= 25 ? 'text-emerald-700' : ($pct >= 10 ? 'text-amber-700' : 'text-red-600') }} w-12 text-right">
                                    {{ number_format($pct, 1) }}%
                                </span>
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-center">
                            @if($noData)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                No Data
                            </span>
                            @elseif($item->margin_pct >= 25)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                ✓ Sehat
                            </span>
                            @elseif($item->margin_pct >= 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                ⚠ Rendah
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                ✗ Rugi
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            <p class="text-base mb-2">Belum ada produk terdaftar.</p>
                            <a href="{{ route('barangs.create') }}" class="text-sm text-emerald-600 hover:underline">Tambah produk →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── Tips ────────────────────────────────────────────────────────── --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 text-sm text-blue-700">
        <strong>ℹ Tips:</strong> Margin dihitung berdasarkan harga beli yang diinput pada tanggal tersebut di menu
        <a href="{{ route('belanja.konsolidasi') }}" class="underline font-semibold">Belanja Konsolidasi</a>.
        Pastikan harga beli selalu diupdate setiap hari untuk akurasi analisis.
    </div>
</div>
</x-app-layout>
