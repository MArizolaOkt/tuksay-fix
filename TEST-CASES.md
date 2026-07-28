
# TEST CASES — TUKSAY ERP SYSTEM

> **Versi:** 1.0 | **Tanggal:** Juli 2026  
> **Cakupan:** Manual testing end-to-end per modul  
> **Environment:** `http://localhost/tuksay-proto/public`  
> **Format:** TC-[MODUL]-[NOMOR] | Status: ⬜ Belum | ✅ Pass | ❌ Fail

---

## CARA MEMBACA DOKUMEN INI

| Kolom | Keterangan |
|---|---|
| **ID** | Kode unik test case |
| **Judul** | Nama singkat skenario |
| **Prasyarat** | Kondisi data yang harus ada sebelum test |
| **Langkah** | Urutan aksi yang dilakukan |
| **Expected Result** | Hasil yang diharapkan |
| **Status** | Hasil pengujian aktual |

---

## MODUL 1 — AUTENTIKASI

### TC-AUTH-001 — Login berhasil sebagai Admin
| | |
|---|---|
| **Prasyarat** | Ada user dengan role `admin` di tabel `users` |
| **Langkah** | 1. Buka `/login` · 2. Isi email & password valid · 3. Klik "Masuk" |
| **Expected** | Redirect ke `/dashboard`, halaman Dashboard tampil |
| **Status** | ⬜ |

### TC-AUTH-002 — Login berhasil sebagai Staff
| | |
|---|---|
| **Prasyarat** | Ada user dengan role `staff` di tabel `users` |
| **Langkah** | 1. Buka `/login` · 2. Isi email & password staff · 3. Klik "Masuk" |
| **Expected** | Redirect ke `/belanja/konsolidasi` (bukan /dashboard) |
| **Status** | ⬜ |

### TC-AUTH-003 — Login gagal dengan password salah
| | |
|---|---|
| **Prasyarat** | Ada user terdaftar |
| **Langkah** | 1. Buka `/login` · 2. Isi email benar + password salah · 3. Klik "Masuk" |
| **Expected** | Tetap di `/login`, muncul flash error "Kredensial tidak cocok" |
| **Status** | ⬜ |

### TC-AUTH-004 — Login gagal dengan email tidak terdaftar
| | |
|---|---|
| **Prasyarat** | - |
| **Langkah** | 1. Buka `/login` · 2. Isi email `tidakada@test.com` + password apapun · 3. Klik "Masuk" |
| **Expected** | Tetap di `/login`, muncul pesan error |
| **Status** | ⬜ |

### TC-AUTH-005 — Akses halaman protected tanpa login
| | |
|---|---|
| **Prasyarat** | Belum login (session kosong) |
| **Langkah** | 1. Langsung akses URL `/dashboard` di browser baru |
| **Expected** | Redirect ke `/login` |
| **Status** | ⬜ |

### TC-AUTH-006 — Staff tidak bisa akses halaman Admin-only
| | |
|---|---|
| **Prasyarat** | Login sebagai user role `staff` |
| **Langkah** | 1. Akses langsung URL `/dashboard` |
| **Expected** | Redirect ke `/belanja/konsolidasi` dengan flash error akses ditolak |
| **Status** | ⬜ |

### TC-AUTH-007 — Logout berhasil
| | |
|---|---|
| **Prasyarat** | Sedang login |
| **Langkah** | 1. Klik menu user · 2. Klik "Logout" |
| **Expected** | Redirect ke `/login`, session dihapus |
| **Status** | ⬜ |

### TC-AUTH-008 — Edit profil berhasil
| | |
|---|---|
| **Prasyarat** | Sedang login |
| **Langkah** | 1. Buka `/profile` · 2. Ubah nama · 3. Klik "Simpan Perubahan" |
| **Expected** | Flash success, nama terupdate |
| **Status** | ⬜ |

### TC-AUTH-009 — Ubah password berhasil
| | |
|---|---|
| **Prasyarat** | Sedang login, tahu password lama |
| **Langkah** | 1. Buka `/profile` · 2. Isi current_password, password baru, konfirmasi · 3. Submit |
| **Expected** | Flash success, bisa login dengan password baru |
| **Status** | ⬜ |

### TC-AUTH-010 — Ubah password gagal (current password salah)
| | |
|---|---|
| **Prasyarat** | Sedang login |
| **Langkah** | 1. Buka `/profile` · 2. Isi current_password yang salah · 3. Submit |
| **Expected** | Muncul error validasi "Password saat ini salah" |
| **Status** | ⬜ |


---

## MODUL 2 — MASTER DATA: CUSTOMER

### TC-CUST-001 — Tambah customer tipe Resto berhasil
| | |
|---|---|
| **Prasyarat** | Login sebagai admin |
| **Langkah** | 1. Buka `/customers/create` · 2. Isi: Nama=`Resto Maju`, Perusahaan=`PT Maju`, Tipe=`resto`, Alamat=`Jl. A No.1`, Payment=`CASH` · 3. Tambahkan 2 outlet: `Cabang Utama`, `Cabang Selatan` · 4. Klik Simpan |
| **Expected** | Redirect ke `/customers`, flash success. Customer tampil di daftar. 2 outlet tercatat |
| **Status** | ⬜ |

