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
                $maxId = (int) \DB::table('surat_jalans')->max('id');
                $sj->no_sj = 'SJ-' . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);
            }
        });
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
