# Activity Diagram — TUKSAY ERP

> **Sistem:** TUKSAY - Fresh Produce Supplier Management System  
> **Standar:** UML 2.x  
> **Format:** Mermaid Flowchart (dirender di GitHub, GitLab, Obsidian, dsb.)

---

## Daftar Alur (Swimlane)

| Swimlane | Aktor |
|---|---|
| Admin / Owner | Pengguna utama, akses penuh |
| Sistem (TUKSAY ERP) | Proses otomatis backend Laravel |
| Karyawan | Akses terbatas: Daftar Belanja |

---

## 1. Alur Pembuatan Purchase Order

```mermaid
flowchart TD
    START([● Mulai]) --> A1[Buka Form\nPurchase Order]
    A1 --> A2[Input Data PO:\nCustomer, Outlet, Tanggal, No. Ref]
    A2 --> A3[Tambah Item Barang\n+ Qty]
    A3 --> A4{Data\nlengkap?}
    A4 -- Tidak --> A2
    A4 -- Ya --> A5[Submit Purchase Order]
    A5 --> S1[Validasi Input]
    S1 --> S2[Auto-generate No. PO\nFormat: PO-000001]
    S2 --> S3[Simpan ke DB\nStatus PO: baru]
    S3 --> S4[Tampilkan Konfirmasi\nPO Berhasil Dibuat]
    S4 --> END([◎ Selesai])
```

**Aktor yang terlibat:**
- `Admin/Owner` → A1 hingga A5
- `Sistem` → S1 hingga S4

---

## 2. Alur Konsolidasi Daftar Belanja

```mermaid
flowchart TD
    START([● Mulai]) --> K1[Buka Halaman\nKonsolidasi Daftar Belanja]
    K1 --> K2[Pilih / Konfirmasi\nTanggal Kirim]
    K2 --> S1[Query: Agregasi Qty PO\nper Barang, JOIN harga_belis]
    S1 --> S2[Tampilkan Tabel:\nBarang, Total Qty, Outlet, Harga Beli]
    S2 --> K3[Input Harga Beli\nper Barang pada Form Tabel]
    K3 --> K4[Klik Simpan Harga Beli]
    K4 --> S3[Validasi Input\nupdateOrCreate harga_belis]
    S3 --> S4[Hitung Total Modal:\nSUM qty x harga_beli]
    S4 --> S5[Buat / Update record\nDaftarBelanja + DaftarBelanjaItem]
    S5 --> S6[Flash: Harga berhasil disimpan\nRedirect ke Konsolidasi]
    S6 --> END([◎ Selesai])
```

**Aktor yang terlibat:**
- `Karyawan` → K1 hingga K4
- `Sistem` → S1 hingga S6

---

## 3. Alur Generate Surat Jalan

```mermaid
flowchart TD
    START([● Mulai]) --> A1[Buka Halaman Logistik\nDaftar PO status: baru]
    A1 --> A2[Pilih Outlet]
    A2 --> A3[Klik Generate Surat Jalan]
    A3 --> S1[Cari PO dengan\nstatus = baru untuk outlet tsb]
    S1 --> S2{PO\nditemukan?}
    S2 -- Tidak --> ERR[Tampilkan Pesan:\nTidak ada PO aktif]
    ERR --> END1([◎ Selesai])
    S2 -- Ya --> S3[Auto-generate No. SJ\nFormat: SJ-000001]
    S3 --> S4[Buat record SuratJalan]
    S4 --> S5[Update Status PO:\nbaru menjadi proses]
    S5 --> S6[Tampilkan Detail\nSurat Jalan]
    S6 --> A4{Cetak\nSJ?}
    A4 -- Ya --> A5[Cetak Surat Jalan A4\nmedia print]
    A4 -- Tidak --> END2([◎ Selesai])
    A5 --> END2
```

**Aktor yang terlibat:**
- `Admin/Owner` → A1 hingga A5
- `Sistem` → S1 hingga S6

---

## 4. Alur Generate Invoice

```mermaid
flowchart TD
    START([● Mulai]) --> A1[Buka Form Generate Invoice]
    A1 --> A2[Pilih Customer]
    A2 --> S1[Sistem menampilkan\nPO status proses milik Customer]
    S1 --> A3{PO\ntersedia?}
    A3 -- Tidak --> ERR[Tampilkan Pesan:\nTidak ada PO siap ditagih]
    ERR --> END1([◎ Selesai])
    A3 -- Ya --> A4[Submit Generate Invoice]
    A4 --> S2[Hitung total_tagihan:\nSUM qty x harga_jual]
    S2 --> S3[Auto-generate No. Invoice\nFormat: INV-000001]
    S3 --> S4[Buat record Invoice\nStatus: terbit]
    S4 --> S5[Update Status PO:\nproses menjadi menunggu_pembayaran]
    S5 --> S6[Tampilkan Detail Invoice]
    S6 --> A5{Cetak\nInvoice?}
    A5 -- Ya --> A6[Cetak Invoice A4]
    A5 -- Tidak --> END2([◎ Selesai])
    A6 --> END2
```