### TC-CUST-002 — Tambah customer tipe Catering berhasil
| | |
|---|---|
| **Prasyarat** | Login sebagai admin |
| **Langkah** | 1. Buka `/customers/create` · 2. Isi Tipe=`catering`, lengkapi field lain · 3. Simpan |
| **Expected** | Customer tersimpan tanpa outlet (outlet section tidak ada/tersembunyi untuk catering) |
| **Status** | ⬜ |

### TC-CUST-003 — Validasi field wajib kosong
| | |
|---|---|
| **Prasyarat** | Login sebagai admin |
| **Langkah** | 1. Buka `/customers/create` · 2. Submit form kosong |
| **Expected** | Muncul error validasi: nama, nama_perusahaan, tipe, alamat, payment_method wajib diisi |
| **Status** | ⬜ |

### TC-CUST-004 — Edit customer berhasil
| | |
|---|---|
| **Prasyarat** | Ada customer, login admin |
| **Langkah** | 1. Buka detail customer · 2. Klik Edit · 3. Ubah nama & payment method · 4. Simpan |
| **Expected** | Flash success, data terupdate di halaman detail |
| **Status** | ⬜ |

### TC-CUST-005 — Hapus customer berhasil (tidak ada PO)
| | |
|---|---|
| **Prasyarat** | Ada customer yang belum pernah punya PO |
| **Langkah** | 1. Buka detail customer · 2. Klik Hapus · 3. Konfirmasi |
| **Expected** | Customer terhapus, redirect ke daftar dengan flash success |
| **Status** | ⬜ |

### TC-CUST-006 — Hapus customer gagal (ada PO)
| | |
|---|---|
| **Prasyarat** | Ada customer yang sudah punya minimal 1 PO |
| **Langkah** | 1. Buka detail customer · 2. Klik Hapus · 3. Konfirmasi |
| **Expected** | Flash error "Customer tidak dapat dihapus karena memiliki Purchase Order" |
| **Status** | ⬜ |

### TC-CUST-007 — Tambah outlet ke customer Resto
| | |
|---|---|
| **Prasyarat** | Ada customer tipe `resto`, login admin |
| **Langkah** | 1. Buka detail customer · 2. Isi form tambah outlet · 3. Submit |
| **Expected** | Outlet baru muncul di daftar outlet customer |
| **Status** | ⬜ |

### TC-CUST-008 — Hapus outlet berhasil (tidak ada PO terkait)
| | |
|---|---|
| **Prasyarat** | Ada outlet yang belum digunakan di PO |
| **Langkah** | 1. Buka detail customer · 2. Klik hapus pada outlet · 3. Konfirmasi |
| **Expected** | Outlet terhapus dari daftar |
| **Status** | ⬜ |

### TC-CUST-009 — AJAX endpoint outlets-json
| | |
|---|---|
| **Prasyarat** | Ada customer tipe `resto` dengan 2 outlet |
| **Langkah** | 1. Akses `GET /customers/{id}/outlets-json` |
| **Expected** | Response JSON: `{"tipe":"resto","outlets":[{"id":1,"nama_outlet":"..."},...]}` |
| **Status** | ⬜ |

---

## MODUL 3 — MASTER DATA: BARANG

### TC-BRG-001 — Tambah barang berhasil
| | |
|---|---|
| **Prasyarat** | Login sebagai admin |
| **Langkah** | 1. Buka `/barangs/create` · 2. Isi: Nama=`Wortel`, Satuan=`kg`, Harga Jual=`8000` · 3. Simpan |
| **Expected** | Redirect ke `/barangs`, flash success, barang tampil di daftar |
| **Status** | ⬜ |

### TC-BRG-002 — Tambah barang gagal (nama sudah ada)
| | |
|---|---|
| **Prasyarat** | Sudah ada barang bernama `Wortel` |
| **Langkah** | 1. Buka `/barangs/create` · 2. Isi nama `Wortel` · 3. Simpan |
| **Expected** | Error validasi "nama sudah dipakai" |
| **Status** | ⬜ |

### TC-BRG-003 — Validasi satuan hanya enum yang valid
| | |
|---|---|
| **Prasyarat** | Login sebagai admin |
| **Langkah** | 1. Kirim POST `/barangs` dengan `satuan=liter` |
| **Expected** | Error validasi pada field satuan |
| **Status** | ⬜ |

### TC-BRG-004 — Edit nama barang dengan nama sendiri (tidak error duplikat)
| | |
|---|---|
| **Prasyarat** | Ada barang `Wortel` dengan id=5 |
| **Langkah** | 1. Edit barang tersebut · 2. Nama tetap `Wortel` · 3. Ubah harga · 4. Simpan |
| **Expected** | Sukses (unique rule exclude self) — tidak error duplikat |
| **Status** | ⬜ |

### TC-BRG-005 — Hapus barang gagal (sudah ada di PO)
| | |
|---|---|
| **Prasyarat** | Ada barang yang sudah pernah masuk PoItem |
| **Langkah** | 1. Buka `/barangs` · 2. Klik Hapus pada barang tersebut |
| **Expected** | Flash error "Barang tidak dapat dihapus karena memiliki data transaksi" |
| **Status** | ⬜ |

