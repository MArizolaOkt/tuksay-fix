<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratJalan extends Model
{
    protected $fillable = [
        'no_sj',
        'customer_id',
        'customer_outlet_id',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (SuratJalan $sj) {
            if (empty($sj->no_sj)) {
                $sj->no_sj = static::generateNoSJ($sj->customer_id, $sj->tanggal ?? now()->toDateString());
            }
        });
    }

    /**
     * Generate nomor surat jalan otomatis.
     * Format: SRTJ-{INISIAL_CUSTOMER}-{DDMMYYYY}-{00001}
     * Contoh: SRTJ-MOC-01072026-00001
     *
     * - Inisial: 3 huruf pertama nama customer (uppercase, tanpa spasi/simbol)
     * - Tanggal: ddMMyyyy
     * - Running number: per hari, 5 digit (reset tiap hari)
     */
    public static function generateNoSJ(int $customerId, string $tanggal): string
    {
        $customer = \App\Models\Customer::find($customerId);
        $namaCustomer = $customer ? $customer->nama : 'UNK';

        // Inisial: 3 huruf pertama, uppercase, hanya huruf/angka
        $inisial = strtoupper(
            substr(preg_replace('/[^A-Za-z0-9]/', '', $namaCustomer), 0, 3)
        );
        if (empty($inisial)) {
            $inisial = 'UNK';
        }

        // Format tanggal: ddMMyyyy
        $tgl = \Carbon\Carbon::parse($tanggal)->format('dmY');

        // Prefix untuk pengecekan running number hari ini
        $prefix = "SRTJ-{$inisial}-{$tgl}";

        // Running number: hitung SJ dengan prefix yang sama hari ini
        $count = \DB::table('surat_jalans')
            ->where('no_sj', 'like', "{$prefix}-%")
            ->count();

        $nextSeq = $count + 1;

        return $prefix . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(CustomerOutlet::class, 'customer_outlet_id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'customer_outlet_id', 'customer_outlet_id');
    }
}
