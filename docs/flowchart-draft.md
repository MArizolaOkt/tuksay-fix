# Flowchart Sistem TUKSAY — Draft Teks

> **Keterangan Simbol:**
> - `[...]` = Proses (persegi panjang)
> - `<...>` = Keputusan / Decision (diamond)
> - `(...)` = Terminal Start/End (oval)
> - `{...}` = Input/Output (parallelogram)
> - `//...//` = Proses Otomatis Sistem

---

## ▶ MULAI

```
(MULAI)
```

---

## FASE 0 — AUTENTIKASI PENGGUNA

```
(MULAI)
   │
   ▼
[Akses Aplikasi Web TUKSAY]
   │
   ▼
<Sudah Login?>
   │ YA                    │ TIDAK
   ▼                       ▼
[Redirect ke          {Form Login: email + password}
 Dashboard Utama]          │
                           ▼
                    [Validasi Kredensial]
                           │
                   <Kredensial Valid?>
                    │              │
                  TIDAK            YA
                    │              │
                    ▼              ▼
              {Tampilkan      //Buat Session//
              Pesan Error}        │
                                  ▼
                           [Redirect ke Dashboard Utama]
```

---

## FASE 1 — SETUP MASTER DATA

```
[Dashboard Utama]
   │
   ├──────────────────────────────────────────┐
   ▼                                          │
[Master Pelanggan]                    [Master Barang]
  Input:                                Input:
  - Nama Perusahaan                     - Nama Produk
  - Kode Outlet                         - Satuan (kg/ikat/buah/pcs)
  - Alamat                              - Harga Jual
  - Informasi Pembayaran                - Harga Beli Pasar
   │                                          │
   └──────────────────┬───────────────────────┘
                      │
                      ▼
              [Biaya Operasional]
                Input:
                - Packaging
                - Transportasi/Komunikasi
                - Biaya Lain-lain
                      │
                      ▼
              [Data Master Tersimpan ✓]
```

---

## FASE 2 — PURCHASE ORDER (PO)

```
[Dashboard Utama]
   │
   ▼
{Input Order dari Pelanggan}
   Input:
   - Pilih Customer/Outlet
   - Tanggal PO
   - Item & Qty pesanan
   │
   ▼
//Generate Nomor PO Otomatis//
   Format: PO-000001 (auto-increment 6 digit)
   │
   ▼
[Simpan PO + Item Detail]
   Status awal: BARU
   │
   ▼
<Perlu Edit PO?>
   │ YA                    │ TIDAK
   ▼                       ▼
[Edit / Hapus PO]     [PO Aktif → Masuk Daftar Order]
(tampil form edit)
   │
   └──────────────────────────────┘
                      │
                      ▼
             [Daftar PO Aktif Tersedia]
```

---

## FASE 3 — KONSOLIDASI & BELANJA HARIAN

```
[Daftar PO Aktif Tersedia]
   │
   ▼
//Konsolidasi Daftar Belanja Otomatis//
   - Agregat semua PO per tanggal
   - Kelompokkan per Customer/Outlet
   - Format: BARU/PROSES/SELESAI
   │
   ▼
[Cetak / Lihat Daftar Belanja]
   Output: Daftar item yang perlu dibeli di pasar
   │
   ▼
{Input Harga Beli Pasar}
   - Harga aktual per item hari itu
   - Bisa berbeda tiap hari
   │
   ▼
//Hitung Total Modal Pembelian//
   Rumus: Σ (harga_beli × qty)
   Tersimpan sebagai dasar invoice
   │
   ▼
[Daftar Belanja Selesai ✓]
```

---

## FASE 4 — LOGISTIK / SURAT JALAN (SJ)

```
[Daftar Belanja Selesai]
   │
   ▼
[Buat Surat Jalan per Outlet]
   - Dikelompokkan per customer_id + customer_outlet_id
   │
   ▼
//Generate Nomor SJ Otomatis//
   Format: SJ-000001 (update status PO → PROSES)
   │
   ▼
<Tampilkan Harga Jual di SJ?>
   │ YA                    │ TIDAK
   ▼                       ▼
[Tampilkan            [Sembunyikan
 Kolom Harga]          Kolom Harga]
   │                       │
   └───────────┬───────────┘
               ▼
        [Cetak Surat Jalan (A4)]
        Otomatis download / print
               │
               ▼
        [Antar ke Outlet Pelanggan]
               │
               ▼
        [Pengiriman Selesai ✓]
        Status PO → SELESAI (opsional)
```

---

## FASE 5 — PENAGIHAN (INVOICE)

```
[Daftar SJ / PO Selesai]
   │
   ▼
<Periode Tagihan?>
   │ MINGGUAN              │ BULANAN
   (Senin–Minggu)          (1–Akhir Bulan)
   │                       │
   └───────────┬───────────┘
               │
               ▼
   //Generate Invoice per Pelanggan//
      - Nomor: INV-000001
      - Status PO: PROSES → SELESAI
      - Detail per SJ yang tercakup
               │
               ▼
   //Hitung Total Tagihan//
      Rumus: Σ (qty × harga_jual) per PO
      Generate As: Invoice INV-XXXXXX
               │
               ▼
   [Simpan Invoice + Update Status PO → SELESAI]
               │
               ▼
   [Kirim / Cetak Invoice (A4) ke Pelanggan]
               │
               ▼
   <Pelanggan Membayar?>
      │ YA                    │ TIDAK
      ▼                       ▼
   [Tandai Invoice       [Tunggu Jatuh
    LUNAS → TERBIT]       Tempo TOP]
```

---

## FASE 6 — LAPORAN KEUANGAN & ANALITIK

```
[Data Invoice + Belanja Terkumpul]
   │
   ▼
[Finance Dashboard]
   Menampilkan:
   - Total Pendapatan (Invoice Lunas)
   - Total Pengeluaran (Modal Belanja + Biaya Ops)
   - Profit Bersih
   │
   ├─────────────────┬──────────────────┐
   ▼                 ▼                  ▼
[Price Trend]   [P&L Report]     [Margin Analysis]
 Grafik harga    Laba/Rugi         Margin per item
 beli per item   per periode       atau per outlet
   │                 │                  │
   └─────────────────┴──────────────────┘
                     │
                     ▼
              [Export / Cetak Laporan]

```

---

## ⏹ SELESAI

```
(SELESAI)
```

---

## Ringkasan Alur Utama (Linear)

```
MULAI
  └─▶ Login
        └─▶ Setup Master Data (Pelanggan, Barang, Biaya)
              └─▶ Input Purchase Order (PO)
                    └─▶ Konsolidasi Daftar Belanja
                          └─▶ Input Harga Beli Pasar
                                └─▶ Buat Surat Jalan (SJ)
                                      └─▶ Generate Invoice
                                            └─▶ Pembayaran / Penagihan
                                                  └─▶ Laporan Keuangan
                                                        └─▶ SELESAI
```

---

## Catatan Teknis

| Entitas | Keterangan |
|---|---|
| **PO** | Nomor auto-increment `PO-000001`, status: BARU → PROSES → SELESAI |
| **SJ** | Nomor auto-increment `SJ-000001`, dikelompokkan per outlet |
| **Invoice** | Nomor auto-increment `INV-000001`, bisa mingguan/bulanan |
| **Harga Beli** | Diinput manual tiap hari, bisa berbeda dari harga master |
| **Harga Jual** | Dari master barang, tampil/sembunyikan di SJ (toggle) |
| **Status Invoice** | TERBIT → LUNAS |
| **TOP** | Terms of Payment (jatuh tempo pembayaran pelanggan) |
