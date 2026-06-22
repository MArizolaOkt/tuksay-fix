---
name: purchase-order-system-update
description: >
  Panduan untuk memodifikasi sistem aplikasi web Purchase Order (PO). Gunakan skill ini
  ketika ada permintaan perubahan pada modul purchase order, termasuk: penambahan field
  tanggal, logika kode PO unik per customer/outlet, validasi margin kotor, tampilan
  quantity dengan satuan dan desimal, atau penghapusan komponen biaya. Trigger skill ini
  setiap kali user menyebutkan "purchase order", "PO", "kode PO", "margin kotor",
  "satuan barang", atau "biaya operasional" dalam konteks perubahan sistem.
---

# Purchase Order System Update

Skill ini memandu agent AI dalam mengimplementasikan 5 perubahan spesifik pada sistem
aplikasi web Purchase Order. Ikuti setiap bagian secara berurutan, dan konfirmasi dengan
user sebelum menyentuh data produksi.

---

## Perubahan 1 — Tambahkan Field "Tanggal Kirim" dan "Tanggal PO"

### Tujuan
Menambahkan dua field tanggal baru pada form dan tabel Purchase Order.

### Langkah Implementasi

**A. Skema Database**

Tambahkan dua kolom baru pada tabel `purchase_orders` (atau nama tabel PO yang relevan):

```sql
ALTER TABLE purchase_orders
  ADD COLUMN tanggal_po   DATE NOT NULL DEFAULT CURRENT_DATE,
  ADD COLUMN tanggal_kirim DATE NULL;
```

> Catatan: `tanggal_po` wajib diisi (NOT NULL), `tanggal_kirim` boleh kosong karena
> tanggal pengiriman mungkin belum diketahui saat PO dibuat.

**B. Form UI (HTML/Frontend)**

Tambahkan dua input di form PO, letakkan di atas atau di bawah field nomor PO:

```html
<!-- Tanggal PO -->
<div class="form-group">
  <label for="tanggal_po">Tanggal PO <span class="required">*</span></label>
  <input type="date" id="tanggal_po" name="tanggal_po" required
         value="{{ today_date }}" />
</div>

<!-- Tanggal Kirim -->
<div class="form-group">
  <label for="tanggal_kirim">Tanggal Kirim</label>
  <input type="date" id="tanggal_kirim" name="tanggal_kirim" />
</div>
```

**C. Tabel Daftar PO**

Tambahkan dua kolom pada tabel rekap/list PO:

| Kolom Baru     | Posisi Disarankan       | Format Tampilan |
|----------------|------------------------|-----------------|
| Tanggal PO     | Setelah Nomor PO       | DD/MM/YYYY      |
| Tanggal Kirim  | Setelah Tanggal PO     | DD/MM/YYYY atau "-" jika kosong |

**D. Backend / API**

- Validasi: `tanggal_po` tidak boleh kosong; `tanggal_kirim` jika diisi harus ≥ `tanggal_po`.
- Sertakan kedua field dalam response API GET dan payload POST/PUT.

---

## Perubahan 2 — Kode PO Unik per Tipe Customer dan Outlet

### Tujuan
Setiap Purchase Order memiliki kode unik yang mencerminkan tipe customer dan outlet asal,
sehingga kode PO tidak bertabrakan antar entitas dan mudah dilacak.

### Format Kode PO yang Disarankan

```
[KODE_TIPE_CUSTOMER]-[KODE_OUTLET]-[TAHUN][BULAN]-[SEQUENCE]

Contoh:
  RTL-JKT01-202506-0001   → Retail, Outlet Jakarta-01, Juni 2025, urutan ke-1
  WHL-BDG02-202506-0023   → Wholesale, Outlet Bandung-02, Juni 2025, urutan ke-23
  HRC-SBY03-202506-0005   → HoReCa, Outlet Surabaya-03, Juni 2025, urutan ke-5
```

> Sesuaikan prefix tipe customer dan kode outlet dengan data aktual sistem Anda.

### Logika Generate Kode PO (Pseudocode)

```python
def generate_kode_po(tipe_customer_code, outlet_code, tanggal_po):
    year  = tanggal_po.strftime("%Y")
    month = tanggal_po.strftime("%m")
    prefix = f"{tipe_customer_code}-{outlet_code}-{year}{month}"

    # Hitung sequence terakhir untuk kombinasi prefix ini
    last_seq = db.query("""
        SELECT MAX(sequence_number)
        FROM purchase_orders
        WHERE kode_po LIKE :prefix
    """, prefix=f"{prefix}-%")

    next_seq = (last_seq or 0) + 1
    kode_po  = f"{prefix}-{str(next_seq).zfill(4)}"
    return kode_po
```

### Implementasi Database

Tambahkan kolom dan constraint unique:

```sql
ALTER TABLE purchase_orders
  ADD COLUMN kode_po VARCHAR(30) NOT NULL UNIQUE;

-- Opsional: index untuk performa query
CREATE UNIQUE INDEX uq_kode_po ON purchase_orders (kode_po);
```

