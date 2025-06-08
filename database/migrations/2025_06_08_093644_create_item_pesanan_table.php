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
        Schema::create('item_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            // Mengganti ikan_id menjadi pupuk_id
            $table->foreignId('pupuk_id')->constrained('pupuk')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_saat_pesanan', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pesanan');
    }
};