### TC-BRG-006 — Hapus barang berhasil (belum ada transaksi)
| | |
|---|---|
| **Prasyarat** | Ada barang baru yang belum pernah masuk PO manapun |
| **Langkah** | 1. Klik Hapus pada barang tersebut |
| **Expected** | Flash success, barang hilang dari daftar |
| **Status** | ⬜ |


---

## MODUL 4 — PURCHASE ORDER

### TC-PO-001 — Buat PO untuk customer Resto berhasil
| | |
|---|---|
| **Prasyarat** | Ada customer tipe `resto` dengan 1 outlet. Ada minimal 1 barang |
| **Langkah** | 1. Buka `/purchase-orders/create` · 2. Pilih customer Resto · 3. Pilih outlet yang muncul · 4. Isi Tanggal=hari ini, Tgl Kirim=besok · 5. Tambah 2 item barang · 6. Simpan |
| **Expected** | PO tersimpan status `baru`. No PO format `RST-[OUTLET]-[YYYYMM]-0001`. Redirect ke index dengan flash success |
| **Status** | ⬜ |

### TC-PO-002 — Buat PO untuk customer Catering berhasil
| | |
|---|---|
| **Prasyarat** | Ada customer tipe `catering`. Ada minimal 1 barang |
| **Langkah** | 1. Buka create form · 2. Pilih customer Catering · 3. Isi Nama Event=`Pernikahan Budi` · 4. Isi tanggal, items · 5. Simpan |
| **Expected** | PO tersimpan. No PO format `CAT-EVT-[YYYYMM]-0001`. Field outlet_id=null |
| **Status** | ⬜ |

### TC-PO-003 — Validasi: Resto harus pilih outlet
| | |
|---|---|
| **Prasyarat** | Ada customer `resto` |
| **Langkah** | 1. Pilih customer Resto · 2. Kosongkan outlet · 3. Isi field lain · 4. Submit |
| **Expected** | Error validasi "customer_outlet_id wajib diisi" |
| **Status** | ⬜ |

### TC-PO-004 — Validasi: Catering harus isi nama event
| | |
|---|---|
| **Prasyarat** | Ada customer `catering` |
| **Langkah** | 1. Pilih customer Catering · 2. Kosongkan nama_event · 3. Submit |
| **Expected** | Error validasi "nama_event wajib diisi" |
| **Status** | ⬜ |

### TC-PO-005 — Validasi: tanggal_kirim tidak boleh sebelum tanggal PO
| | |
|---|---|
| **Prasyarat** | - |
| **Langkah** | 1. Buat PO · 2. Tanggal PO=`2026-07-23` · 3. Tanggal Kirim=`2026-07-22` (sebelumnya) · 4. Submit |
| **Expected** | Error validasi pada tanggal_kirim |
| **Status** | ⬜ |

### TC-PO-006 — Validasi: items minimal 1
| | |
|---|---|
| **Prasyarat** | - |
| **Langkah** | 1. Isi semua field PO · 2. Hapus semua item · 3. Submit |
| **Expected** | Error validasi "items minimal 1" |
| **Status** | ⬜ |

### TC-PO-007 — Edit PO berhasil (status baru)
| | |
|---|---|
| **Prasyarat** | Ada PO dengan status `baru` |
| **Langkah** | 1. Buka detail PO · 2. Klik Edit · 3. Ubah qty salah satu item · 4. Simpan |
| **Expected** | PO terupdate, redirect ke detail dengan flash success. Items lama terganti |
| **Status** | ⬜ |

### TC-PO-008 — Edit PO ditolak (status bukan baru)
| | |
|---|---|
| **Prasyarat** | Ada PO dengan status `proses` |
| **Langkah** | 1. Akses langsung `/purchase-orders/{id}/edit` |
| **Expected** | Redirect ke halaman detail PO dengan flash error "PO dengan status proses tidak dapat diedit" |
| **Status** | ⬜ |

### TC-PO-009 — Hapus PO berhasil (status baru)
| | |
|---|---|
| **Prasyarat** | Ada PO dengan status `baru` |
| **Langkah** | 1. Buka detail PO · 2. Klik Hapus · 3. Konfirmasi |
| **Expected** | PO dan semua PoItem-nya terhapus. Redirect ke index |
| **Status** | ⬜ |

### TC-PO-010 — Hapus PO ditolak (status bukan baru)
| | |
|---|---|
| **Prasyarat** | Ada PO dengan status `proses` |
| **Langkah** | 1. Kirim DELETE ke `/purchase-orders/{id}/destroy` |
| **Expected** | Flash error "Hanya PO dengan status 'baru' yang dapat dihapus" |
| **Status** | ⬜ |

### TC-PO-011 — Filter PO by status
| | |
|---|---|
| **Prasyarat** | Ada PO dengan berbagai status |
| **Langkah** | 1. Buka `/purchase-orders` · 2. Pilih filter status=`proses` · 3. Submit |
| **Expected** | Hanya PO berstatus `proses` yang tampil |
| **Status** | ⬜ |

### TC-PO-012 — Search PO by no_po
| | |
|---|---|
| **Prasyarat** | Ada PO dengan no_po yang diketahui |
| **Langkah** | 1. Buka `/purchase-orders` · 2. Isi search dengan sebagian no_po · 3. Submit |
| **Expected** | Hanya PO yang cocok yang tampil |
| **Status** | ⬜ |