### Catatan Penting

- Kode PO di-generate otomatis di backend saat PO pertama kali disimpan (bukan diinput manual).
- Tampilkan kode PO sebagai read-only di form setelah PO disimpan.
- Jika sistem mendukung draft PO, generate kode baru hanya saat status berubah ke "Confirmed".
- Pastikan logika generate kode berjalan dalam database transaction untuk menghindari
  race condition (dua PO mendapat kode yang sama secara bersamaan).

---

## Perubahan 3 — Peringatan Jika Estimasi Margin Kotor Melebihi Batas Wajar

### Tujuan
Menambahkan validasi dan peringatan visual pada form PO ketika harga beli yang dimasukkan
menghasilkan estimasi margin kotor > 100% (atau ambang batas lain yang ditentukan bisnis).

### Definisi Margin Kotor

```
Margin Kotor (%) = ((Harga Jual - Harga Beli) / Harga Jual) × 100

Kondisi peringatan: Margin Kotor > 100%  →  artinya Harga Beli ≤ 0 atau data tidak wajar
Kondisi alternatif yang perlu dicek: Harga Beli > Harga Jual  →  margin negatif (rugi)
```

> Margin kotor tidak mungkin melebihi 100% secara matematis jika harga jual > 0 dan
> harga beli > 0. Jika terjadi, kemungkinan besar ada kesalahan input data.
> Konfirmasikan dengan user: apakah "melebihi 100%" dimaksudkan sebagai harga beli > harga jual?

### Implementasi Frontend (JavaScript)

```javascript
function hitungMarginKotor(hargaBeli, hargaJual) {
  if (!hargaJual || hargaJual <= 0) return null;
  return ((hargaJual - hargaBeli) / hargaJual) * 100;
}

function validasiMargin() {
  const hargaBeli = parseFloat(document.getElementById('harga_beli').value) || 0;
  const hargaJual = parseFloat(document.getElementById('harga_jual').value) || 0;
  const margin    = hitungMarginKotor(hargaBeli, hargaJual);
  const warningEl = document.getElementById('margin-warning');

  if (margin === null) {
    warningEl.style.display = 'none';
    return;
  }

  if (margin > 100 || hargaBeli > hargaJual) {
    warningEl.style.display  = 'block';
    warningEl.innerHTML = `
      ⚠️ <strong>Peringatan:</strong> Estimasi margin kotor tidak wajar
      (${margin.toFixed(1)}%). Mohon periksa kembali harga beli dan harga jual.
    `;
    warningEl.className = 'alert alert-danger';
  } else if (margin < 0) {
    warningEl.style.display  = 'block';
    warningEl.innerHTML = `
      ⚠️ <strong>Perhatian:</strong> Harga beli lebih tinggi dari harga jual.
      Margin negatif (${margin.toFixed(1)}%).
    `;
    warningEl.className = 'alert alert-warning';
  } else {
    warningEl.style.display = 'none';
  }
}

// Pasang event listener
document.getElementById('harga_beli').addEventListener('input', validasiMargin);
document.getElementById('harga_jual').addEventListener('input', validasiMargin);
```

### Elemen HTML Peringatan

```html
<div id="margin-warning" class="alert" style="display:none;" role="alert"></div>
```

### Validasi Backend (Opsional tapi Disarankan)

```python
MARGIN_WARNING_THRESHOLD = 100  # persen

def validasi_margin(harga_beli, harga_jual):
    if harga_jual <= 0:
        raise ValueError("Harga jual harus lebih dari 0.")
    margin = ((harga_jual - harga_beli) / harga_jual) * 100
    if margin > MARGIN_WARNING_THRESHOLD or harga_beli > harga_jual:
        # Simpan dengan flag warning, atau tolak tergantung kebijakan bisnis
        return {"warning": True, "margin": margin}
    return {"warning": False, "margin": margin}
```

---

## Perubahan 4 — Tampilan Quantity dengan Satuan dan Format Desimal

### Tujuan
Menampilkan quantity barang beserta satuan (unit) dan format desimal yang rapi di seluruh
tampilan: form input, tabel list, dan detail PO.

### Format Tampilan yang Diharapkan

```
Sebelum:  10
Sesudah:  10 Kg
          5.50 Liter
          100 Pcs
          0.25 Karton
```

### Aturan Format Desimal

| Kondisi                  | Format          | Contoh       |
|--------------------------|-----------------|--------------|
| Bilangan bulat           | Tanpa desimal   | 10 Pcs       |
| Ada desimal              | Maks 2 desimal  | 5.50 Kg      |
| Input user               | Terima desimal  | field: `step="0.01"` |

### Implementasi Frontend

**Input Form:**

```html
<div class="form-group qty-group">
  <label for="quantity">Quantity</label>
  <div class="input-with-unit">
    <input type="number" id="quantity" name="quantity"
           min="0" step="0.01" placeholder="0.00" required />
    <span class="unit-label" id="unit-display"><!-- diisi dinamis --></span>
  </div>
</div>
```

