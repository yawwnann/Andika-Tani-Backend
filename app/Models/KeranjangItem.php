<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperKeranjangItem
 */
class KeranjangItem extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'keranjang_items';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'pupuk_id', // DIUBAH dari ikan_id
        'quantity',
    ];

    /**
     * Relasi: Satu item keranjang dimiliki oleh satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Satu item keranjang merujuk ke satu produk Pupuk.
     */
    public function pupuk(): BelongsTo // DIUBAH dari ikan()
    {
        // DIUBAH dari Ikan::class ke Pupuk::class
        return $this->belongsTo(Pupuk::class, 'pupuk_id');
    }
}