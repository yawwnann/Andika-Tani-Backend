<?php

namespace App\Http\Resources;

use App\Http\Resources\Api\PupukResource as ApiPupukResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PupukResource; // <--- TAMBAHKAN BARIS INI
// use App\Http\Resources\IkanResource; // <-- Hapus atau komen ini, karena sudah tidak digunakan

class KeranjangItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            // Pastikan relasi 'pupuk' di-load di controller sebelum resource ini digunakan
            'pupuk' => new PupukResource($this->whenLoaded('pupuk')),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}