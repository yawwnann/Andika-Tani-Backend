<?php

namespace Database\Factories;

use App\Models\KategoriPupuk;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PupukFactory extends Factory
{
    protected $model = \App\Models\Pupuk::class;

    public function definition(): array
    {
        // Pastikan Anda mendapatkan ID kategori yang valid
        // Ini akan membuat kategori baru jika tidak ada yang ditemukan
        $kategoriPupuk = KategoriPupuk::inRandomOrder()->first();
        if (!$kategoriPupuk) {
            $kategoriPupuk = KategoriPupuk::factory()->create();
        }

        return [
            'kategori_pupuk_id' => $kategoriPupuk->id, // <-- Pastikan ini ada dan valid
            'nama_pupuk' => $this->faker->words(3, true), // <-- Pastikan ini ada dan menghasilkan string non-kosong
            'slug' => Str::slug($this->faker->unique()->words(3, true)), // <-- Pastikan ini ada dan unik
            'deskripsi' => $this->faker->paragraph(),
            'harga' => $this->faker->randomFloat(2, 10000, 500000), // <-- Pastikan ini ada
            'stok' => $this->faker->numberBetween(1, 100),
            'status_ketersediaan' => $this->faker->randomElement(['Tersedia', 'Habis']),
            'gambar_utama' => null, // Atau $this->faker->imageUrl(), jika Anda ingin gambar palsu
        ];
    }
}