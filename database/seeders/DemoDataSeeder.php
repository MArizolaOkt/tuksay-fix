<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\BiayaOperasional;
use App\Models\Customer;
use App\Models\CustomerOutlet;
use App\Models\HargaBeli;
use App\Models\PoItem;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Admin User ───────────────────────────────────────────────
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@tuksay.test'],
            [
                'name'              => 'Admin TUKSAY',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // ─── Produk (Barangs) ─────────────────────────────────────────
        $barangs = [
            ['nama' => 'Bayam Hijau',     'satuan' => 'ikat',  'harga_jual' => 3500],
            ['nama' => 'Kangkung',        'satuan' => 'ikat',  'harga_jual' => 3000],
            ['nama' => 'Wortel Impor',    'satuan' => 'kg',    'harga_jual' => 18000],
            ['nama' => 'Kentang Medan',   'satuan' => 'kg',    'harga_jual' => 15000],
            ['nama' => 'Brokoli',         'satuan' => 'kg',    'harga_jual' => 28000],
            ['nama' => 'Tomat Merah',     'satuan' => 'kg',    'harga_jual' => 12000],
            ['nama' => 'Cabai Merah',     'satuan' => 'kg',    'harga_jual' => 45000],
            ['nama' => 'Bawang Merah',    'satuan' => 'kg',    'harga_jual' => 35000],
            ['nama' => 'Bawang Putih',    'satuan' => 'kg',    'harga_jual' => 38000],
            ['nama' => 'Selada Keriting', 'satuan' => 'ikat',  'harga_jual' => 5000],
            ['nama' => 'Paprika Merah',   'satuan' => 'buah',  'harga_jual' => 8000],
            ['nama' => 'Pakcoy',          'satuan' => 'ikat',  'harga_jual' => 4000],
        ];

        $createdBarangs = [];
        foreach ($barangs as $data) {
            $createdBarangs[] = Barang::firstOrCreate(['nama' => $data['nama']], $data);
        }

        // ─── Harga Beli (7 hari ke belakang) ─────────────────────────
        $hargaBeliRatio = [0.65, 0.68, 0.70, 0.72, 0.75]; // 65-75% dari harga jual
        for ($d = 7; $d >= 0; $d--) {
            $tanggal = Carbon::today()->subDays($d)->toDateString();
            foreach ($createdBarangs as $barang) {
                $ratio = $hargaBeliRatio[array_rand($hargaBeliRatio)];
                HargaBeli::updateOrCreate(
                    ['barang_id' => $barang->id, 'tanggal' => $tanggal],
                    ['harga_beli' => round($barang->harga_jual * $ratio, -2)]
                );
            }
        }

        // ─── Customers ────────────────────────────────────────────────
        $customersData = [
            [
                'nama'            => 'Budi Santoso',
                'nama_perusahaan' => 'PT Restoran Sederhana',
                'alamat'          => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'payment_method'  => 'TOP14',
                'outlets'         => ['Cabang Sudirman', 'Cabang Thamrin', 'Cabang Gatot Subroto'],
            ],
            [
                'nama'            => 'Siti Rahayu',
                'nama_perusahaan' => 'CV Catering Nusantara',
                'alamat'          => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
                'payment_method'  => 'TOP7',
                'outlets'         => ['Dapur Pusat', 'Dapur Kebon Jeruk'],
            ],
            [
                'nama'            => 'Ahmad Fauzi',
                'nama_perusahaan' => 'Hotel Bintang Tiga',
                'alamat'          => 'Jl. MH Thamrin No. 8, Jakarta',
                'payment_method'  => 'TOP30',
                'outlets'         => ['Dapur Hotel Utama', 'Resto Rooftop'],
            ],
            [
                'nama'            => 'Maya Dewi',
                'nama_perusahaan' => 'Warung Makan Bu Maya',
                'alamat'          => 'Jl. Cipete Raya No. 23, Jakarta Selatan',
                'payment_method'  => 'CASH',
                'outlets'         => ['Warung Cipete'],
            ],
            [
                'nama'            => 'Hendra Wijaya',
                'nama_perusahaan' => 'PT Supermarket Segar',
                'alamat'          => 'Jl. Pondok Indah No. 15, Jakarta Selatan',
                'payment_method'  => 'TOP14',
                'outlets'         => ['Toko Pondok Indah', 'Toko Fatmawati', 'Toko Cilandak'],
            ],
        ];

        $customers = [];
        foreach ($customersData as $data) {
            $outlets = $data['outlets'];
            unset($data['outlets']);
            $customer = Customer::firstOrCreate(['nama_perusahaan' => $data['nama_perusahaan']], $data);
            foreach ($outlets as $namaOutlet) {
                CustomerOutlet::firstOrCreate(
                    ['customer_id' => $customer->id, 'nama_outlet' => $namaOutlet]
                );
            }
            $customers[] = $customer->load('outlets');
        }

        // ─── Purchase Orders (beberapa hari ke belakang) ──────────────
        $statuses = ['baru', 'proses', 'selesai', 'selesai', 'selesai']; // lebih banyak selesai

        for ($d = 14; $d >= 1; $d--) {
            $tanggal  = Carbon::today()->subDays($d)->toDateString();
            $numPos   = rand(2, 4);

            for ($p = 0; $p < $numPos; $p++) {
                $customer = $customers[array_rand($customers)];
                $outlet   = $customer->outlets->random();
                $status   = $d <= 2 ? 'baru' : ($d <= 5 ? $statuses[array_rand($statuses)] : 'selesai');

                // Check if PO already exists for this outlet+tanggal
                $exists = PurchaseOrder::where('customer_outlet_id', $outlet->id)
                    ->whereDate('tanggal', $tanggal)
                    ->exists();
                if ($exists) continue;

                $po = PurchaseOrder::create([
                    'customer_id'        => $customer->id,
                    'customer_outlet_id' => $outlet->id,
                    'tanggal'            => $tanggal,
                    'no_ref'             => 'REF-' . strtoupper(substr(md5(rand()), 0, 6)),
                    'status'             => $status,
                ]);

                // 3-6 items per PO
                $selectedBarangs = collect($createdBarangs)->shuffle()->take(rand(3, 6));
                foreach ($selectedBarangs as $barang) {
                    PoItem::create([
                        'purchase_order_id' => $po->id,
                        'barang_id'         => $barang->id,
                        'qty'               => rand(5, 50) / 2, // 2.5, 5.0, ... 25.0
                    ]);
                }
            }
        }

        // ─── Biaya Operasional (1 bulan) ──────────────────────────────
        $biayaList = [
            ['nama_biaya' => 'Bensin Motor Delivery',    'kategori' => 'Transport'],
            ['nama_biaya' => 'Plastik Kemasan',          'kategori' => 'Packaging'],
            ['nama_biaya' => 'Karung Goni',              'kategori' => 'Packaging'],
            ['nama_biaya' => 'Pulsa HP Operasional',     'kategori' => 'Komunikasi'],
            ['nama_biaya' => 'Es Batu Pendingin',        'kategori' => 'Lain-lain'],
            ['nama_biaya' => 'Biaya Parkir Pasar',       'kategori' => 'Transport'],
            ['nama_biaya' => 'Biaya Tak Terduga Lainnya','kategori' => 'Tak Terduga'],
        ];

        $rentang = collect(range(0, 29))->shuffle()->take(20);
        foreach ($rentang as $d) {
            $biaya = $biayaList[array_rand($biayaList)];
            BiayaOperasional::create([
                'nama_biaya' => $biaya['nama_biaya'],
                'kategori'   => $biaya['kategori'],
                'jumlah'     => rand(1, 15) * 5000,
                'tanggal'    => Carbon::today()->subDays($d)->toDateString(),
            ]);
        }

        $this->command->info('✅ Demo data berhasil di-seed!');
        $this->command->info('   - 1 user admin (admin@tuksay.test / password)');
        $this->command->info('   - ' . count($createdBarangs) . ' produk barang');
        $this->command->info('   - ' . count($customers) . ' customers dengan outlets');
        $this->command->info('   - ' . PurchaseOrder::count() . ' purchase orders');
        $this->command->info('   - ' . BiayaOperasional::count() . ' catatan biaya operasional');
    }
}
