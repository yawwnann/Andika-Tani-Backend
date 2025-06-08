<?php

namespace App\Filament\Resources\KategoriPupukResource\Pages;

use App\Filament\Resources\KategoriPupukResource; // <- Pastikan ini benar
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriPupuk extends ListRecords // <- Ubah nama class
{
    protected static string $resource = KategoriPupukResource::class; // <- Pastikan ini benar

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}