<x-app-layout>
@section('title', 'Edit Biaya Operasional')
@section('page-title', 'Edit Biaya Operasional')

<div class="max-w-lg">
    <form method="POST" action="{{ route('biaya-operasional.update', $biayaOperasional) }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Biaya <span class="text-red-500">*</span></label>
                <input type="text" name="nama_biaya" value="{{ old('nama_biaya', $biayaOperasional->nama_biaya) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ old('kategori', $biayaOperasional->kategori) === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah (Rp) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                    <input type="number" name="jumlah" value="{{ old('jumlah', $biayaOperasional->jumlah) }}" required min="0" step="100"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $biayaOperasional->tanggal) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                Simpan Perubahan
            </button>
            <a href="{{ route('biaya-operasional.index') }}"
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
</x-app-layout>