### TC-PO-013 — Auto-generate no_po sequential
| | |
|---|---|
| **Prasyarat** | Sudah ada PO `RST-CABANUTM-202507-0001` untuk outlet yang sama di bulan yang sama |
| **Langkah** | 1. Buat PO baru dengan customer & outlet yang sama, bulan yang sama |
| **Expected** | No PO yang terbentuk adalah `RST-CABANUTM-202507-0002` |
| **Status** | ⬜ |


---

## MODUL 5 — TRANSISI STATUS PO

> Ini adalah modul paling kritis. Semua aturan berasal dari `PurchaseOrder::canTransitionTo()`.

### TC-STS-001 — Transisi baru → proses BERHASIL (ada SJ)
| | |
|---|---|
| **Prasyarat** | Ada PO status `baru`. Sudah ada SuratJalan dengan customer_id + outlet_id + tanggal yang sama |
| **Langkah** | 1. Buka detail PO · 2. Ubah status ke `proses` |
| **Expected** | Status berubah ke `proses`, flash success |
| **Status** | ⬜ |

### TC-STS-002 — Transisi baru → proses GAGAL (belum ada SJ)
| | |
|---|---|
| **Prasyarat** | Ada PO status `baru`, belum ada SJ |
| **Langkah** | 1. Buka detail PO · 2. Ubah status ke `proses` |
| **Expected** | Flash error "Pastikan Surat Jalan sudah dibuat terlebih dahulu" |
| **Status** | ⬜ |

### TC-STS-003 — Transisi proses → menunggu_pembayaran BERHASIL
| | |
|---|---|
| **Prasyarat** | Ada PO status `proses` |
| **Langkah** | 1. Buka detail PO · 2. Ubah status ke `menunggu_pembayaran` |
| **Expected** | Status berubah ke `menunggu_pembayaran` (transisi ini selalu diizinkan) |
| **Status** | ⬜ |

### TC-STS-004 — Transisi menunggu_pembayaran → selesai BERHASIL (ada invoice lunas)
| | |
|---|---|
| **Prasyarat** | Ada PO status `menunggu_pembayaran`. Ada Invoice dengan status=`lunas` untuk customer yang sama |
| **Langkah** | 1. Buka detail PO · 2. Ubah status ke `selesai` |
| **Expected** | Status berubah ke `selesai` |
| **Status** | ⬜ |

### TC-STS-005 — Transisi menunggu_pembayaran → selesai GAGAL (invoice belum lunas)
| | |
|---|---|
| **Prasyarat** | Ada PO status `menunggu_pembayaran`. Invoice customer masih `terbit` |
| **Langkah** | 1. Buka detail PO · 2. Ubah status ke `selesai` |
| **Expected** | Flash error "Pastikan Invoice sudah dicetak dan pembayaran telah dilakukan (lunas)" |
| **Status** | ⬜ |

### TC-STS-006 — Transisi tidak valid: baru → selesai langsung
| | |
|---|---|
| **Prasyarat** | Ada PO status `baru` |
| **Langkah** | 1. Kirim PATCH `/purchase-orders/{id}/status` dengan `status=selesai` |
| **Expected** | Flash error transisi tidak diizinkan |
| **Status** | ⬜ |

### TC-STS-007 — Transisi tidak valid: selesai → baru (mundur)
| | |
|---|---|
| **Prasyarat** | Ada PO status `selesai` |
| **Langkah** | 1. Kirim PATCH `/purchase-orders/{id}/status` dengan `status=baru` |
| **Expected** | Flash error transisi tidak diizinkan |
| **Status** | ⬜ |

### TC-STS-008 — Transisi ke status yang sama (idempotent)
| | |
|---|---|
| **Prasyarat** | Ada PO status `proses` |
| **Langkah** | 1. Kirim PATCH dengan `status=proses` (sama) |
| **Expected** | Flash error — transisi ke status yang sama tidak diizinkan |
| **Status** | ⬜ |

---

## MODUL 6 — LOGISTIK / SURAT JALAN

### TC-SJ-001 — Generate SJ dari PO baru berhasil
| | |
|---|---|
| **Prasyarat** | Ada PO status `baru`, login admin |
| **Langkah** | 1. Buka `/logistik/create` · 2. Pilih PO dari daftar · 3. Klik Generate |
| **Expected** | SJ tersimpan. PO berubah status ke `proses`. Redirect ke index SJ dengan flash success |
| **Status** | ⬜ |

### TC-SJ-002 — No SJ ter-generate dengan format benar
| | |
|---|---|
| **Prasyarat** | Ada PO customer bernama `Moc Resto`, tanggal 01-07-2026 |
| **Langkah** | 1. Generate SJ dari PO tersebut |
| **Expected** | No SJ = `SRTJ-MOC-01072026-00001` |
| **Status** | ⬜ |

### TC-SJ-003 — No SJ sequential per hari
| | |
|---|---|
| **Prasyarat** | Sudah ada SJ `SRTJ-MOC-01072026-00001` untuk hari yang sama |
| **Langkah** | 1. Generate SJ kedua untuk customer yang sama di hari yang sama |
| **Expected** | No SJ = `SRTJ-MOC-01072026-00002` |
| **Status** | ⬜ |

