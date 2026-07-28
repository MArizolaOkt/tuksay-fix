# Dokumen Black Box Testing - Sistem Tuksay Proto

**Status Server**: `php artisan serve` aktif (Running pada `http://127.0.0.1:8000`, PID: `20580`)  
**Metode Pengujian**: Black Box Testing (Equivalence Partitioning, Boundary Value Analysis, State Transition, & Positive/Negative Scenarios)  
**Tanggal Pengujian**: 27 Juli 2026  

---

## 📋 Daftar Isi
1. [Pendahuluan & Lingkup Pengujian](#1-pendahuluan--lingkup-pengujian)
2. [Lingkungan Pengujian (Test Environment)](#2-lingkungan-pengujian-test-environment)
3. [Matriks Hak Akses & Peran (Role Matrix)](#3-matriks-hak-akses--peran-role-matrix)
4. [Skenario Test Case Black Box](#4-skenario-test-case-black-box)
   - [Modul 1: Autentikasi & Profile Management](#modul-1-autentikasi--profile-management)
   - [Modul 2: Master Data Customer & Outlets](#modul-2-master-data-customer--outlets)
   - [Modul 3: Master Data Barang](#modul-3-master-data-barang)
   - [Modul 4: Manajemen User](#modul-4-manajemen-user)
   - [Modul 5: Purchase Order (PO) Management](#modul-5-purchase-order-po-management)
   - [Modul 6: Belanja & Konsolidasi Item](#modul-6-belanja--konsolidasi-item)
   - [Modul 7: Logistik & Surat Jalan](#modul-7-logistik--surat-jalan)
   - [Modul 8: Invoicing & Status Pembayaran](#modul-8-invoicing--status-pembayaran)
   - [Modul 9: Finance Report & Analytics](#modul-9-finance-report--analytics)
5. [Ringkasan Eksekusi Test Suite](#5-ringkasan-eksekusi-test-suite)

---

## 1. Pendahuluan & Lingkup Pengujian
Dokumen ini berisi spesifikasi skenario dan test case **Black Box Testing** untuk aplikasi web **Tuksay Proto**. Pengujian difokuskan pada pengujian fungsionalitas dari sudut pandang pengguna akhir (end-user) tanpa melihat struktur kode internal, meliputi antarmuka pengguna (UI), alur transaksi (workflow), validasi input, serta pengontrolan hak akses berdasarkan peran (**Admin** dan **Staff**).

---

## 2. Lingkungan Pengujian (Test Environment)

| Parameter | Spesifikasi / Status |
| :--- | :--- |
| **URL Server** | `http://127.0.0.1:8000` |
| **Status Service** | Active (`php artisan serve` running) |
| **Framework** | Laravel |
| **Database** | MySQL / MariaDB (Laragon Environment) |
| **Peramban (Browser)** | Google Chrome / Microsoft Edge / Mozilla Firefox |

---

## 3. Matriks Hak Akses & Peran (Role Matrix)

| Fitur / Modul | Guest (Unauthenticated) | Staff | Admin |
| :--- | :---: | :---: | :---: |
| Halaman Login (`/login`) | ✅ | ❌ (Redirect) | ❌ (Redirect) |
| Redirect Landing (`/`) | Redirect Login | Redirect `/belanja/konsolidasi` | Redirect `/dashboard` |
| Profile User (`/profile`) | ❌ | ✅ | ✅ |
| Dashboard (`/dashboard`) | ❌ | ❌ (403/Forbidden) | ✅ |
| Master Customers (`/customers`) | ❌ | ❌ | ✅ |
| Master Barang (`/barangs`) | ❌ | ❌ | ✅ |
| User Management (`/users`) | ❌ | ❌ | ✅ |
| Purchase Order (`/purchase-orders`) | ❌ | ❌ | ✅ |
| Konsolidasi Belanja (`/belanja/konsolidasi`) | ❌ | ✅ | ✅ |
| Input Harga Belanja (`/belanja/harga`) | ❌ | ✅ | ✅ |
| Logistik / Surat Jalan (`/logistik`) | ❌ | ❌ | ✅ |
| Invoices & Cetak (`/invoices`) | ❌ | ❌ | ✅ |
| Laporan Keuangan (`/finance/*`) | ❌ | ❌ | ✅ |

---

## 4. Skenario Test Case Black Box

### Modul 1: Autentikasi & Profile Management

| Test Case ID | Nama Skenario | Langkah Pengujian | Input Data | Hasil yang Diharapkan | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-AUTH-01** | Login Sukses sebagai Admin | 1. Buka `/login`<br>2. Masukkan email & password Admin<br>3. Klik button "Log in" | Email: `admin@tuksay.com`<br>Password: `password` | Berhasil login, di-redirect ke `/dashboard`, muncul pesan/halaman dashboard Admin. | Positive |
| **TC-AUTH-02** | Login Sukses sebagai Staff | 1. Buka `/login`<br>2. Masukkan email & password Staff<br>3. Klik button "Log in" | Email: `staff@tuksay.com`<br>Password: `password` | Berhasil login, di-redirect ke `/belanja/konsolidasi`. | Positive |
| **TC-AUTH-03** | Login Gagal (Password Salah) | 1. Buka `/login`<br>2. Masukkan email benar, password salah<br>3. Klik "Log in" | Email: `admin@tuksay.com`<br>Password: `salah123` | Login gagal, pesan error "These credentials do not match our records." muncul. | Negative |
| **TC-AUTH-04** | Form Validation (Email Kosong) | 1. Buka `/login`<br>2. Kosongkan email, isi password<br>3. Klik "Log in" | Email: ` ` (kosong)<br>Password: `password` | Form menolak submit, HTML5 validation / Laravel validation memunculkan error field required. | Boundary / EP |
| **TC-AUTH-05** | Logout Sistem | 1. Login sebagai user<br>2. Klik tombol Logout pada navbar | Klik "Log Out" | Session dihancurkan, user di-redirect kembali ke halaman `/login`. | Positive |
| **TC-AUTH-06** | Update Informasi Profil | 1. Buka `/profile`<br>2. Ubah Nama dan Email<br>3. Klik "Save" | Name: `Admin Tuksay Updated`<br>Email: `admin.new@tuksay.com` | Profil berhasil diperbarui, notifikasi sukses ditampilkan. | Positive |

---

### Modul 2: Master Data Customer & Outlets

| Test Case ID | Nama Skenario | Langkah Pengujian | Input Data | Hasil yang Diharapkan | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-CUST-01** | Tambah Customer Baru (Valid) | 1. Login Admin<br>2. Buka `/customers/create`<br>3. Isi form Customer<br>4. Klik "Simpan" | Nama: `Resto Sedap Rasa`<br>Kode: `CUST-001`<br>Telepon: `08123456789`<br>Alamat: `Jl. Merdeka No. 10` | Customer tersimpan di DB, di-redirect ke `/customers` dengan notifikasi sukses. | Positive |
| **TC-CUST-02** | Tambah Customer tanpa Kode / Nama | 1. Buka `/customers/create`<br>2. Kosongkan Nama Customer<br>3. Klik "Simpan" | Nama: ` `<br>Kode: `CUST-002` | Gagal simpan, pesan error "The name field is required." muncul. | Negative |
| **TC-CUST-03** | Tambah Outlet pada Customer | 1. Pilih customer pada `/customers`<br>2. Klik "Tambah Outlet"<br>3. Isi form outlet<br>4. Submit | Nama Outlet: `Cabang Sudirman`<br>Alamat: `Jl. Sudirman No. 45` | Outlet terhubung dengan Customer, tampil di list outlet customer tersebut. | Positive |
| **TC-CUST-04** | Edit Data Customer | 1. Buka `/customers/{id}/edit`<br>2. Ubah Telepon & Alamat<br>3. Klik "Update" | Telepon: `08987654321` | Data customer berhasil diperbarui di database. | Positive |
| **TC-CUST-05** | Hapus Data Customer | 1. Buka `/customers`<br>2. Klik ikon "Hapus" pada salah satu baris<br>3. Konfirmasi modal delete | ID Customer: `1` | Customer terhapus dari daftar (soft delete / hard delete). | Positive |

---

### Modul 3: Master Data Barang

| Test Case ID | Nama Skenario | Langkah Pengujian | Input Data | Hasil yang Diharapkan | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-BRG-01** | Tambah Barang Baru (Valid) | 1. Login Admin<br>2. Buka `/barangs/create`<br>3. Isi form barang<br>4. Klik "Simpan" | Kode: `BRG-001`<br>Nama: `Bawang Merah Super`<br>Satuan: `Kg`<br>Kategori: `Bumbu` | Barang baru tersimpan, muncul di tabel daftar barang. | Positive |
| **TC-BRG-02** | Duplikasi Kode Barang | 1. Buka `/barangs/create`<br>2. Input Kode Barang yang sudah ada<br>3. Klik "Simpan" | Kode: `BRG-001` (Existing) | Gagal simpan, pesan error validation unique constraint pada kode barang. | Negative |
| **TC-BRG-03** | Edit Data Barang | 1. Buka `/barangs/{id}/edit`<br>2. Ubah Satuan dari `Kg` ke `Gram`<br>3. Simpan | Satuan: `Gram` | Satuan barang berhasil diperbarui. | Positive |
| **TC-BRG-04** | Hapus Barang | 1. Klik "Hapus" pada barang di `/barangs`<br>2. Konfirmasi hapus | ID Barang: `BRG-001` | Barang terhapus dari sistem. | Positive |

---

### Modul 4: Manajemen User

| Test Case ID | Nama Skenario | Langkah Pengujian | Input Data | Hasil yang Diharapkan | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-USR-01** | Tambah User Baru (Role Staff) | 1. Login Admin<br>2. Buka `/users/create`<br>3. Isi Nama, Email, Password, Role=Staff<br>4. Klik "Simpan" | Nama: `Budi Staff`<br>Email: `budi@tuksay.com`<br>Role: `staff` | User baru bertipe Staff terbuat. User dapat login sebagai Staff. | Positive |
| **TC-USR-02** | Tambah User Baru (Role Admin) | 1. Buka `/users/create`<br>2. Isi data & pilih Role=Admin<br>3. Simpan | Nama: `Siti Admin`<br>Email: `siti@tuksay.com`<br>Role: `admin` | User baru bertipe Admin terbuat. | Positive |
| **TC-USR-03** | Pembatasan Akses Staff ke User Management | 1. Login sebagai Staff<br>2. Akses URL `/users` secara langsung | URL: `http://127.0.0.1:8000/users` | Halaman menolak akses dengan HTTP 403 Forbidden atau redirect dengan pesan warning. | Negative / Security |

---

### Modul 5: Purchase Order (PO) Management

| Test Case ID | Nama Skenario | Langkah Pengujian | Input Data | Hasil yang Diharapkan | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-PO-01** | Buat Purchase Order Baru | 1. Login Admin<br>2. Buka `/purchase-orders/create`<br>3. Pilih Customer & Outlet<br>4. Tambah item barang & kuantitas<br>5. Klik "Simpan PO" | Customer: `Resto Sedap`<br>Item 1: `Bawang Merah` (Qty: 10 Kg)<br>Item 2: `Cabai Rawit` (Qty: 5 Kg) | PO berhasil terbuat dengan status awal `draft` / `pending`. | Positive |
| **TC-PO-02** | Buat PO Tanpa Item Barang | 1. Buka `/purchase-orders/create`<br>2. Pilih Customer tanpa menambah item barang<br>3. Klik "Simpan PO" | Customer: `Resto Sedap`<br>Item: Kosong | Sistem menolak pembuatan PO, menampilkan pesan "Minimal 1 item barang harus ditambahkan." | Negative / BVA |
| **TC-PO-03** | Update Status PO | 1. Buka `/purchase-orders/{id}`<br>2. Ubah status PO menjadi `Approved` / `Proses Belanja` | Status: `Approved` | Status PO terupdate, data terdistribusi ke modul Belanja. | State Transition |
| **TC-PO-04** | Hapus Purchase Order | 1. Buka `/purchase-orders`<br>2. Klik tombol "Delete" pada PO status draft | PO ID: `PO-2026-001` | PO terhapus dari sistem. | Positive |

---

### Modul 6: Belanja & Konsolidasi Item

| Test Case ID | Nama Skenario | Langkah Pengujian | Input Data | Hasil yang Diharapkan | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-BLJ-01** | Akses Konsolidasi Belanja oleh Staff | 1. Login sebagai Staff<br>2. Akses `/belanja/konsolidasi` | Navigation item "Konsolidasi Belanja" | Halaman menampilkan ringkasan akumulasi kebutuhan barang dari semua PO yang aktif. | Positive |
| **TC-BLJ-02** | Input Harga Realisasi Belanja oleh Staff | 1. Buka `/belanja/konsolidasi`<br>2. Isi harga riil pembelian per item<br>3. Klik "Simpan Harga Belanja" | Item `Bawang Merah`: Rp 35.000/Kg<br>Item `Cabai Rawit`: Rp 45.000/Kg | Harga realisasi belanja tersimpan, modal/COGS tercatat untuk laporan keuangan. | Positive |
| **TC-BLJ-03** | Input Harga Belanja Nilai Negatif | 1. Pada form `/belanja/harga`<br>2. Input harga `-5000`<br>3. Klik Simpan | Harga: `-5000` | Validation error: "Harga tidak boleh bernilai negatif." | Negative / BVA |
| **TC-BLJ-04** | Admin Melihat Detail Daftar Belanja | 1. Login Admin<br>2. Buka `/belanja`<br>3. Pilih daftar belanja | ID Belanja: `BLJ-001` | Rincian belanja beserta total pengeluaran riil ditampilkan dengan akurat. | Positive |

---

### Modul 7: Logistik & Surat Jalan

| Test Case ID | Nama Skenario | Langkah Pengujian | Input Data | Hasil yang Diharapkan | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-LOG-01** | Generate Surat Jalan dari PO | 1. Login Admin<br>2. Buka `/logistik/create`<br>3. Pilih PO yang siap dikirim<br>4. Klik "Generate Surat Jalan" | Select PO: `PO-2026-001` | Surat Jalan (SJ) berhasil dibuat dengan nomor otomatis (misal: `SJ-202607-001`). | Positive |
| **TC-LOG-02** | Preview Surat Jalan | 1. Buka `/logistik/{id}` | ID Surat Jalan: `SJ-001` | Menampilkan detail barang, penerima (outlet customer), dan driver/ekspedisi. | Positive |
| **TC-LOG-03** | Cetak Surat Jalan (Print View) | 1. Buka `/logistik/{id}/print`<br>2. Periksa format cetak | URL: `/logistik/1/print` | Tampilan khusus siap cetak (print stylesheet), menyembunyikan navigasi web. | UI / Positive |

---

### Modul 8: Invoicing & Status Pembayaran

| Test Case ID | Nama Skenario | Langkah Pengujian | Input Data | Hasil yang Diharapkan | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-INV-01** | Generate Invoice dari Surat Jalan / PO | 1. Login Admin<br>2. Buka `/invoices/create`<br>3. Pilih Surat Jalan / PO terkait<br>4. Klik "Generate Invoice" | Target SJ: `SJ-202607-001` | Invoice terbuat dengan perhitungan harga jual + margin. Status awal `Belum Lunas` / `Unpaid`. | Positive |
| **TC-INV-02** | Tandai Invoice Lunas | 1. Buka `/invoices/{id}`<br>2. Klik tombol "Mark as Lunas" / "Tandai Lunas" | Action: `PATCH /invoices/{id}/lunas` | Status Invoice berubah menjadi `Lunas` (`Paid`), tanggal pelunasan tercatat. | State Transition |
| **TC-INV-03** | Cetak Invoice Customer | 1. Buka `/invoices/{id}/print` | Invoice ID: `INV-2026-001` | Tampilan faktur/invoice resmi untuk customer siap di-print/export PDF. | UI / Positive |

---

### Modul 9: Finance Report & Analytics

| Test Case ID | Nama Skenario | Langkah Pengujian | Input Data | Hasil yang Diharapkan | Jenis Test |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-FIN-01** | Dashboard Keuangan | 1. Login Admin<br>2. Buka `/finance/dashboard` | Filter Periode: Bulan Ini | Menampilkan metric total revenue, total belanja (COGS), dan perkiraan laba bersih. | Positive |
| **TC-FIN-02** | Tren Harga Barang | 1. Buka `/finance/price-trend`<br>2. Pilih Barang `Bawang Merah` | Item: `Bawang Merah` | Grafik/Tabel fluktuasi harga beli riil vs harga jual dari waktu ke waktu muncul. | Positive |
| **TC-FIN-03** | Laporan Laba Rugi (P&L) | 1. Buka `/finance/pl`<br>2. Pilih Rentang Tanggal | Rentang: `01/07/2026` s.d. `27/07/2026` | Laporan Laba Rugi tergenerasi dengan perhitungan otomatis (Penjualan - COGS = Laba Kotor). | Positive |
| **TC-FIN-04** | Analisis Margin Keuntungan | 1. Buka `/finance/margin` | All Products / Per Customer | Menampilkan persentase margin per item/customer. | Positive |

---

## 5. Ringkasan Eksekusi Test Suite

- **Total Test Case**: 31 Test Case  
- **Cakupan Modul**: 9 Modul Utama  
- **Metode**: Manual Functional Black Box Testing  
- **Rekomendasi Tindakan**:
  1. Eksekusi pengujian secara manual atau gunakan tools otomasi browser (Puppeteer / Playwright / Laravel Dusk) berdasarkan urutan Test Case ID.
  2. Pastikan akun **Admin** (`admin@tuksay.com`) dan **Staff** (`staff@tuksay.com`) telah disiapkan di database local.
