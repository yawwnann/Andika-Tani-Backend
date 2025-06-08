<?php

// app/Filament/Resources/PupukResource/Pages/CreatePupuk.php

namespace App\Filament\Resources\PupukResource\Pages;

use App\Filament\Resources\PupukResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePupuk extends CreateRecord
{
    protected static string $resource = PupukResource::class;

    // Tambahkan metode ini jika belum ada, atau modifikasi jika sudah ada
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        \Log::info('Data received before creating Pupuk:', $data); // Log data
        // dd($data); // <--- HILANGKAN KOMENTAR INI SEMENTARA UNTUK MELIHAT DATA
        return $data;
    }

    // Atau jika Anda memiliki metode handleRecordCreation kustom:
    // protected function handleRecordCreation(array $data): Model
    // {
    //     \Log::info('Data received in handleRecordCreation:', $data);
    //     // dd($data); // <--- HILANGKAN KOMENTAR INI SEMENTARA UNTUK MELIHAT DATA
    //     return static::getModel()::create($data);
    // }
}