### TC-SJ-004 — Generate SJ gagal: PO bukan status baru
| | |
|---|---|
| **Prasyarat** | Ada PO status `proses` |
| **Langkah** | 1. Kirim POST `/logistik/generate` dengan `purchase_order_id` dari PO tersebut |
| **Expected** | Flash error "Surat Jalan hanya bisa dibuat dari PO berstatus 'baru'" |
| **Status** | ⬜ |

### TC-SJ-005 — Halaman create hanya tampilkan PO berstatus baru
| | |
|---|---|
| **Prasyarat** | Ada PO dengan berbagai status |
| **Langkah** | 1. Buka `/logistik/create` |
| **Expected** | Hanya PO berstatus `baru` yang tampil di daftar pilihan |
| **Status** | ⬜ |

### TC-SJ-006 — Detail SJ menampilkan item dari PO terkait
| | |
|---|---|
| **Prasyarat** | Ada SJ yang sudah di-generate dari PO berisi 3 item |
| **Langkah** | 1. Buka detail SJ |
| **Expected** | Tampil 3 item barang dari PO yang cocok (customer + outlet + tanggal) |
| **Status** | ⬜ |

### TC-SJ-007 — Cetak SJ membuka halaman print
| | |
|---|---|
| **Prasyarat** | Ada SJ |
| **Langkah** | 1. Buka detail SJ · 2. Klik Cetak |
| **Expected** | Halaman `/logistik/{id}/print` tampil dengan layout print-only, tanpa sidebar |
| **Status** | ⬜ |


---

## MODUL 7 — BELANJA / PROCUREMENT

### TC-BLJ-001 — Konsolidasi tampil dengan tanggal hari ini
| | |
|---|---|
| **Prasyarat** | Ada PO berstatus `baru` / `proses` dengan tanggal_kirim = hari ini |
| **Langkah** | 1. Buka `/belanja/konsolidasi` tanpa parameter |
| **Expected** | Tampil daftar barang yang dibutuhkan hari ini, total qty per barang, outlet breakdown |
| **Status** | ⬜ |

### TC-BLJ-002 — Auto-fallback ke tanggal terdekat
| | |
|---|---|
| **Prasyarat** | Tidak ada PO untuk hari ini. Ada PO dengan tanggal_kirim=`2026-07-25` |
| **Langkah** | 1. Buka `/belanja/konsolidasi` tanpa parameter |
| **Expected** | Otomatis menampilkan data tanggal `2026-07-25` dengan info banner auto-fallback |
| **Status** | ⬜ |

### TC-BLJ-003 — Filter konsolidasi berdasarkan tanggal tertentu
| | |
|---|---|
| **Prasyarat** | Ada PO tanggal_kirim=`2026-07-26` |
| **Langkah** | 1. Buka `/belanja/konsolidasi?tanggal=2026-07-26` |
| **Expected** | Tampil data untuk tanggal tersebut, tidak auto-fallback |
| **Status** | ⬜ |

### TC-BLJ-004 — Input harga beli berhasil + DaftarBelanja terbuat
| | |
|---|---|
| **Prasyarat** | Ada PO berstatus `baru` dengan tanggal_kirim=hari ini. Ada 2 barang di PO |
| **Langkah** | 1. Buka konsolidasi · 2. Isi harga beli untuk semua barang · 3. Klik Simpan Harga Beli |
| **Expected** | HargaBeli tersimpan. Record DaftarBelanja terbuat (no_db=`DB-YYYYMMDD-0001`). PO ter-sync ke pivot. Redirect ke konsolidasi dengan flash success |
| **Status** | ⬜ |

### TC-BLJ-005 — Input harga beli kedua kali (update) bukan duplikat
| | |
|---|---|
| **Prasyarat** | Sudah ada harga beli untuk suatu barang di tanggal yang sama |
| **Langkah** | 1. Simpan harga beli lagi dengan nilai berbeda untuk barang dan tanggal yang sama |
| **Expected** | Record HargaBeli di-update (bukan insert baru). DaftarBelanja total_modal terupdate |
| **Status** | ⬜ |

### TC-BLJ-006 — Input harga beli validasi: harga_beli tidak boleh negatif
| | |
|---|---|
| **Prasyarat** | - |
| **Langkah** | 1. Kirim POST `/belanja/harga` dengan `harga[0][harga_beli]=-100` |
| **Expected** | Error validasi "harga_beli min:0" |
| **Status** | ⬜ |

### TC-BLJ-007 — Input harga tanpa PO aktif: DaftarBelanja tidak terbuat
| | |
|---|---|
| **Prasyarat** | Tidak ada PO aktif (baru/proses) untuk tanggal yang diinput |
| **Langkah** | 1. Kirim POST `/belanja/harga` dengan tanggal yang tidak ada PO-nya |
| **Expected** | HargaBeli tersimpan tapi DaftarBelanja TIDAK terbuat (logika skip) |
| **Status** | ⬜ |

