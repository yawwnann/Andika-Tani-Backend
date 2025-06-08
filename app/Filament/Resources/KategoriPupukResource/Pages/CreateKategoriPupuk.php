<?php

namespace App\Filament\Resources\KategoriPupukResource\Pages;

use App\Filament\Resources\KategoriPupukResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKategoriPupuk extends CreateRecord
{
    protected static string $resource = KategoriPupukResource::class;

    // (Opsional) Arahkan kembali ke halaman utama setelah berhasil membuat data
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}