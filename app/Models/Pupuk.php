<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Pupuk
 *
 * @property int $id
 * @property int $kategori_pupuk_id
 * @property string $nama_pupuk
 * @property string $slug
 * @property string|null $deskripsi
 * @property float $harga
 * @property int $stok
 * @property string $status_ketersediaan
 * @property string|null $gambar_utama
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\KategoriPupuk $kategoriPupuk
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pesanan[] $pesanan
 * @property-read int|null $pesanan_count
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Pupuk extends Model
{
    use HasFactory;

    protected $table = 'pupuk';

    protected $fillable = [
        'kategori_pupuk_id',
        'nama_pupuk',
        'slug',
        'deskripsi',
        'harga',
        'stok',
        'status_ketersediaan',
        'gambar_utama',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    public function kategoriPupuk()
    {
        return $this->belongsTo(KategoriPupuk::class, 'kategori_pupuk_id');
    }

    public function pesanan()
    {
        return $this->belongsToMany(Pesanan::class, 'item_pesanan')
            ->withPivot('jumlah', 'harga_saat_pesanan')
            ->withTimestamps();
    }
}