<?php

namespace Database\Factories;

use App\Models\KategoriPupuk;
use App\Models\Pupuk;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PupukFactory extends Factory
{
    protected $model = Pupuk::class;

    public function definition(): array
    {
        $kategoriPupuk = KategoriPupuk::inRandomOrder()->first();
        if (!$kategoriPupuk) {
            $kategoriPupuk = KategoriPupuk::factory()->create();
            Log::info("Created a new KategoriPupuk for seeding purposes.");
        }
        $kategoriPupukId = $kategoriPupuk->id;

        // Nama pupuk yang realistis
        $namaPupuk = $this->generateNamaPupuk();

        // Deskripsi pupuk yang panjang dan relevan
        $deskripsi = $this->generateDeskripsiPupuk();

        // Gambar pupuk yang relevan - menggunakan Unsplash dengan keyword fertilizer
        $fertilizerImages = [
            'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1605000797499-95a51c5269ae?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1592156668519-572bfef86c66?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=640&h=480&fit=crop',
            'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=640&h=480&fit=crop&auto=format',
            'https://images.unsplash.com/photo-1586771107445-d3ca888129ff?w=640&h=480&fit=crop',
        ];

        return [
            'kategori_pupuk_id' => $kategoriPupukId,
            'nama_pupuk' => $namaPupuk,
            'slug' => Str::slug($namaPupuk . '-' . uniqid()),
            'deskripsi' => $deskripsi,
            'harga' => $this->faker->randomFloat(2, 15000, 750000),
            'stok' => $this->faker->numberBetween(5, 200),
            'status_ketersediaan' => $this->faker->randomElement(['Tersedia', 'Tersedia', 'Tersedia', 'Habis']), // Lebih banyak tersedia
            'gambar_utama' => $this->faker->randomElement($fertilizerImages),
        ];
    }

    /**
     * Generate nama pupuk yang realistis
     */
    private function generateNamaPupuk(): string
    {
        $jenisUtama = $this->faker->randomElement([
            'NPK',
            'Urea',
            'Kompos',
            'Organik',
            'TSP',
            'KCl',
            'ZA',
            'Phonska'
        ]);

        $formula = $this->faker->randomElement([
            '16-16-16',
            '15-15-15',
            '20-10-10',
            '12-12-17',
            '25-7-7',
            '46-0-0'
        ]);

        $brand = $this->faker->randomElement([
            'Pupuk Nusantara',
            'Agro Prima',
            'Tani Subur',
            'Petani Jaya',
            'Harvest Gold',
            'Green Grow',
            'Super Tani',
            'Agro Max'
        ]);

        $tipe = $this->faker->randomElement([
            "$jenisUtama $formula",
            "$jenisUtama Premium",
            "$jenisUtama $brand",
            "Super $jenisUtama",
            "$jenisUtama Granule"
        ]);

        return $tipe;
    }

    /**
     * Generate deskripsi pupuk yang panjang dan relevan
     */
    private function generateDeskripsiPupuk(): string
    {
        $manfaat = $this->faker->randomElements([
            'meningkatkan hasil panen hingga 30%',
            'mempercepat pertumbuhan tanaman',
            'memperkuat sistem perakaran',
            'meningkatkan kualitas buah dan sayuran',
            'memperbaiki struktur tanah',
            'meningkatkan daya tahan tanaman terhadap hama',
            'mempercepat proses fotosintesis',
            'meningkatkan kandungan nutrisi pada hasil panen'
        ], 3);

        $komposisi = $this->faker->randomElements([
            'Nitrogen (N) untuk pertumbuhan daun yang optimal',
            'Fosfor (P) untuk perkembangan akar dan bunga',
            'Kalium (K) untuk kekuatan batang dan kualitas buah',
            'Magnesium untuk proses fotosintesis',
            'Sulfur untuk pembentukan protein',
            'Kalsium untuk struktur sel yang kuat',
            'Mikronutrien lengkap (Fe, Mn, Zn, Cu, B, Mo)'
        ], 4);

        $aplikasi = $this->faker->randomElements([
            'padi sawah dan padi gogo',
            'jagung dan tanaman serealia',
            'kedelai dan kacang-kacangan',
            'cabai dan tomat',
            'kentang dan umbi-umbian',
            'tanaman buah seperti mangga dan jeruk',
            'sayuran hijau dan kubis-kubisan',
            'tanaman hias dan bunga'
        ], 3);

        $keunggulan = $this->faker->randomElements([
            'formulasi granule yang mudah diserap tanaman',
            'tahan cuaca dan tidak mudah tercuci hujan',
            'ramah lingkungan dan aman untuk tanah',
            'meningkatkan efisiensi pemupukan',
            'mengurangi frekuensi aplikasi',
            'tersedia dalam kemasan praktis',
            'sudah teruji di berbagai kondisi lahan'
        ], 3);

        $deskripsi = "Pupuk berkualitas tinggi yang diformulasikan khusus untuk " . implode(', ', $aplikasi) . ". ";
        $deskripsi .= "Produk ini mengandung " . implode(', ', $komposisi) . " yang memberikan nutrisi lengkap dan seimbang untuk tanaman. ";
        $deskripsi .= "Dengan kandungan nutrisi yang optimal, pupuk ini mampu " . implode(', ', $manfaat) . ". ";
        $deskripsi .= "Keunggulan produk meliputi " . implode(', ', $keunggulan) . ". ";

        $petunjukPenggunaan = "Cara aplikasi: taburkan secara merata di sekitar tanaman dengan dosis " .
            $this->faker->numberBetween(100, 500) . " gram per tanaman, kemudian siram dengan air secukupnya. ";

        $deskripsi .= $petunjukPenggunaan;
        $deskripsi .= "Aplikasi dilakukan setiap " . $this->faker->randomElement(['2-3 minggu', '1 bulan', '6-8 minggu']) . " sekali ";
        $deskripsi .= "atau sesuai dengan fase pertumbuhan tanaman. Untuk hasil maksimal, kombinasikan dengan pupuk organik ";
        $deskripsi .= "dan pastikan tanaman mendapat cukup air serta sinar matahari. Simpan di tempat kering dan terhindar dari sinar matahari langsung.";

        return $deskripsi;
    }
}