<?php

namespace App\Filament\Resources\PupukResource\Pages;

use App\Filament\Resources\PupukResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePupuk extends CreateRecord
{
    protected static string $resource = PupukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}