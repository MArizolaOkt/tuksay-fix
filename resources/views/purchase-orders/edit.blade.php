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
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $purchaseOrder->tanggal) }}" required
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Customer <span class="text-red-500">*</span></label>
                <select name="customer_id" x-model="customerId" @change="loadOutlets()" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $purchaseOrder->customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Outlet <span class="text-red-500">*</span></label>
                <select name="customer_outlet_id" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <template x-for="outlet in outlets" :key="outlet.id">
                        <option :value="outlet.id"
                                :selected="outlet.id == {{ $purchaseOrder->customer_outlet_id }}"
                                x-text="outlet.nama_outlet"></option>
                    </template>
                </select>
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
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="">Pilih produk...</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}" data-harga="{{ $barang->harga_jual }}">
                                        {{ $barang->nama }} ({{ $barang->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-28">
                            <label class="block text-xs text-gray-500 mb-1">Qty</label>
                            <input type="number" :name="`items[${index}][qty]`" x-model="item.qty"
                                   min="0.001" step="0.001" required
                                   class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
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

</form>

@push('scripts')
<script>
function poEditForm() {
    return {
        customerId: '{{ $purchaseOrder->customer_id }}',
        outlets: [],
        items: @json($purchaseOrder->items->map(fn($i) => ['barang_id' => $i->barang_id, 'qty' => $i->qty])),

        init() {
            this.loadOutlets();
        },

        loadOutlets() {
            if (!this.customerId) return;
            fetch(`/customers/${this.customerId}/outlets-json`)
                .then(r => r.json())
                .then(data => { this.outlets = data; });
        },

        addItem() {
            this.items.push({ barang_id: '', qty: '' });
        },

        removeItem(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        }
    }
}
</script>
@endpush
</x-app-layout>
