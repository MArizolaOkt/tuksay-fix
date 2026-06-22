<x-app-layout>
@section('title', 'Edit ' . $purchaseOrder->no_po)
@section('page-title', 'Edit ' . $purchaseOrder->no_po)
@section('page-subtitle', 'Hanya PO berstatus "baru" yang dapat diedit')

<form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}"
      x-data="poEditForm()"
      x-init="init()"
      class="space-y-6">
@csrf @method('PUT')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h3 class="font-semibold text-gray-900">Detail PO</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. PO</label>
                <input type="text" value="{{ $purchaseOrder->no_po }}" disabled
                       class="w-full px-4 py-2.5 border border-gray-100 rounded-xl text-sm bg-gray-50 text-gray-500 font-mono">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal PO <span class="text-red-500">*</span></label>
                {{-- Tanggal PO tidak bisa diubah setelah PO dibuat --}}
                <input type="text" value="{{ \Carbon\Carbon::parse($purchaseOrder->tanggal)->translatedFormat('d F Y') }}" disabled
                       class="w-full px-4 py-2.5 border border-gray-100 rounded-xl text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
                <input type="hidden" name="tanggal" value="{{ $purchaseOrder->tanggal->toDateString() }}">
            </div>

            {{-- Tanggal Kirim — Perubahan 1 SKILL.md --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Kirim</label>
                <input type="date" name="tanggal_kirim"
                       value="{{ old('tanggal_kirim', $purchaseOrder->tanggal_kirim?->toDateString()) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-gray-400 mt-1">Opsional. Jika diisi, harus ≥ Tanggal PO.</p>
                @error('tanggal_kirim') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label>
                <select name="customer_id" x-model="customerId" @change="loadCustomerInfo()" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $purchaseOrder->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->nama }} ({{ ucfirst($customer->tipe) }})
                        </option>
                    @endforeach
                </select>
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
                <select name="customer_outlet_id" :required="customerTipe === 'resto'"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <template x-for="outlet in outlets" :key="outlet.id">
                        <option :value="outlet.id"
                                :selected="outlet.id == {{ $purchaseOrder->customer_outlet_id ?? 'null' }}"
                                x-text="outlet.nama_outlet"></option>
                    </template>
                </select>
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
                       class="w-full px-4 py-2.5 border border-purple-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                       placeholder="cth: Pernikahan Budi & Ani, 21 Juni 2026">
                <p class="text-xs text-purple-500 mt-1">Nama event unik untuk PO ini</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Referensi</label>
                <input type="text" name="no_ref" value="{{ old('no_ref', $purchaseOrder->no_ref) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
        </div>
    </div>

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
                    <div class="flex gap-3 items-start p-3 rounded-xl border border-gray-100">
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1">Produk</label>
                            <select :name="`items[${index}][barang_id]`" x-model="item.barang_id" required
                                    @change="updateItemSatuan(item); checkMarginEdit(items)"
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
                                   :min="item.isDecimal ? '0.1' : '1'"
                                   :step="item.isDecimal ? '0.1' : '1'"
                                   required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                   :placeholder="item.isDecimal ? '0.0' : '0'">
                        </div>
                        {{-- Perubahan 4: badge satuan konsisten dengan create.blade.php --}}
                        <div class="w-24 text-right">
                            <label class="block text-xs text-gray-500 mb-1">Satuan</label>
                            <span class="inline-block px-2 py-2 text-xs text-gray-500 bg-gray-50 rounded-lg" x-text="item.satuan || '-'">-</span>
                        </div>
                        <div class="mt-6">
                            <button type="button" @click="removeItem(index)"
                                    class="p-1.5 text-gray-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<div class="flex gap-3">
    <button type="submit"
            class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors shadow-sm">
        Simpan Perubahan
    </button>
    <a href="{{ route('purchase-orders.show', $purchaseOrder) }}"
       class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-colors">
        Batal
    </a>
</div>

{{-- Margin Warning — Perubahan 3 SKILL.md --}}
<div id="margin-warning" class="rounded-xl px-4 py-3 text-sm" style="display:none;" role="alert"></div>

</form>

@php
$itemsData = $purchaseOrder->items->map(fn($i) => [
    'barang_id' => $i->barang_id,
    'qty'       => $i->qty,
    'satuan'    => $i->barang?->satuan ?? '',
    'isDecimal' => in_array(strtolower($i->barang?->satuan ?? ''), ['kg','gram','gr','liter','l','ml']),
])->values()->all();
@endphp

@push('scripts')
<script>
// Perubahan 3 — SKILL.md: data harga beli terakhir per barang
const hargaBeliMapEdit = {
@foreach($barangs as $barang)
    '{{ $barang->id }}': { hargaBeli: {{ $barang->hargaBelis->last()?->harga_beli ?? 0 }}, hargaJual: {{ $barang->harga_jual }} },
@endforeach
};
function checkMarginEdit(items) {
    const warningEl = document.getElementById('margin-warning');
    if (!warningEl) return;
    let warnings = [];
    items.forEach(item => {
        if (!item.barang_id) return;
        const d = hargaBeliMapEdit[item.barang_id];
        if (!d || !d.hargaBeli) return;
        if (d.hargaBeli > d.hargaJual) warnings.push(`⚠️ Harga beli lebih tinggi dari harga jual untuk item ini.`);
    });
    if (warnings.length > 0) {
        warningEl.style.display = 'block';
        warningEl.className = 'rounded-xl px-4 py-3 text-sm bg-red-50 border border-red-200 text-red-800';
        warningEl.innerHTML = warnings.join('<br>');
    } else { warningEl.style.display = 'none'; }
}

function poEditForm() {
    return {
        customerId: '{{ $purchaseOrder->customer_id }}',
        customerTipe: '',
        outlets: [],
        namaEvent: '{{ old('nama_event', $purchaseOrder->nama_event ?? '') }}',
        items: @json($itemsData),

        init() {
            this.loadCustomerInfo();
        },

        loadCustomerInfo() {
            if (!this.customerId) return;
            fetch(`/customers/${this.customerId}/outlets-json`)
                .then(r => r.json())
                .then(data => {
                    this.customerTipe = data.tipe;
                    this.outlets = data.outlets;
                });
        },

        updateItemSatuan(item) {
            const DECIMAL_UNITS = ['kg', 'gram', 'gr', 'liter', 'l', 'ml'];

            // Hanya update item yang diubah, bukan semua item
            const index = this.items.indexOf(item);
            if (index === -1) return;

            const sel = document.querySelector(`select[name="items[${index}][barang_id]"]`);
            if (!sel) return;

            const selectedOpt = sel.options[sel.selectedIndex];
            if (selectedOpt && selectedOpt.dataset.satuan !== undefined) {
                const satuan = selectedOpt.dataset.satuan || '';
                this.items[index].satuan    = satuan;
                this.items[index].isDecimal = DECIMAL_UNITS.includes(satuan.toLowerCase().trim());

                // Reset qty hanya untuk item ini
                this.items[index].qty = '';
            }
        },

        addItem() {
            this.items.push({ barang_id: '', qty: '', satuan: '', isDecimal: true });
        },

        removeItem(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
            checkMarginEdit(this.items); // Perubahan 3
        }
    }
}
</script>
@endpush
</x-app-layout>
