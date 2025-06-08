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
        Schema::create('pupuk', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel kategori_pupuk
            $table->foreignId('kategori_pupuk_id')->constrained('kategori_pupuk')->cascadeOnDelete();
            $table->string('nama_pupuk', 150);
            $table->string('slug', 170)->unique();
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2); // Menggunakan 2 angka di belakang koma untuk Rupiah
            $table->integer('stok')->default(0);
            $table->string('status_ketersediaan', 50)->default('Tersedia');
            $table->string('gambar_utama', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pupuk');
    }
};
