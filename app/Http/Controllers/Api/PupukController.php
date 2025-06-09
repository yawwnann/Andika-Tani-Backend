<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PupukResource;
use App\Http\Resources\KategoriResource; // <--- TAMBAHKAN BARIS INI (jika belum ada)
use App\Models\Pupuk;
use App\Models\KategoriPupuk;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PupukController extends Controller
{
    /**
     * Menampilkan daftar pupuk dengan filter dan pencarian.
     * GET /api/pupuk
     */
    public function index(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:100',
            'sort' => 'nullable|string|in:harga,created_at,nama_pupuk',
            'order' => 'nullable|string|in:asc,desc',
            'status_ketersediaan' => 'nullable|string|in:Tersedia,Habis',
            'kategori_slug' => 'nullable|string|exists:kategori_pupuk,slug'
        ]);

        $searchQuery = $request->query('q');
        $sortBy = $request->query('sort', 'created_at');
        $sortOrder = $request->query('order', 'desc');
        $statusKetersediaan = $request->query('status_ketersediaan');
        $kategoriSlug = $request->query('kategori_slug');

        $pupukQuery = Pupuk::with('kategoriPupuk');

        if ($statusKetersediaan) {
            $pupukQuery->where('status_ketersediaan', $statusKetersediaan);
        }

        if ($kategoriSlug) {
            $pupukQuery->whereHas('kategoriPupuk', function (Builder $query) use ($kategoriSlug) {
                $query->where('slug', $kategoriSlug);
            });
        }

        if ($searchQuery) {
            $pupukQuery->where(function (Builder $query) use ($searchQuery) {
                $query->where('nama_pupuk', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('deskripsi', 'LIKE', "%{$searchQuery}%");
            });
        }

        $allowedSorts = ['harga', 'created_at', 'nama_pupuk'];
        $sortField = in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
        $sortDirection = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';

        $pupukQuery->orderBy($sortField, $sortDirection);

        if ($sortField !== 'nama_pupuk') {
            $pupukQuery->orderBy('nama_pupuk', 'asc');
        }

        $pupuk = $pupukQuery->paginate(12)->withQueryString();

        return PupukResource::collection($pupuk);
    }

    /**
     * Menampilkan detail satu pupuk.
     * GET /api/pupuk/{pupuk}
     */
    public function show(Pupuk $pupuk)
    {
        $pupuk->loadMissing('kategoriPupuk');

        return new PupukResource($pupuk);
    }

    /**
     * Menampilkan daftar kategori pupuk.
     * GET /api/pupuk/kategori
     */
    public function daftarKategori()
    {
        $kategori = KategoriPupuk::orderBy('nama_kategori', 'asc')->get();

        return KategoriResource::collection($kategori);
    }
}