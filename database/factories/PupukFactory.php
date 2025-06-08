<?php

namespace Database\Factories;

// DIUBAH: Import model yang relevan
use App\Models\Pupuk;
use App\Models\KategoriPupuk;
// Import lain yang tetap digunakan
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pupuk>
 */
class PupukFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pupuk::class; // DIUBAH: Tentukan model yang benar

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // DIUBAH: Gunakan KategoriPupuk
        $kategoriPupukId = KategoriPupuk::inRandomOrder()->first()?->id;
        if (!$kategoriPupukId) {
            // Buat kategori baru jika tidak ada sama sekali
            $kategoriPupukId = KategoriPupuk::factory()->create()->id;
        }

        // DIUBAH: Daftar nama produk diganti menjadi nama pupuk
        $namaPupukList = ['NPK Mutiara', 'Urea Non-Subsidi', 'ZA Petro', 'KCL Mahkota', 'Kompos Organik', 'Pupuk Kandang Fermentasi', 'Gandasil Daun', 'Grow More Bunga', 'POC Cair', 'Dolomit Super'];
        $namaPupuk = $this->faker->randomElement($namaPupukList) . ' ' . $this->faker->companySuffix(); // Tambahkan suffix perusahaan

        $stok = $this->faker->numberBetween(0, 250);

        // --- LOGIKA PENGAMBILAN GAMBAR DARI PIXABAY (TETAP DIGUNAKAN) ---
        $imageUrl = null;
        $apiKey = env('PIXABAY_API_KEY');

        if (!empty($apiKey)) {
            $queryParts = explode(' ', $namaPupuk);
            $baseQuery = $queryParts[0];

            // DIUBAH: Kata kunci pencarian diubah menjadi "fertilizer"
            $searchQuery = urlencode($baseQuery . ' fertilizer');

            // DIUBAH: Kategori pencarian diubah menjadi 'nature' atau 'science'
            $apiUrl = "https://pixabay.com/api/?key={$apiKey}&q={$searchQuery}&image_type=photo&category=nature&safesearch=true&per_page=3";

            try {
                $response = Http::timeout(10)->get($apiUrl);

                if ($response->successful() && $response->json('totalHits') > 0) {
                    $imageUrl = $response->json('hits.0.webformatURL');
                } else {
                    Log::warning("Pixabay API: No relevant fertilizer found for query '{$searchQuery}'.");
                }
            } catch (\Exception $e) {
                Log::error("Pixabay API Error for query {$searchQuery}: " . $e->getMessage());
            }
        } else {
            Log::warning('PIXABAY_API_KEY is not set in .env file.');
        }

        // DIUBAH: Fallback placeholder disesuaikan
        if (is_null($imageUrl)) {
            $imageUrl = 'https://via.placeholder.com/300x200.png?text=Pupuk+Not+Found';
        }
        // ----------------------------------------------

        return [
            // DIUBAH: Sesuaikan dengan nama kolom di tabel 'pupuk'
            'kategori_pupuk_id' => $kategoriPupukId,
            'nama_pupuk' => $namaPupuk,
            'slug' => Str::slug($namaPupuk) . '-' . uniqid(),
            'deskripsi' => $this->faker->paragraph(3),
            'harga' => $this->faker->numberBetween(15000, 250000),
            'stok' => $stok,
            'status_ketersediaan' => $stok > 0 ? 'Tersedia' : 'Habis',
            'gambar_utama' => $imageUrl,
        ];
    }
}