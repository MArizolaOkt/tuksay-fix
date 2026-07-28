<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Check & seed users
$users = DB::table('users')->get();
echo "=== USERS ===\n";
foreach ($users as $u) {
    echo "  id={$u->id} email={$u->email} role={$u->role}\n";
}

// Ensure admin user exists
if (!DB::table('users')->where('email','admin@tuksay.test')->exists()) {
    DB::table('users')->insert([
        'name'              => 'Admin Test',
        'email'             => 'admin@tuksay.test',
        'password'          => Hash::make('password123'),
        'role'              => 'admin',
        'email_verified_at' => now(),
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);
    echo "  [CREATED] admin@tuksay.test\n";
} else {
    // update password to known value
    DB::table('users')->where('email','admin@tuksay.test')
        ->update(['password' => Hash::make('password123'), 'email_verified_at' => now()]);
    echo "  [UPDATED] admin@tuksay.test password reset\n";
}

// Ensure staff user exists
if (!DB::table('users')->where('email','staff@tuksay.test')->exists()) {
    DB::table('users')->insert([
        'name'              => 'Staff Test',
        'email'             => 'staff@tuksay.test',
        'password'          => Hash::make('password123'),
        'role'              => 'staff',
        'email_verified_at' => now(),
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);
    echo "  [CREATED] staff@tuksay.test\n";
} else {
    DB::table('users')->where('email','staff@tuksay.test')
        ->update(['password' => Hash::make('password123'), 'email_verified_at' => now()]);
    echo "  [UPDATED] staff@tuksay.test password reset\n";
}

echo "\n=== DATA COUNTS ===\n";
foreach (['users','customers','customer_outlets','barangs','purchase_orders','surat_jalans','invoices','harga_belis','daftar_belanjas'] as $t) {
    echo "  $t = " . DB::table($t)->count() . "\n";
}

echo "\n=== CUSTOMERS ===\n";
foreach (DB::table('customers')->get() as $c) {
    echo "  id={$c->id} nama={$c->nama} tipe={$c->tipe}\n";
}

echo "\n=== CUSTOMER_OUTLETS ===\n";
foreach (DB::table('customer_outlets')->get() as $o) {
    echo "  id={$o->id} customer_id={$o->customer_id} nama={$o->nama_outlet}\n";
}

echo "\n=== BARANGS (first 5) ===\n";
foreach (DB::table('barangs')->limit(5)->get() as $b) {
    echo "  id={$b->id} nama={$b->nama} satuan={$b->satuan} harga_jual={$b->harga_jual}\n";
}

echo "\n=== PO STATUS DISTRIBUTION ===\n";
foreach (DB::table('purchase_orders')->selectRaw('status, COUNT(*) as cnt')->groupBy('status')->get() as $r) {
    echo "  {$r->status} = {$r->cnt}\n";
}
