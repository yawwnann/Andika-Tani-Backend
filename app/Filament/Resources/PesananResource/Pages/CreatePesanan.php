<?php
// File: app/Filament/Resources/PesananResource/Pages/CreatePesanan.php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreatePesanan extends CreateRecord
{
    protected static string $resource = PesananResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan Anda menggunakan 'pupuk_id' dan 'harga_saat_pesanan' di sini
        $items = $data['items'] ?? [];
        $total = 0;
        if (is_array($items)) {
            foreach ($items as $item) {
                // Perhatikan: 'harga_saat_pesanan' (sesuai model Pesanan)
                $jumlah = $item['jumlah'] ?? 0;
                $harga = $item['harga_saat_pesanan'] ?? 0; // <--- Ganti 'harga_saat_pesan' jadi 'harga_saat_pesanan'
                if (!empty($jumlah) && !empty($harga)) {
                    $total += $jumlah * $harga;
                }
            }
        }
        $data['total_harga'] = $total;
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $itemsData = $data['items'] ?? [];
            $pesananData = Arr::except($data, ['items']);

            if (!isset($pesananData['total_harga'])) {
                $total = 0;
                if (is_array($itemsData)) {
                    foreach ($itemsData as $item) {
                        $jumlah = $item['jumlah'] ?? 0;
                        $harga = $item['harga_saat_pesanan'] ?? 0; // <--- Ganti 'harga_saat_pesan' jadi 'harga_saat_pesanan'
                        if (!empty($jumlah) && !empty($harga)) {
                            $total += $jumlah * $harga;
                        }
                    }
                }
                $pesananData['total_harga'] = $total;
            }

            Log::info('Membuat record Pesanan utama...', $pesananData);
            $record = static::getModel()::create($pesananData);
            Log::info("Record Pesanan utama dibuat, ID: {$record->id}");

            Log::info('Memulai proses attach item...');
            if (is_array($itemsData)) {
                foreach ($itemsData as $item) {
                    // GUNAKAN 'pupuk_id', BUKAN 'ikan_id'
                    $pupukId = $item['pupuk_id'] ?? null; // <--- Ganti 'ikan_id' jadi 'pupuk_id'
                    $jumlah = $item['jumlah'] ?? 0;
                    $harga = $item['harga_saat_pesanan'] ?? 0; // <--- Ganti 'harga_saat_pesan' jadi 'harga_saat_pesanan'

                    if ($pupukId && $jumlah > 0) {
                        Log::info("Attaching Pupuk ID: {$pupukId} with Jumlah: {$jumlah}, Harga: {$harga}");
                        try {
                            $record->items()->attach($pupukId, [
                                'jumlah' => $jumlah,
                                'harga_saat_pesanan' => $harga // <--- Ganti 'harga_saat_pesan' jadi 'harga_saat_pesanan'
                            ]);
                            Log::info("Berhasil attach Pupuk ID: {$pupukId}");
                        } catch (\Exception $e) {
                            Log::error("Gagal attach Pupuk ID: {$pupukId} - " . $e->getMessage());
                            // throw $e;
                        }
                    } else {
                        Log::warning('Skipping item, Pupuk ID atau Jumlah tidak valid:', $item); // Sesuaikan log
                    }
                }
            }
            Log::info("Proses attach item selesai untuk Pesanan ID: {$record->id}");

            return $record;
        });
    }
}