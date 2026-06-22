<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'no_po',
        'no_ref',
        'customer_id',
        'customer_outlet_id',
        'nama_event',
        'tanggal',
        'tanggal_kirim',  // Perubahan 1 — SKILL.md
        'status',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'tanggal_kirim' => 'date',  // Perubahan 1 — SKILL.md
    ];

    /**
     * Perubahan 2 — SKILL.md: Generate kode PO unik per tipe customer & outlet.
     * Format: [TIPE]-[OUTLET]-[YYYYMM]-[NNNN]
     * Contoh: RST-JKT01-202506-0001  |  CTR-EVT-202506-0001
     */
    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $po) {
            if (empty($po->no_po)) {
                $po->no_po = static::generateKodePO($po);
            }
        });
    }

    public static function generateKodePO(PurchaseOrder $po): string
    {
        // Ambil tipe customer
        $customer = Customer::find($po->customer_id);
        $tipe     = $customer ? strtoupper(substr($customer->tipe, 0, 3)) : 'PO';

        // Ambil kode outlet (pakai ID outlet diformat, atau EVT untuk catering)
        if ($po->customer_outlet_id) {
            $outlet = CustomerOutlet::find($po->customer_outlet_id);
            // Buat kode outlet dari nama: ambil 5 karakter pertama tanpa spasi, uppercase
            $outletKode = $outlet
                ? strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($outlet->nama_outlet, 0, 5)))
                : 'OTL' . $po->customer_outlet_id;
        } else {
            // Catering: gunakan EVT (event-based)
            $outletKode = 'EVT';
        }

        $tanggal = $po->tanggal ?? now()->toDateString();
        $period  = \Carbon\Carbon::parse($tanggal)->format('Ym');
        $prefix  = "{$tipe}-{$outletKode}-{$period}";

        // Cari sequence terakhir untuk prefix ini (race-condition safe dengan lock)
        $lastSeq = DB::table('purchase_orders')
            ->where('no_po', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(no_po, '-', -1) AS UNSIGNED)) as last_seq")
            ->value('last_seq');

        $nextSeq = (int)($lastSeq ?? 0) + 1;

        return $prefix . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(CustomerOutlet::class, 'customer_outlet_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PoItem::class);
    }
}
