<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DaftarBelanjaItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'daftar_belanja_id',
        'barang_id',
        'total_qty',
        'harga_beli',
        'harga_jual',
        'outlet_breakdown',
    ];

    protected $casts = [
        'total_qty'  => 'decimal:3',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    // ─── Relasi ─────────────────────────────────────────────────────────────────

    public function daftarBelanja(): BelongsTo
    {
        return $this->belongsTo(DaftarBelanja::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Total modal untuk item ini (harga_beli × total_qty).
     */
    public function totalModal(): ?float
    {
        if (is_null($this->harga_beli)) {
            return null;
        }

        return (float)$this->harga_beli * (float)$this->total_qty;
    }

    /**
     * Total revenue untuk item ini (harga_jual × total_qty).
     */
    public function totalRevenue(): float
    {
        return (float)$this->harga_jual * (float)$this->total_qty;
    }
}
