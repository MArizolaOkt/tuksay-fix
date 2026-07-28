<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'purchase_order_id',
        'barang_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
    ];

    public function getHargaJualAttribute()
    {
        $tanggal = $this->purchaseOrder?->tanggal_kirim ?? $this->purchaseOrder?->tanggal;
        if ($tanggal) {
            if ($tanggal instanceof \Carbon\Carbon) {
                $tanggal = $tanggal->toDateString();
            } else {
                $tanggal = substr($tanggal, 0, 10);
            }
        } else {
            $tanggal = today()->toDateString();
        }

        $hargaBeliRecord = HargaBeli::where('barang_id', $this->barang_id)
            ->whereDate('tanggal', '<=', $tanggal)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        if (!$hargaBeliRecord) {
            $hargaBeliRecord = HargaBeli::where('barang_id', $this->barang_id)
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->first();
        }

        $hargaBeli = $hargaBeliRecord ? $hargaBeliRecord->harga_beli : 0;
        return $hargaBeli > 0 ? (float) ($hargaBeli / 0.7) : 0.0;
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
