<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class DaftarBelanja extends Model
{
    protected $fillable = [
        'no_db',
        'tanggal',
        'total_modal',
        'total_revenue',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'total_modal'   => 'decimal:2',
        'total_revenue' => 'decimal:2',
    ];

    /**
     * Auto-generate kode unik no_db saat creating.
     * Format: DB-{YYYYMMDD}-{NNNN}
     * Contoh: DB-20260708-0001
     */
    protected static function booted(): void
    {
        static::creating(function (DaftarBelanja $db) {
            if (empty($db->no_db)) {
                $db->no_db = static::generateKodeDB($db->tanggal);
            }
        });
    }

    public static function generateKodeDB(string $tanggal): string
    {
        $tgl    = \Carbon\Carbon::parse($tanggal)->format('Ymd');
        $prefix = "DB-{$tgl}";

        $lastSeq = DB::table('daftar_belanjas')
            ->where('no_db', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(no_db, '-', -1) AS UNSIGNED)) as last_seq")
            ->value('last_seq');

        $nextSeq = (int)($lastSeq ?? 0) + 1;

        return $prefix . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    // ─── Relasi ─────────────────────────────────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(DaftarBelanjaItem::class);
    }

    public function purchaseOrders(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseOrder::class, 'daftar_belanja_po');
    }

    /**
     * Hitung estimasi margin kotor dalam persen.
     */
    public function marginPersen(): float
    {
        if ((float)$this->total_revenue <= 0) {
            return 0;
        }

        return (((float)$this->total_revenue - (float)$this->total_modal) / (float)$this->total_revenue) * 100;
    }
}
