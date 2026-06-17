<x-app-layout>
@section('title', 'Tambah Customer')
@section('page-title', 'Tambah Customer')
@section('page-subtitle', 'Isi data customer baru beserta outlet')

<div class="max-w-2xl">
    <form method="POST" action="{{ route('customers.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-base">Informasi Customer</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Customer <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('nama') border-red-300 @enderror"
                       placeholder="Nama lengkap customer">
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Perusahaan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent @error('nama_perusahaan') border-red-300 @enderror"
                       placeholder="Nama perusahaan / toko">
                @error('nama_perusahaan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat <span class="text-red-500">*</span></label>
                <textarea name="alamat" rows="3" required
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none @error('alamat') border-red-300 @enderror"
                          placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Metode Pembayaran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach(['CASH', 'TOP7', 'TOP14', 'TOP30'] as $method)
                        <label class="relative flex cursor-pointer">
                            <input type="radio" name="payment_method" value="{{ $method }}"
                                   {{ old('payment_method', 'CASH') === $method ? 'checked' : '' }}
                                   class="peer sr-only">
                            <span class="w-full text-center px-3 py-2.5 text-sm font-medium border-2 rounded-xl transition-all
                                         border-gray-200 text-gray-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700">
                                {{ $method }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('payment_method') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Outlets --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4" x-data="{ outlets: [''] }">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-900 text-base">Outlet / Lokasi Pengiriman</h3>
                <button type="button" @click="outlets.push('')"
                        class="inline-flex items-center gap-1.5 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Outlet
                </button>
            </div>

            <template x-for="(outlet, i) in outlets" :key="i">
                <div class="flex gap-3 items-center">
                    <input type="text" :name="`outlets[${i}]`" x-model="outlets[i]"
                           class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           :placeholder="`Nama Outlet ${i + 1}`">
                    <button type="button" @click="outlets.splice(i, 1)"
                            x-show="outlets.length > 1"
                            class="text-gray-400 hover:text-red-500 transition-colors p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
            <p class="text-xs text-gray-400">Outlet kosong akan diabaikan</p>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                Simpan Customer
            </button>
            <a href="{{ route('customers.index') }}"
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
</x-app-layout>
