<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeranjangItem extends Model
{
    use HasFactory;

    protected $table = 'keranjang_items'; // Pastikan nama tabel benar

    protected $fillable = [
        'user_id',
        'pupuk_id', // <--- PASTIKAN INI ADA
        'quantity',
    ];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Pupuk (sebelumnya Ikan)
    public function pupuk(): BelongsTo // <--- PASTIKAN INI ADA DAN MENUNJUK KE PUPUK
    {
        return $this->belongsTo(Pupuk::class); // Menunjuk ke model Pupuk
    }
}