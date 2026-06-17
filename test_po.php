<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $po = App\Models\PurchaseOrder::create([
        'customer_id'        => 1,
        'customer_outlet_id' => 1,
        'tanggal'            => '2026-06-11',
        'status'             => 'baru',
    ]);
    echo "SUCCESS: no_po = " . $po->no_po . PHP_EOL;
    echo "id = " . $po->id . PHP_EOL;
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
