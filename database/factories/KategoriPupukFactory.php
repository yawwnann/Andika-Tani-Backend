<?php

namespace Database\Factories; // PASTIKAN NAMESPACE INI BENAR

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

// PASTIKAN NAMA CLASS INI BENAR
class KategoriPupukFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nama = 'Pupuk ' . $this->faker->unique()->words(2, true);
        return [
            'nama_kategori' => $nama,
            'slug' => Str::slug($nama),
            'deskripsi' => $this->faker->sentence(),
        ];
    }
}