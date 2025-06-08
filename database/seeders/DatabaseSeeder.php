<?php

namespace Database\Seeders;

// Gunakan use untuk mengimpor semua model yang dibutuhkan
use App\Models\KategoriPupuk;
use App\Models\Pesanan;
use App\Models\Pupuk;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. BUAT ROLES
        // ===========================================
        $this->command->info('Membuat Roles...');
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'slug' => 'admin']);
        $customerRole = Role::firstOrCreate(['name' => 'Pelanggan', 'slug' => 'pelanggan']);
        $this->command->info('Roles dibuat.');

        // 2. BUAT USER
        // ===========================================
        $this->command->info('Membuat Users...');
        // User Admin
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@pupuk.com'],
            [
                'name' => 'Admin Pupuk',
                'password' => Hash::make('password'),
            ]
        );
        $adminUser->roles()->sync($adminRole);

        // User Pelanggan
        $pelangganUsers = User::factory(10)->create();
        foreach ($pelangganUsers as $pelanggan) {
            $pelanggan->roles()->sync($customerRole);
        }
        $this->command->info('Users dibuat.');

        // 3. BUAT DATA PRODUK
        // ===========================================
        $this->command->info('Membuat Kategori dan Pupuk...');
        KategoriPupuk::factory(5)->create();
        $pupukList = Pupuk::factory(30)->create();
        $this->command->info('Kategori dan Pupuk dibuat.');


        // 4. BUAT DATA PESANAN (LOGIKA PALING KOMPLEKS)
        // ===========================================
        $this->command->info('Membuat Pesanan dan item-itemnya...');
        Pesanan::factory(25)->create([
            // Pastikan pesanan dibuat oleh user dengan role pelanggan
            'user_id' => $pelangganUsers->random()->id,
        ])->each(function (Pesanan $pesanan) use ($pupukList) {
            // Untuk setiap pesanan, tambahkan 1 sampai 3 jenis pupuk secara acak
            $itemsToAttach = $pupukList->random(rand(1, 3));
            $totalHarga = 0;

            foreach ($itemsToAttach as $pupuk) {
                $jumlah = rand(1, 5);
                $hargaSaatPesan = $pupuk->harga;
                $totalHarga += $jumlah * $hargaSaatPesan;

                // Lampirkan pupuk ke pesanan melalui tabel pivot 'item_pesanan'
                // beserta data tambahan (jumlah & harga_saat_pesanan)
                $pesanan->items()->attach($pupuk->id, [
                    'jumlah' => $jumlah,
                    'harga_saat_pesanan' => $hargaSaatPesan,
                ]);
            }

            // Setelah semua item ditambahkan, update total harga di pesanan utama
            $pesanan->update(['total_harga' => $totalHarga]);
        });
        $this->command->info('Pesanan dan item-itemnya dibuat.');

        $this->command->info('Database seeding selesai!');
    }
}