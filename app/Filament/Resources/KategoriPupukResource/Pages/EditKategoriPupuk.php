<?php

namespace App\Filament\Resources\KategoriPupukResource\Pages;

use App\Filament\Resources\KategoriPupukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriPupuk extends EditRecord
{
    protected static string $resource = KategoriPupukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol "Delete" di pojok kanan atas halaman edit
            Actions\DeleteAction::make(),
        ];
    }

    // (Opsional) Arahkan kembali ke halaman utama setelah berhasil mengubah data
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}