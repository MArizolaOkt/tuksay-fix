<x-app-layout>
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')
@section('page-subtitle', $barang->nama)

<div class="max-w-lg">
    <form method="POST" action="{{ route('barangs.update', $barang) }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Produk <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $barang->nama) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('nama') border-red-300 @enderror">
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Satuan <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-4 gap-3">
                    @foreach(['kg', 'ikat', 'buah', 'pck'] as $sat)
                        <label class="relative flex cursor-pointer">
                            <input type="radio" name="satuan" value="{{ $sat }}"
                                   {{ old('satuan', $barang->satuan) === $sat ? 'checked' : '' }}
                                   class="peer sr-only">
                            <span class="w-full text-center px-3 py-2.5 text-sm font-medium border-2 rounded-xl transition-all
                                         border-gray-200 text-gray-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700">
                                {{ $sat }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga Jual (Rp) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">Rp</span>
                    <input type="number" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}" required min="0" step="100"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                Simpan Perubahan
            </button>
            <a href="{{ route('barangs.index') }}"
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
</x-app-layout>