### TC-BLJ-008 — Daftar belanja menampilkan riwayat
| | |
|---|---|
| **Prasyarat** | Ada beberapa DaftarBelanja (login admin) |
| **Langkah** | 1. Buka `/belanja` |
| **Expected** | Daftar tampil diurutkan terbaru, kolom: no_db, tanggal, total_modal, total_revenue, margin% |
| **Status** | ⬜ |

### TC-BLJ-009 — Staff tidak bisa akses `/belanja` (index)
| | |
|---|---|
| **Prasyarat** | Login sebagai user role `staff` |
| **Langkah** | 1. Akses langsung `/belanja` |
| **Expected** | Redirect ke `/belanja/konsolidasi` dengan flash error akses ditolak |
| **Status** | ⬜ |

### TC-BLJ-010 — Staff bisa akses `/belanja/konsolidasi`
| | |
|---|---|
| **Prasyarat** | Login sebagai user role `staff` |
| **Langkah** | 1. Akses `/belanja/konsolidasi` |
| **Expected** | Halaman konsolidasi tampil normal |
| **Status** | ⬜ |

### TC-BLJ-011 — Margin dihitung benar
| | |
|---|---|
| **Prasyarat** | Ada DaftarBelanja dengan total_modal=1.000.000 dan total_revenue=1.500.000 |
| **Langkah** | 1. Buka detail DaftarBelanja tersebut |
| **Expected** | Margin tampil = `33.33%` ((1.5jt - 1jt) / 1.5jt × 100) |
| **Status** | ⬜ |

---

## MODUL 8 — INVOICE

### TC-INV-001 — Generate invoice dari 1 PO berhasil
| | |
|---|---|
| **Prasyarat** | Ada PO status `proses` milik customer A. Login admin |
| **Langkah** | 1. Buka `/invoices/create` · 2. Pilih customer A · 3. Isi tanggal · 4. Centang PO · 5. Klik Generate |
| **Expected** | Invoice terbuat status `terbit`. PO berubah ke `menunggu_pembayaran`. Total tagihan = SUM(qty × harga_jual). Redirect ke index |
| **Status** | ⬜ |

### TC-INV-002 — Generate invoice dari multiple PO 1 customer
| | |
|---|---|
| **Prasyarat** | Ada 3 PO status `proses` milik customer yang sama |
| **Langkah** | 1. Pilih customer · 2. Centang 3 PO · 3. Generate |
| **Expected** | 1 invoice dengan total_tagihan = gabungan semua PO. Ketiga PO → `menunggu_pembayaran` |
| **Status** | ⬜ |

### TC-INV-003 — Generate invoice gagal: tidak ada PO valid
| | |
|---|---|
| **Prasyarat** | - |
| **Langkah** | 1. Kirim POST `/invoices/generate` dengan purchase_order_ids dari PO yang sudah berstatus `menunggu_pembayaran` |
| **Expected** | Flash error "Tidak ada PO yang valid untuk di-invoice" |
| **Status** | ⬜ |

### TC-INV-004 — Generate invoice gagal: validasi field kosong
| | |
|---|---|
| **Prasyarat** | - |
| **Langkah** | 1. Kirim POST `/invoices/generate` tanpa customer_id atau tanggal |
| **Expected** | Error validasi |
| **Status** | ⬜ |

### TC-INV-005 — No invoice ter-generate dengan format benar
| | |
|---|---|
| **Prasyarat** | Belum ada invoice sama sekali |
| **Langkah** | 1. Generate invoice pertama |
| **Expected** | No invoice = `INV-000001` |
| **Status** | ⬜ |

### TC-INV-006 — Tandai lunas berhasil + cascade PO selesai
| | |
|---|---|
| **Prasyarat** | Ada Invoice status `terbit`. PO terkait status `menunggu_pembayaran` |
| **Langkah** | 1. Buka detail invoice · 2. Klik "Tandai Lunas" |
| **Expected** | Invoice → `lunas`. Semua PO customer yang `menunggu_pembayaran` → `selesai`. Flash success |
| **Status** | ⬜ |

### TC-INV-007 — Tandai lunas pada invoice yang sudah lunas
| | |
|---|---|
| **Prasyarat** | Ada Invoice status `lunas` |
| **Langkah** | 1. Kirim PATCH `/invoices/{id}/lunas` |
| **Expected** | Flash info "Invoice sudah berstatus lunas" (tidak error, idempotent) |
| **Status** | ⬜ |

### TC-INV-008 — Filter invoice by status
| | |
|---|---|
| **Prasyarat** | Ada invoice dengan berbagai status |
| **Langkah** | 1. Buka `/invoices?status=terbit` |
| **Expected** | Hanya invoice berstatus `terbit` yang tampil |
| **Status** | ⬜ |

### TC-INV-009 — KPI index menampilkan total tagihan dan total lunas
| | |
|---|---|
| **Prasyarat** | Ada invoice terbit Rp 2jt dan invoice lunas Rp 5jt |
| **Langkah** | 1. Buka `/invoices` |
| **Expected** | KPI "Belum Lunas" = Rp 2.000.000 · "Total Lunas" = Rp 5.000.000 |
| **Status** | ⬜ |

