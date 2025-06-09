<?php
// File: app/Http/Resources/Api/PupukResource.php

namespace App\Http\Resources; // Perhatikan namespace di sini (biasanya ada folder 'Api')

use App\Http\Resources\KategoriResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
// use Illuminate\Support\Facades\Storage; // Tidak digunakan di sini
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary; // Tidak digunakan langsung di toArray() ini

class PupukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Pastikan relasi 'kategoriPupuk' dimuat saat resource ini digunakan
        // Misalnya: Pupuk::with('kategoriPupuk')->find($id);

        return [
            'id' => $this->id,
            'nama_pupuk' => $this->nama_pupuk, // Mengacu pada kolom 'nama_pupuk'
            'slug' => $this->slug,
            'deskripsi' => $this->deskripsi,
            'harga' => (float) $this->harga, // Menggunakan float agar nilai koma tetap terjaga
            'stok' => (int) $this->stok,
            'status_ketersediaan' => $this->status_ketersediaan,

            // Kolom gambar_utama akan mengembalikan URL Cloudinary yang disimpan di database
            // Asumsi: Model Pupuk sudah menyimpan URL lengkap dari Cloudinary di atribut ini.
            // Jika ada accessor di model (misal: getGambarUtamaUrlAttribute), gunakan $this->gambar_utama_url
            'gambar_utama' => $this->gambar_utama,

            // Relasi ke KategoriPupukResource (pastikan KategoriResource sudah diupdate/disesuaikan)
            'kategori' => KategoriResource::make($this->whenLoaded('kategoriPupuk')), // Mengacu pada relasi kategoriPupuk

            'dibuat_pada' => $this->created_at->format('Y-m-d H:i:s'), // Format tanggal dan waktu
            'diupdate_pada' => $this->updated_at->format('Y-m-d H:i:s'), // Format tanggal dan waktu
        ];
    }
}