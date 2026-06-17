<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'nama',
        'nama_perusahaan',
        'alamat',
        'payment_method',
    ];

    protected $casts = [
        'payment_method' => 'string',
    ];

    public function outlets(): HasMany
    {
        return $this->hasMany(CustomerOutlet::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function suratJalans(): HasMany
    {
        return $this->hasMany(SuratJalan::class);
    }
}
