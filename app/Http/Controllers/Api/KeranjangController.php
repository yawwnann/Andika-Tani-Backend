<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeranjangItem;
use App\Models\Pupuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\KeranjangItemResource;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class KeranjangController extends Controller
{
    /**
     * Display a listing of the resource (tampilkan isi keranjang user).
     * GET /api/keranjang
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // PERBAIKAN: Eager loading yang benar:
        // 'pupuk.kategoriPupuk' akan memuat item keranjang, lalu pupuk yang terhubung
        // dengan item tersebut, dan kemudian kategori pupuk yang terhubung dengan pupuk.
        $keranjangItems = $user->keranjangItems()->with('pupuk.kategoriPupuk')->get(); // <--- KOREKSI PENTING

        return KeranjangItemResource::collection($keranjangItems);
    }

    /**
     * Store a newly created resource in storage (tambahkan item ke keranjang).
     * POST /api/keranjang
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'pupuk_id' => ['required', Rule::exists('pupuk', 'id')->where(fn($query) => $query->where('stok', '>', 0))],
            'quantity' => 'required|integer|min:1',
        ]);

        $pupukId = $validated['pupuk_id'];
        $quantity = $validated['quantity'];
        $pupuk = Pupuk::find($pupukId);

        if (!$pupuk || $pupuk->stok < $quantity) {
            return response()->json(['message' => 'Stok pupuk tidak mencukupi atau pupuk tidak ditemukan.'], 422);
        }

        $keranjangItem = $user->keranjangItems()
            ->where('pupuk_id', $pupukId)
            ->first();

        if ($keranjangItem) {
            $newQuantity = $keranjangItem->quantity + $quantity;
            if ($newQuantity > $pupuk->stok) {
                return response()->json(['message' => 'Stok pupuk tidak cukup untuk jumlah ini.', 'stok_tersisa' => $pupuk->stok], 422);
            }
            $keranjangItem->quantity = $newQuantity;
            $keranjangItem->save();
        } else {
            $keranjangItem = $user->keranjangItems()->create([
                'pupuk_id' => $pupukId,
                'quantity' => $quantity,
            ]);
        }

        // PERBAIKAN: Eager loading yang benar setelah item disimpan/diupdate
        return new KeranjangItemResource($keranjangItem->load('pupuk.kategoriPupuk')); // <--- KOREKSI PENTING
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /api/keranjang/{keranjangItem}
     */
    public function update(Request $request, KeranjangItem $keranjangItem)
    {
        $user = Auth::user();
        if (!$user || $keranjangItem->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate(['quantity' => 'required|integer|min:1']);
        $pupuk = $keranjangItem->pupuk;

        if (!$pupuk || $pupuk->stok < $validated['quantity']) {
            return response()->json(['message' => 'Stok pupuk tidak mencukupi.'], 422);
        }

        $keranjangItem->update(['quantity' => $validated['quantity']]);

        // PERBAIKAN: Eager loading yang benar setelah item diupdate
        return new KeranjangItemResource($keranjangItem->load('pupuk.kategoriPupuk')); // <--- KOREKSI PENTING
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/keranjang/{keranjangItem}
     */
    public function destroy(KeranjangItem $keranjangItem)
    {
        $user = Auth::user();
        if (!$user || $keranjangItem->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $keranjangItem->delete();
        return response()->json(['message' => 'Item berhasil dihapus dari keranjang.'], 200);
    }
}