**Fungsi Format Quantity (JavaScript):**

```javascript
function formatQuantity(qty, satuan) {
  const num = parseFloat(qty);
  if (isNaN(num)) return '-';

  // Tampilkan desimal hanya jika ada bagian pecahan
  const formatted = Number.isInteger(num)
    ? num.toString()
    : num.toFixed(2);

  return satuan ? `${formatted} ${satuan}` : formatted;
}

// Contoh penggunaan di tabel:
// formatQuantity(10, 'Kg')     → "10 Kg"
// formatQuantity(5.5, 'Liter') → "5.50 Liter"
// formatQuantity(100, 'Pcs')   → "100 Pcs"
```

**Render Tabel PO:**

```javascript
// Dalam fungsi render row tabel
const qtyCell = `<td class="text-right">${formatQuantity(item.quantity, item.satuan)}</td>`;
```

### Database

Pastikan kolom `satuan` tersedia pada tabel item PO:

```sql
-- Jika belum ada:
ALTER TABLE purchase_order_items
  ADD COLUMN satuan VARCHAR(20) NOT NULL DEFAULT 'Pcs';
```

### Catatan

- Satuan diambil dari master data barang (tabel produk/item), bukan diinput manual per PO.
- Jika satu barang bisa memiliki beberapa satuan (Pcs, Karton, dll), sediakan dropdown
  konversi satuan di form PO item.

---

## Perubahan 5 — Hapus Komponen Biaya Operasional

### Tujuan
Menghilangkan field, kolom, dan kalkulasi "Biaya Operasional" dari seluruh sistem PO.

### Checklist Penghapusan

Pastikan semua titik berikut dihapus atau dinonaktifkan:

**Frontend / UI:**
- [ ] Field input "Biaya Operasional" di form PO
- [ ] Kolom "Biaya Operasional" di tabel list PO
- [ ] Baris "Biaya Operasional" di halaman detail / summary PO
- [ ] Kalkulasi total yang menyertakan biaya operasional
- [ ] Label, tooltip, atau help text yang merujuk biaya operasional

**Backend / Logika:**
- [ ] Hapus parameter `biaya_operasional` dari fungsi kalkulasi total
- [ ] Hapus validasi yang berkaitan dengan biaya operasional
- [ ] Update formula total PO:
  ```
  Sebelum: Total = Subtotal + Biaya Operasional + Pajak
  Sesudah: Total = Subtotal + Pajak
  ```

**Database:**
- [ ] Jangan langsung DROP kolom — arsipkan terlebih dahulu:
  ```sql
  -- Langkah 1: Backup nilai (opsional, untuk audit)
  ALTER TABLE purchase_orders
    RENAME COLUMN biaya_operasional TO biaya_operasional_deprecated;

  -- Langkah 2: Set default 0 agar tidak mengganggu record lama
  UPDATE purchase_orders
    SET biaya_operasional_deprecated = 0
    WHERE biaya_operasional_deprecated IS NULL;

  -- Langkah 3: Setelah yakin tidak ada referensi, hapus kolom
  -- (jalankan setelah deploy dan verifikasi)
  -- ALTER TABLE purchase_orders DROP COLUMN biaya_operasional_deprecated;
  ```

**Laporan / Export:**
- [ ] Hapus kolom biaya operasional dari template cetak PO (PDF/Excel)
- [ ] Update laporan rekapitulasi yang mungkin menampilkan kolom ini

### Urutan Deployment yang Disarankan

1. Deploy perubahan backend (hapus logika kalkulasi)
2. Deploy perubahan frontend (hapus field & kolom)
3. Jalankan migrasi database (rename kolom dulu, jangan langsung drop)
4. Verifikasi di staging/testing
5. Deploy ke produksi
6. Setelah 1-2 sprint, baru drop kolom dari database

---

## Urutan Implementasi yang Disarankan

Untuk meminimalkan risiko konflik dan bug, implementasikan perubahan dalam urutan ini:

```
1. Perubahan 5 (Hapus Biaya Operasional)  ← Bersihkan dulu yang tidak perlu
2. Perubahan 1 (Tambah Tanggal PO & Kirim) ← Skema database
3. Perubahan 2 (Kode PO Unik)              ← Logika generate kode
4. Perubahan 4 (Quantity + Satuan)         ← UI dan format
5. Perubahan 3 (Peringatan Margin)         ← Validasi terakhir
```

---

## Checklist Final Sebelum Deploy

- [ ] Semua migrasi database sudah ditest di environment staging
- [ ] Kode PO yang di-generate tidak menghasilkan duplikat (test concurrent insert)
- [ ] Peringatan margin muncul dan hilang sesuai kondisi
- [ ] Field quantity menampilkan satuan dan desimal dengan benar di semua halaman
- [ ] Biaya operasional tidak muncul di form, tabel, detail, dan laporan cetak
- [ ] Tanggal Kirim dan Tanggal PO tersimpan dan tampil dengan format yang konsisten
- [ ] Tidak ada error pada data PO lama setelah migrasi