**Aktor yang terlibat:**
- `Admin/Owner` → A1 hingga A6
- `Sistem` → S1 hingga S6

---

## 5. Alur Pembayaran Invoice (Tandai Lunas)

```mermaid
flowchart TD
    START([● Mulai]) --> A1[Terima Pembayaran\ndari Customer]
    A1 --> A2[Buka Daftar Invoice\nCari Invoice yang terbit]
    A2 --> A3[Klik Tandai Invoice Lunas]
    A3 --> S1{Invoice\nberstatus terbit?}
    S1 -- Tidak --> ERR[Tampilkan Error:\nStatus tidak valid]
    ERR --> END1([◎ Selesai])
    S1 -- Ya --> S2[Update Invoice:\nterbit menjadi lunas]
    S2 --> S3[Update PO terkait:\nmenunggu_pembayaran menjadi selesai]
    S3 --> S4[Flash: Invoice lunas\nRedirect ke Daftar Invoice]
    S4 --> END2([◎ Selesai])
```

**Aktor yang terlibat:**
- `Admin/Owner` → A1 hingga A3
- `Sistem` → S1 hingga S4

---

## 6. Alur Dashboard & Laporan Keuangan

```mermaid
flowchart TD
    START([● Mulai]) --> A1[Buka Dashboard Keuangan]
    A1 --> S1[Kalkulasi KPI dari\nPO status: selesai]
    S1 --> S2[Hitung Revenue:\nSUM qty x harga_jual]
    S2 --> S3[Hitung COGS:\nSUM qty x harga_beli]
    S3 --> S4[Hitung Gross Profit:\nRevenue - COGS]
    S4 --> S5[Hitung OPEX:\nSUM biaya_operasionals]
    S5 --> S6[Hitung Net Profit:\nGross Profit - OPEX]
    S6 --> S7[Hitung BEP Harian:\nMonthly OPEX / Hari Aktif]
    S7 --> S8[Render Chart.js:\nLine, Doughnut, Bar]
    S8 --> A2[Tampilkan KPI + Grafik\nkepada Admin]
    A2 --> A3{Filter\nperiode?}
    A3 -- Ya --> A1
    A3 -- Tidak --> END([◎ Selesai])
```

**Aktor yang terlibat:**
- `Admin/Owner` → A1 hingga A3
- `Sistem` → S1 hingga S8

---

## Ringkasan Transisi Status Purchase Order

```mermaid
stateDiagram-v2
    [*] --> baru : PO dibuat
    baru --> proses : Surat Jalan di-generate
    proses --> menunggu_pembayaran : Invoice di-generate
    menunggu_pembayaran --> selesai : Invoice ditandai Lunas
    selesai --> [*]
```

---

## Contoh Swimlane dengan subgraph

Untuk keperluan formal (skripsi/tugas akhir), gunakan `subgraph` untuk menandai swimlane:

```mermaid
flowchart LR
    subgraph Admin
        A1[Buka Form PO]
        A2[Input Data]
        A3[Submit PO]
    end
    subgraph Sistem
        S1[Validasi]
        S2[Generate No. PO]
        S3[Simpan ke DB]
    end
    A1 --> A2 --> A3 --> S1 --> S2 --> S3
```

---

## Catatan Penulisan

- Diagram ini menggunakan sintaks **Mermaid** yang dapat dirender langsung di:
  - **GitHub / GitLab** (dalam file `.md`)
  - **Obsidian** (dengan plugin Mermaid)
  - **VS Code** (dengan ekstensi Markdown Preview Mermaid Support)
  - **draw.io** / **Lucidchart** (import via plugin)
- Setiap diagram dapat dipisah menjadi diagram mandiri sesuai kebutuhan laporan atau skripsi.
- Node `A` = aktivitas oleh **Aktor** (Admin/Karyawan)
- Node `S` = aktivitas oleh **Sistem** (backend Laravel)
- Node `K` = aktivitas oleh **Karyawan** khusus

---

*TUKSAY ERP — Fresh Produce Supplier Management System*  
*Activity Diagram · UML 2.x · Draft*
