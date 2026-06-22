<x-app-layout>
@section('title', 'Buat Purchase Order')
@section('page-title', 'Buat Purchase Order')
@section('page-subtitle', 'Isi detail pesanan customer')

@push('styles')
<style>
    .item-row { transition: all 0.2s ease; }
    .item-row:hover { background-color: #f9fafb; }
</style>
@endpush

<form method="POST" action="{{ route('purchase-orders.store') }}"
      x-data="poForm()"
      x-init="init()"
      class="space-y-6">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: PO Info --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="font-semibold text-gray-900">Detail PO</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal PO <span class="text-red-500">*</span></label>
                {{-- Tanggal PO otomatis = hari ini, tidak bisa diubah user --}}
                <input type="text" value="{{ old('tanggal', today()->translatedFormat('d F Y')) }}" disabled
                       class="w-full px-4 py-2.5 border border-gray-100 rounded-xl text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                <input type="hidden" name="tanggal" value="{{ old('tanggal', today()->toDateString()) }}">
            </div>

            {{-- Tanggal Kirim -- Perubahan 1 SKILL.md --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Kirim</label>
                <input type="date" name="tanggal_kirim" value="{{ old('tanggal_kirim') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-gray-400 mt-1">Opsional. Jika diisi, harus ≥ Tanggal PO.</p>
                @error('tanggal_kirim') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label>
                <select name="customer_id" x-model="customerId" @change="loadCustomerInfo()" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Pilih customer...</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->nama }} ({{ ucfirst($customer->tipe) }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Outlet: hanya untuk customer Resto --}}
            <div x-show="customerTipe === 'resto'"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Outlet <span class="text-red-500">*</span></label>
                <select name="customer_outlet_id" :required="customerTipe === 'resto'" :disabled="outlets.length === 0"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:bg-gray-50 disabled:text-gray-400">
                    <option value="">Pilih outlet...</option>
                    <template x-for="outlet in outlets" :key="outlet.id">
                        <option :value="outlet.id" x-text="outlet.nama_outlet"></option>
                    </template>
                </select>
                @error('customer_outlet_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Nama Event: hanya untuk customer Catering --}}
            <div x-show="customerTipe === 'catering'"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Event / Acara <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_event" x-model="namaEvent"
                       :required="customerTipe === 'catering'"
                       value="{{ old('nama_event') }}"
                       class="w-full px-4 py-2.5 border border-purple-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 @error('nama_event') border-red-300 @enderror"
                       placeholder="cth: Pernikahan Budi & Ani, 21 Juni 2026">
                <p class="text-xs text-purple-500 mt-1">Nama event unik untuk PO ini</p>
                @error('nama_event') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Referensi</label>
                <input type="text" name="no_ref" value="{{ old('no_ref') }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                       placeholder="Opsional">
            </div>
        </div>

        {{-- Summary --}}
        <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
            <p class="text-sm font-medium text-emerald-800 mb-3">Ringkasan</p>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-emerald-700">Jumlah item</span>
                    <span class="font-semibold text-emerald-900" x-text="items.length + ' produk'">0 produk</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-emerald-700">Est. Nilai PO</span>
                    <span class="font-semibold text-emerald-900" x-text="'Rp ' + formatNumber(totalNilai)">Rp 0</span>
                </div>
                <template x-if="customerTipe === 'catering' && namaEvent">
                    <div class="flex justify-between pt-1 border-t border-emerald-200">
                        <span class="text-purple-600 font-medium">Event</span>
                        <span class="font-semibold text-purple-800 text-right max-w-32 truncate" x-text="namaEvent"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Right: Items --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Item Produk</h3>
                <button type="button" @click="addItem()"
                        class="inline-flex items-center gap-1.5 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Baris
                </button>
            </div>

            <div class="p-4 space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex gap-3 items-start p-3 rounded-xl border border-gray-100 item-row">
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Produk</label>
                            <select :name="`items[${index}][barang_id]`" x-model="item.barang_id"
                                    @change="updateItemPrice(item)"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Pilih produk...</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}"
                                            data-harga="{{ $barang->harga_jual }}"
                                            data-satuan="{{ $barang->satuan }}">
                                        {{ $barang->nama }} ({{ $barang->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-28">
                            <label class="block text-xs text-gray-500 mb-1">Qty</label>
                            <input type="number" :name="`items[${index}][qty]`" x-model="item.qty"
                                   @input="calcTotal(); checkMarginGlobal(items)"
                                   :min="item.isDecimal ? '0.1' : '1'"
                                   :step="item.isDecimal ? '0.1' : '1'"
                                   required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                   :placeholder="item.isDecimal ? '0.0' : '0'">
                        </div>
                        <div class="w-24 text-right">
                            <label class="block text-xs text-gray-500 mb-1">Satuan</label>
                            <span class="inline-block px-2 py-2 text-xs text-gray-500 bg-gray-50 rounded-lg" x-text="item.satuan || '-'">-</span>
                        </div>
                        <div class="w-32 text-right hidden sm:block">
                            <label class="block text-xs text-gray-500 mb-1">Subtotal</label>
                            <span class="text-sm font-medium text-gray-700" x-text="'Rp ' + formatNumber(item.harga * item.qty)">Rp 0</span>
                        </div>
                        <div class="mt-6">
                            <button type="button" @click="removeItem(index)"
                                    class="p-1.5 text-gray-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="items.length === 0" class="text-center py-8 text-gray-400 text-sm">
                    Belum ada item. Klik "Tambah Baris" untuk menambah produk.
                </div>
            </div>

            {{-- Total Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-b-2xl">
                <span class="text-sm text-gray-500">Total Nilai PO</span>
                <span class="text-lg font-bold text-gray-900" x-text="'Rp ' + formatNumber(totalNilai)">Rp 0</span>
            </div>
        </div>
    </div>
</div>

<div class="flex gap-3">
    <button type="submit" :disabled="items.length === 0"
            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
        Buat Purchase Order
    </button>
    <a href="{{ route('purchase-orders.index') }}"
       class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
        Batal
    </a>
</div>

{{-- Margin Warning — Perubahan 3 SKILL.md --}}
<div id="margin-warning" class="rounded-xl px-4 py-3 text-sm" style="display:none;" role="alert"></div>

</form>

@push('scripts')
<script>
// Perubahan 3 — SKILL.md: data harga beli terakhir per barang
const hargaBeliMap = {
@foreach($barangs as $barang)
    '{{ $barang->id }}': {
        hargaBeli: {{ $barang->hargaBelis->last()?->harga_beli ?? 0 }},
        hargaJual: {{ $barang->harga_jual }}
    },
@endforeach
};

function hitungMarginKotor(hargaBeli, hargaJual) {
    if (!hargaJual || hargaJual <= 0) return null;
    return ((hargaJual - hargaBeli) / hargaJual) * 100;
}

function checkMarginGlobal(items) {
    const warningEl = document.getElementById('margin-warning');
    if (!warningEl) return;
    let warnings = [];
    items.forEach(item => {
        if (!item.barang_id) return;
        const d = hargaBeliMap[item.barang_id];
        if (!d || !d.hargaBeli) return;
        const margin = hitungMarginKotor(d.hargaBeli, d.hargaJual);
        if (margin !== null && (d.hargaBeli > d.hargaJual)) {
            warnings.push(`⚠️ Harga beli lebih tinggi dari harga jual (margin negatif).`);
        }
    });
    if (warnings.length > 0) {
        warningEl.style.display = 'block';
        warningEl.className = 'rounded-xl px-4 py-3 text-sm bg-red-50 border border-red-200 text-red-800';
        warningEl.innerHTML = warnings.join('<br>');
    } else {
        warningEl.style.display = 'none';
    }
}

function poForm() {
    return {
        customerId: '{{ old('customer_id', '') }}',
        customerTipe: '',
        outlets: [],
        namaEvent: '{{ old('nama_event', '') }}',
        items: [{ barang_id: '', qty: '', harga: 0, satuan: '', isDecimal: true }],
        totalNilai: 0,

        init() {
            if (this.customerId) this.loadCustomerInfo();
        },

        loadCustomerInfo() {
            if (!this.customerId) {
                this.customerTipe = '';
                this.outlets = [];
                return;
            }
            fetch(`/customers/${this.customerId}/outlets-json`)
                .then(r => r.json())
                .then(data => {
                    this.customerTipe = data.tipe;
                    this.outlets = data.outlets;
                });
        },

        addItem() {
            this.items.push({ barang_id: '', qty: '', harga: 0, satuan: '', isDecimal: true });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calcTotal();
        },

        updateItemPrice(item) {
            // Satuan yang bersifat desimal (per berat/volume)
            const DECIMAL_UNITS = ['kg', 'gram', 'gr', 'liter', 'l', 'ml'];

            // Cari index item yang diubah — JANGAN loop semua item
            const index = this.items.indexOf(item);
            if (index === -1) return;

            const sel = document.querySelector(`select[name="items[${index}][barang_id]"]`);
            if (!sel) return;

            const selectedOpt = sel.options[sel.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.harga !== undefined) {
                this.items[index].harga = parseFloat(selectedOpt.dataset.harga) || 0;
                const satuan = selectedOpt.dataset.satuan || '';
                this.items[index].satuan = satuan;

                // Deteksi apakah satuan bersifat desimal (kg, liter, dll)
                const isDecimal = DECIMAL_UNITS.includes(satuan.toLowerCase().trim());
                this.items[index].isDecimal = isDecimal;

                // Reset qty hanya untuk item ini saja
                this.items[index].qty = '';
            }

            this.calcTotal();
            checkMarginGlobal(this.items);
        },

        calcTotal() {
            this.totalNilai = this.items.reduce((sum, item) => {
                return sum + ((parseFloat(item.harga) || 0) * (parseFloat(item.qty) || 0));
            }, 0);
        },

        formatNumber(n) {
            return Math.round(n).toLocaleString('id-ID');
        }
    }
}
</script>
@endpush
</x-app-layout>
