<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HargaBeli extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'barang_id',
        'tanggal',
        'harga_beli',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'harga_beli' => 'decimal:2',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
