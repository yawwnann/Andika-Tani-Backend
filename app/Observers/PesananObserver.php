<?php
// app/Observers/PesananObserver.php
namespace App\Observers;

use App\Models\Pesanan;
use App\Models\Pupuk; // <-- Ganti ini dari App\Models\Ikan
use Illuminate\Support\Facades\Log;

class PesananObserver
{
    public function created(Pesanan $pesanan): void
    {
        Log::info("--- PesananObserver@created START for Pesanan ID: {$pesanan->id} ---");
        try {
            $pesanan->load('items');
            $items = $pesanan->items;

            if ($items && $items->count() > 0) {
                Log::info("Found {$items->count()} items for Pesanan ID: {$pesanan->id}");

                foreach ($items as $item) {
                    // Pastikan $item adalah objek model Pupuk
                    // Ini akan bekerja karena relasi items() di Pesanan adalah ke Pupuk
                    $jumlahDipesan = $item->pivot->jumlah;
                    Log::info("Processing Item -> Pupuk ID: {$item->id}, Nama: {$item->nama_pupuk}, Stok Saat Ini: {$item->stok}, Jumlah Dipesan: {$jumlahDipesan}"); // Ganti nama_ikan jadi nama_pupuk

                    if ($item->stok >= $jumlahDipesan) {
                        Log::info("Attempting to decrement stock for Pupuk ID: {$item->id} by {$jumlahDipesan}");
                        $affectedRows = $item->decrement('stok', $jumlahDipesan);
                        Log::info("Decrement result (affected rows): {$affectedRows} for Pupuk ID: {$item->id}");
                    } else {
                        Log::warning("Stok TIDAK CUKUP for Pupuk ID {$item->id}. Stok: {$item->stok}, Dipesan: {$jumlahDipesan}");
                    }
                }
            } else {
                Log::warning("Tidak ada item relasi ditemukan (items) untuk Pesanan ID: {$pesanan->id} saat observer 'created' dijalankan.");
            }
        } catch (\Exception $e) {
            Log::error("Error in PesananObserver@created for Pesanan ID: {$pesanan->id} - " . $e->getMessage(), ['exception' => $e]);
        }
        Log::info("--- PesananObserver@created END for Pesanan ID: {$pesanan->id} ---");
    }
    public function updated(Pesanan $pesanan): void
    { /* ... */
    }
    public function deleted(Pesanan $pesanan): void
    { /* ... */
    }
}