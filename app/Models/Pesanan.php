<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperPesanan
 */
class Pesanan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'pesanan';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'nama_pelanggan',
        'nomor_whatsapp',
        'alamat_pengiriman',
        'total_harga',
        'metode_pembayaran',
        'status_pembayaran',
        'tanggal_pesanan', // Diperbaiki dari 'tanggal_pesan'
        'status',
        'nomor_resi',
        'payment_proof_path', // Ditambahkan
        'catatan',
        'catatan_admin',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_harga' => 'decimal:2', // Diperbaiki dari 'integer' agar bisa koma
        'tanggal_pesanan' => 'date',      // Diperbaiki dari 'tanggal_pesan'
    ];

    /**
     * Relasi Many-to-Many ke model Pupuk melalui tabel pivot 'item_pesanan'.
     * Nama method 'items' dipertahankan untuk kompatibilitas dengan Filament Repeater.
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Pupuk::class, 'item_pesanan') // Diubah ke Pupuk & item_pesanan
            ->withPivot('jumlah', 'harga_saat_pesanan') // Diperbaiki: harga_saat_pesanan
            ->withTimestamps();
    }

    /**
     * Relasi BelongsTo ke model User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor untuk mendapatkan URL thumbnail bukti pembayaran dari Cloudinary.
     */
    public function getPaymentProofThumbnailAttribute(): ?string
    {
        if ($this->payment_proof_path) {
            // Asumsi payment_proof_path adalah URL Cloudinary lengkap
            if (Str::contains($this->payment_proof_path, '/upload/')) {
                return Str::replaceFirst('/upload/', '/upload/w_80,h_80,c_thumb,q_auto,f_auto/', $this->payment_proof_path);
            }
            return $this->payment_proof_path;
        }
        return null;
    }

    /**
     * Accessor untuk mendapatkan format status pesanan yang lebih rapi.
     */
    public function getFormattedStatusAttribute(): string
    {
        return $this->status ? ucwords(str_replace('_', ' ', $this->status)) : 'N/A';
    }

    /**
     * Accessor untuk mendapatkan format status pembayaran yang lebih rapi.
     */
    public function getFormattedStatusPembayaranAttribute(): string
    {
        return $this->status_pembayaran ? ucwords(str_replace('_', ' ', $this->status_pembayaran)) : 'N/A';
    }
}