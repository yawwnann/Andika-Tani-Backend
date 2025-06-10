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
use Illuminate\Support\Facades\Log;

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

        $keranjangItems = $user->keranjangItems()->with('pupuk.kategoriPupuk')->get();
        return KeranjangItemResource::collection($keranjangItems);
    }

    /**
     * Store a newly created resource in storage (tambahkan item ke keranjang).
     * POST /api/keranjang
     */
    public function store(Request $request)
    {
        try {
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

            if (!$pupuk) {
                return response()->json(['message' => 'Pupuk tidak ditemukan.'], 404);
            }

            if ($pupuk->stok < $quantity) {
                return response()->json([
                    'message' => 'Stok pupuk tidak mencukupi.',
                    'stok_tersedia' => $pupuk->stok
                ], 422);
            }

            $keranjangItem = $user->keranjangItems()
                ->where('pupuk_id', $pupukId)
                ->first();

            if ($keranjangItem) {
                $newQuantity = $keranjangItem->quantity + $quantity;
                if ($newQuantity > $pupuk->stok) {
                    return response()->json([
                        'message' => 'Stok pupuk tidak cukup untuk jumlah ini.',
                        'stok_tersedia' => $pupuk->stok
                    ], 422);
                }
                $keranjangItem->quantity = $newQuantity;
                $keranjangItem->save();
            } else {
                $keranjangItem = $user->keranjangItems()->create([
                    'user_id' => $user->id,
                    'pupuk_id' => $pupukId,
                    'quantity' => $quantity,
                ]);
            }

            return new KeranjangItemResource($keranjangItem->load('pupuk.kategoriPupuk'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in KeranjangController@store', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in KeranjangController@store', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat menambahkan item ke keranjang.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /api/keranjang/{keranjangItem}
     */
    public function update(Request $request, KeranjangItem $keranjangItem)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1']);
        $pupuk = $keranjangItem->pupuk;

        if (!$pupuk || $pupuk->stok < $validated['quantity']) {
            return response()->json(['message' => 'Stok pupuk tidak mencukupi.'], 422);
        }

        $keranjangItem->update(['quantity' => $validated['quantity']]);

        // PERBAIKAN: Eager loading yang benar setelah item diupdate
        return new KeranjangItemResource($keranjangItem->load('pupuk.kategoriPupuk'));
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/keranjang/{keranjangItem}
     */
    public function destroy(KeranjangItem $keranjangItem)
    {
        $keranjangItem->delete();
        return response()->json(['message' => 'Item berhasil dihapus dari keranjang.'], 200);
    }

    /**
     * Remove all items from the user's cart.
     * DELETE /api/keranjang/clear
     */
    public function clear()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user->keranjangItems()->delete();
        return response()->json(['message' => 'Keranjang berhasil dikosongkan.'], 200);
    }
}