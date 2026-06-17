<x-app-layout>
@section('title', 'Invoice')
@section('page-title', 'Invoice')
@section('page-subtitle', 'Manajemen tagihan customer')
@section('header-actions')
    <a href="{{ route('invoices.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Invoice
    </a>
@endsection

<div class="space-y-4">
    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Piutang (Terbit)</p>
            <p class="text-2xl font-bold text-amber-600">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Lunas</p>
            <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($totalLunas, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari No. Invoice / customer..."
                   class="flex-1 min-w-48 px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <select name="status"
                    class="px-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                <option value="terbit" {{ request('status') === 'terbit' ? 'selected' : '' }}>Terbit</option>
                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
            <button type="submit"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Filter
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('invoices.show', $invoice) }}"
                               class="font-mono font-semibold text-purple-600 hover:text-purple-700 hover:underline">
                                {{ $invoice->no_invoice }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-gray-700 hidden md:table-cell font-medium">{{ $invoice->customer->nama }}</td>
                        <td class="px-6 py-4 text-gray-500 hidden sm:table-cell">
                            {{ \Carbon\Carbon::parse($invoice->tanggal)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                            Rp {{ number_format($invoice->total_tagihan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $invoice->status === 'terbit' ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-200' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('invoices.show', $invoice) }}"
                                   class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                    Detail
                                </a>
                                <a href="{{ route('invoices.print', $invoice) }}" target="_blank"
                                   class="px-3 py-1.5 text-xs font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                                    Print
                                </a>
                                @if($invoice->status === 'terbit')
                                    <form method="POST" action="{{ route('invoices.lunas', $invoice) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">
                                            Lunas
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Belum ada Invoice. <a href="{{ route('invoices.create') }}" class="text-emerald-600 hover:underline">Buat sekarang →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-gray-50">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
</x-app-layout>
