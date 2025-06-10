<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // <--- Import BelongsToMany
use Illuminate\Database\Eloquent\Relations\HasMany; // <--- Import HasMany
use Illuminate\Support\Str;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;

// HAPUS INI jika Anda tidak punya model ItemPesanan terpisah:
// use App\Models\ItemPesanan; // <--- Hapus jika tidak ada model ItemPesanan.php

/**
 * @mixin IdeHelperPupuk
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\KeranjangItem[] $keranjangItems
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
        'stok' => 'integer',
    ];

    // Relasi ke KategoriPupuk
    public function kategoriPupuk(): BelongsTo
    {
        return $this->belongsTo(KategoriPupuk::class, 'kategori_pupuk_id');
    }

    // --- REVISI RELASI UNTUK PESANAN (MELALUI TABEL PIVOT) ---
    // Pupuk bisa ada di banyak Pesanan (Many-to-Many) melalui tabel pivot 'item_pesanan'
    public function pesanan(): BelongsToMany // <--- DIUBAH DARI pesananItems(): HasMany
    {
        return $this->belongsToMany(Pesanan::class, 'item_pesanan', 'pupuk_id', 'pesanan_id')
            ->withPivot('jumlah', 'harga_saat_pesanan')
            ->withTimestamps();
    }

    // --- RELASI UNTUK KERANJANG (Jika ada keranjang_items yang punya pupuk_id) ---
    // Pupuk bisa ada di banyak KeranjangItem (HasMany)
    public function keranjangItems(): HasMany // <--- Tambahkan relasi ini jika Pupuk memiliki banyak KeranjangItem
    {
        return $this->hasMany(KeranjangItem::class, 'pupuk_id');
    }

    // PASTIKAN TIDAK ADA RELASI BERIKUT INI:
    // public function pupuk() { /* ... */ } // <--- JANGAN ADA RELASI DENGAN NAMA INI DI MODEL PUPUK

    // Accessor untuk mendapatkan URL gambar utama yang sudah di-transformasi oleh Cloudinary
    public function getGambarUtamaUrlAttribute(): ?string
    {
        if ($this->gambar_utama) {
            try {
                if (Str::startsWith($this->gambar_utama, ['http://', 'https://'])) {
                    return $this->gambar_utama;
                }
                return Cloudinary::url($this->gambar_utama, [
                    'secure' => true,
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to generate Cloudinary URL for pupuk ID {$this->id}, public ID: {$this->gambar_utama}. Error: " . $e->getMessage());
                return null;
            }
        }
        return null;
    }
}