### TC-INV-010 — Halaman cetak invoice tampil tanpa navigasi
| | |
|---|---|
| **Prasyarat** | Ada invoice |
| **Langkah** | 1. Akses `/invoices/{id}/print` |
| **Expected** | Tampil halaman A4 dengan header invoice, tabel item, kolom tanda tangan — tanpa sidebar/navbar |
| **Status** | ⬜ |

### TC-INV-011 — Total tagihan dihitung benar
| | |
|---|---|
| **Prasyarat** | PO berisi: Wortel 10kg@Rp8.000 + Bayam 5ikat@Rp3.500 |
| **Langkah** | 1. Generate invoice dari PO tersebut |
| **Expected** | total_tagihan = (10×8.000) + (5×3.500) = Rp 97.500 |
| **Status** | ⬜ |


---

## MODUL 9 — FINANCE REPORTS

### TC-FIN-001 — Finance dashboard tampil dengan KPI default 30 hari
| | |
|---|---|
| **Prasyarat** | Ada PO selesai dalam 30 hari terakhir, login admin |
| **Langkah** | 1. Buka `/finance/dashboard` |
| **Expected** | KPI tampil: grossRevenue, COGS, grossProfit, marginPct. 2 chart tampil. Halaman tidak error |
| **Status** | ⬜ |

### TC-FIN-002 — Finance dashboard KPI = 0 saat tidak ada data
| | |
|---|---|
| **Prasyarat** | Tidak ada PO status `selesai` |
| **Langkah** | 1. Buka `/finance/dashboard` |
| **Expected** | Semua KPI = 0, margin = 0%, tidak ada error division by zero |
| **Status** | ⬜ |

### TC-FIN-003 — Filter periode finance dashboard
| | |
|---|---|
| **Prasyarat** | Ada PO selesai dalam berbagai tanggal |
| **Langkah** | 1. Buka `/finance/dashboard?days=7` |
| **Expected** | KPI hanya menghitung data 7 hari terakhir |
| **Status** | ⬜ |

### TC-FIN-004 — Alert harga muncul jika perubahan > 10% dalam 7 hari
| | |
|---|---|
| **Prasyarat** | Barang X: harga beli 7 hari lalu=Rp5.000, hari ini=Rp5.800 (naik 16%) |
| **Langkah** | 1. Buka `/finance/dashboard` |
| **Expected** | Muncul alert `warning` untuk barang X dengan perubahan 16% |
| **Status** | ⬜ |

### TC-FIN-005 — Alert tipe `danger` jika perubahan > 20%
| | |
|---|---|
| **Prasyarat** | Barang Y: harga beli naik dari Rp5.000 ke Rp6.200 (24%) dalam 7 hari |
| **Langkah** | 1. Buka `/finance/dashboard` |
| **Expected** | Alert tipe `danger` untuk barang Y |
| **Status** | ⬜ |

### TC-FIN-006 — Alert margin muncul jika margin < 25%
| | |
|---|---|
| **Prasyarat** | Barang Z: harga_jual=Rp8.000, harga_beli terbaru=Rp7.000 (margin = 12.5%) |
| **Langkah** | 1. Buka `/finance/dashboard` |
| **Expected** | Muncul alert `margin` untuk barang Z dengan margin 12.5% |
| **Status** | ⬜ |

### TC-FIN-007 — Laporan P&L menampilkan data bulan yang benar
| | |
|---|---|
| **Prasyarat** | Ada PO selesai di bulan Juli 2026 |
| **Langkah** | 1. Buka `/finance/pl?month=2026-07` |
| **Expected** | Revenue, COGS, Gross Profit tampil untuk data Juli 2026 saja |
| **Status** | ⬜ |

### TC-FIN-008 — P&L COGS hanya dihitung jika ada harga_beli matched tanggal_kirim
| | |
|---|---|
| **Prasyarat** | Ada PO selesai dengan tanggal_kirim=2026-07-23. Ada harga_beli untuk barang tsb di tanggal 2026-07-23 |
| **Langkah** | 1. Buka `/finance/pl?month=2026-07` |
| **Expected** | COGS terhitung. Jika tidak ada harga_beli untuk tanggal tsb, COGS = 0 untuk PO itu |
| **Status** | ⬜ |

### TC-FIN-009 — Analisis margin menampilkan N/A jika belum ada harga beli
| | |
|---|---|
| **Prasyarat** | Ada barang tapi belum ada HargaBeli untuk tanggal yang dipilih |
| **Langkah** | 1. Buka `/finance/margin?tanggal=2026-01-01` |
| **Expected** | Kolom harga_beli, margin_rp, margin_pct = NULL/N/A untuk semua barang |
| **Status** | ⬜ |

### TC-FIN-010 — Analisis margin perhitungan benar
| | |
|---|---|
| **Prasyarat** | Barang: harga_jual=Rp10.000, harga_beli=Rp7.000 pada tanggal yang dipilih |
| **Langkah** | 1. Buka `/finance/margin?tanggal=[tanggal tsb]` |
| **Expected** | margin_rp=3.000, margin_pct=30.00% |
| **Status** | ⬜ |

### TC-FIN-011 — Price trend tampil chart untuk barang yang dipilih
| | |
|---|---|
| **Prasyarat** | Ada 5 record HargaBeli untuk barang X dalam 30 hari terakhir |
| **Langkah** | 1. Buka `/finance/price-trend?barang_id={id}&days=30` |
| **Expected** | Halaman tampil dengan tabel 5 record dan chart placeholder |
| **Status** | ⬜ |

