<?php
// File: app/Http/Resources/PesananResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource; // Uncomment jika Anda punya dan menggunakan UserResource
use App\Http\Resources\PupukResource; // <--- TAMBAHKAN INI

class PesananResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Pastikan relasi 'items.pupuk' dan 'user' (jika ada) sudah dimuat di controller.
        // PesananApiController Anda sudah seharusnya melakukan eager loading yang sesuai.

        return [
            'id' => $this->id,
            'nama_pelanggan' => $this->nama_pelanggan,
            'nomor_whatsapp' => $this->nomor_whatsapp,
            'alamat_pengiriman' => $this->alamat_pengiriman,
            'total_harga' => (float) $this->total_harga,
            // Diubah: 'tanggal_pesan' menjadi 'tanggal_pesanan'
            'tanggal_pesanan' => $this->tanggal_pesanan ? $this->tanggal_pesanan->format('Y-m-d H:i:s') : ($this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null),
            'status' => $this->status,
            'catatan' => $this->catatan,
            'nomor_resi' => $this->nomor_resi,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,

            // Field yang dibutuhkan oleh PesananDetailPage.jsx (Pastikan kolom ini ada di tabel 'pesanan')
            'status_pembayaran' => $this->status_pembayaran,
            'payment_proof_url' => $this->payment_proof_path,
            'metode_pembayaran' => $this->metode_pembayaran,

            // Relasi user (jika ada dan dibutuhkan)
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }, null),

            // Sertakan detail item pupuk yang dipesan
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(function ($pupuk) { // <--- $pupuk di sini adalah instance model Pupuk
                    return [
                        'id' => $pupuk->id,
                        'pupuk_id' => $pupuk->id, // <--- Diubah dari ikan_id
                        'nama_pupuk' => $pupuk->nama_pupuk, // <--- Diubah dari nama_ikan
                        'slug' => $pupuk->slug,
                        'gambar_utama' => $pupuk->gambar_utama, // Untuk gambar pupuk per item
                        'jumlah' => (int) $pupuk->pivot->jumlah,
                        'harga_saat_pesanan' => (float) $pupuk->pivot->harga_saat_pesanan, // <--- Diubah dari harga_saat_pesan
                        'subtotal' => (float) ($pupuk->pivot->jumlah * $pupuk->pivot->harga_saat_pesanan), // <--- Diubah
                        // Jika Anda ingin menyertakan kategori pupuk di sini, pastikan
                        // relasi Pupuk::kategoriPupuk() sudah di-eager load (misal: items.pupuk.kategoriPupuk)
                        'kategori' => KategoriResource::make($pupuk->whenLoaded('kategoriPupuk')),
                    ];
                });
            }, []),
        ];
    }
}