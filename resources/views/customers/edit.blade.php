<x-app-layout>
@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')
@section('page-subtitle', $customer->nama)

<div class="max-w-2xl">
    <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <h3 class="font-semibold text-gray-900 text-base">Informasi Customer</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Customer <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $customer->nama) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('nama') border-red-300 @enderror">
                @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Perusahaan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $customer->nama_perusahaan) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 @error('nama_perusahaan') border-red-300 @enderror">
                @error('nama_perusahaan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat <span class="text-red-500">*</span></label>
                <textarea name="alamat" rows="3" required
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none @error('alamat') border-red-300 @enderror">{{ old('alamat', $customer->alamat) }}</textarea>
                @error('alamat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Metode Pembayaran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach(['CASH', 'TOP7', 'TOP14', 'TOP30'] as $method)
                        <label class="relative flex cursor-pointer">
                            <input type="radio" name="payment_method" value="{{ $method }}"
                                   {{ old('payment_method', $customer->payment_method) === $method ? 'checked' : '' }}
                                   class="peer sr-only">
                            <span class="w-full text-center px-3 py-2.5 text-sm font-medium border-2 rounded-xl transition-all
                                         border-gray-200 text-gray-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700">
                                {{ $method }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
                Simpan Perubahan
            </button>
            <a href="{{ route('customers.show', $customer) }}"
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
</x-app-layout>