### TC-FIN-012 — Price trend kosong jika tidak ada data
| | |
|---|---|
| **Prasyarat** | - |
| **Langkah** | 1. Buka `/finance/price-trend` tanpa parameter barang_id |
| **Expected** | Halaman tampil tanpa chart/tabel data, tidak error |
| **Status** | ⬜ |


---

## MODUL 10 — ALUR END-TO-END (E2E)

> Test ini mensimulasikan satu siklus bisnis lengkap dari awal sampai selesai.

### TC-E2E-001 — Full order-to-cash: Resto

**Tujuan:** Verifikasi seluruh alur dari buat PO sampai PO selesai untuk customer tipe Resto.

| Step | Aksi | Expected |
|------|------|----------|
| 1 | Tambah customer `Resto Maju` (tipe=resto) + outlet `Cabang Pusat` | Customer & outlet tersimpan |
| 2 | Tambah barang `Wortel` (kg, Rp8.000) dan `Bayam` (ikat, Rp3.500) | Barang tersimpan |
| 3 | Buat PO untuk Resto Maju / Cabang Pusat. Item: Wortel 10kg, Bayam 5ikat. Tgl kirim=besok | PO status=`baru`, no_po ter-generate |
| 4 | Generate Surat Jalan dari PO tersebut | SJ tersimpan. PO → `proses` |
| 5 | Buka konsolidasi belanja untuk tanggal kirim PO | Tampil Wortel 10kg + Bayam 5ikat |
| 6 | Input harga beli: Wortel=Rp7.000, Bayam=Rp3.000. Simpan | HargaBeli tersimpan. DaftarBelanja terbuat |
| 7 | Generate invoice untuk Resto Maju dari PO tersebut | Invoice terbit. total_tagihan = Rp97.500. PO → `menunggu_pembayaran` |
| 8 | Tandai invoice sebagai lunas | Invoice → `lunas`. PO → `selesai` |
| 9 | Cek Finance Dashboard | grossRevenue >= 97.500, COGS >= 82.500, grossProfit >= 15.000 |

**Status Keseluruhan:** ⬜

---

### TC-E2E-002 — Full order-to-cash: Catering

| Step | Aksi | Expected |
|------|------|----------|
| 1 | Tambah customer `Catering Sejahtera` (tipe=catering) | Tersimpan tanpa outlet |
| 2 | Buat PO. Nama event=`Seminar Nasional`. Item: 3 barang | PO status=`baru`, no_po `CAT-EVT-...` |
| 3 | Generate SJ dari PO | SJ no_sj ter-generate. PO → `proses` |
| 4 | Input harga beli di konsolidasi | DaftarBelanja terbuat |
| 5 | Generate & lunas invoice | PO → `selesai` |

**Status Keseluruhan:** ⬜

---

### TC-E2E-003 — Satu invoice untuk multiple PO satu customer

| Step | Aksi | Expected |
|------|------|----------|
| 1 | Buat 3 PO untuk customer yang sama pada hari berbeda | 3 PO status=`baru` |
| 2 | Generate SJ untuk masing-masing PO | Ketiga PO → `proses` |
| 3 | Generate 1 invoice untuk ketiga PO sekaligus | Invoice total = sum ketiga PO. Ketiga PO → `menunggu_pembayaran` |
| 4 | Tandai lunas | Invoice → `lunas`. Ketiga PO → `selesai` |

**Status Keseluruhan:** ⬜

---

## MATRIKS RINGKASAN TEST CASE

| Modul | Jumlah TC | Critical |
|-------|-----------|---------|
| Autentikasi | 10 | TC-AUTH-005, 006 |
| Master Data Customer | 9 | TC-CUST-006 |
| Master Data Barang | 6 | TC-BRG-005 |
| Purchase Order | 13 | TC-PO-003, 004, 008, 010 |
| Transisi Status PO | 8 | TC-STS-002, 005, 006 |
| Logistik / Surat Jalan | 7 | TC-SJ-004 |
| Belanja | 11 | TC-BLJ-004, 007, 009 |
| Invoice | 11 | TC-INV-003, 006, 011 |
| Finance Reports | 12 | TC-FIN-002, 006, 008 |
| End-to-End | 3 | TC-E2E-001 |
| **TOTAL** | **90** | |

---

## DATA SETUP YANG DIBUTUHKAN SEBELUM TESTING

Jalankan di seeder atau buat manual sebelum mulai:

```
Users:
  - admin@tuksay.id  / password  (role: admin)
  - staff@tuksay.id  / password  (role: staff)

Customers:
  - Resto Maju (tipe: resto) + outlet: Cabang Pusat, Cabang Selatan
  - Catering Sejahtera (tipe: catering)

Barangs:
  - Wortel (kg, Rp 8.000)
  - Bayam (ikat, Rp 3.500)
  - Brokoli (kg, Rp 20.000)
  - Tomat (kg, Rp 12.000)
```

---

*Test case ini dibuat berdasarkan analisis source code per Juli 2026.*
*Perubahan kode (terutama business rules) harus diikuti update dokumen ini.*
