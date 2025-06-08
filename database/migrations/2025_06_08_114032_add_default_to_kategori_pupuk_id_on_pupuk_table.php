<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            // Ubah kolom kategori_pupuk_id yang sudah ada
            // Asumsikan kategori_pupuk_id adalah foreignId, sesuaikan tipe data jika berbeda (misal: unsignedBigInteger)
            // Pilih ID kategori yang ingin Anda jadikan default (misalnya, 'Umum' atau 'Lain-lain')
            // Ganti 1 dengan ID kategori_pupuk yang valid dan ada di tabel kategori_pupuk Anda.
            $table->foreignId('kategori_pupuk_id')
                ->default(1) // <-- Tambahkan ini. GANTI DENGAN ID KATEGORI YANG ADA!
                ->change(); // <-- Gunakan change() untuk mengubah kolom yang sudah ada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pupuk', function (Blueprint $table) {
            // Untuk rollback, hapus default value
            $table->foreignId('kategori_pupuk_id')->default(null)->change();
        });
    }
};