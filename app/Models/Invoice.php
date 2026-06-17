<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'no_invoice',
        'customer_id',
        'tanggal',
        'total_tagihan',
        'status',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'total_tagihan' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->no_invoice)) {
                $maxId = (int) \DB::table('invoices')->max('id');
                $invoice->no_invoice = 'INV-' . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
