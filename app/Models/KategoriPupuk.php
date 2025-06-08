<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperKategoriPupuk
 */
class KategoriPupuk extends Model
{
    use HasFactory;

    protected $table = 'kategori_pupuk';

    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
    ];

    public function pupuk()
    {
        return $this->hasMany(Pupuk::class, 'kategori_pupuk_id');
    }
}