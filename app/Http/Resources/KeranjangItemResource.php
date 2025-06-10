<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PupukResource; // Pastikan ini diimpor

class KeranjangItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            // 'pupuk' adalah relasi dari KeranjangItem ke Pupuk
            // Kita ingin resource PupukResource memformat data pupuk
            'pupuk' => new PupukResource($this->whenLoaded('pupuk')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}