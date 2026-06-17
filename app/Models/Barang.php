<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $fillable = [
        'nama',
        'satuan',
        'harga_jual',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
    ];

    public function poItems(): HasMany
    {
        return $this->hasMany(PoItem::class);
    }

    public function hargaBelis(): HasMany
    {
        return $this->hasMany(HargaBeli::class);
    }
}
