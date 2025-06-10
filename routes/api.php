<?php

// File: routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- IMPORTS CONTROLLER API ---
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PupukController; // <--- DIUBAH: Import PupukController
use App\Http\Controllers\Api\PesananApiController;
use App\Http\Controllers\Api\KeranjangController;
use App\Http\Controllers\Api\PaymentProofController;
use App\Http\Controllers\Api\UserProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// == API Endpoints Katalog Produk (Pupuk) - Publik ==
// Endpoint untuk mendapatkan daftar kategori pupuk
Route::get('/kategori', [PupukController::class, 'daftarKategori'])->name('api.kategori.index'); // Menggunakan PupukController

// Endpoint untuk mendapatkan daftar pupuk
Route::get('/pupuk', [PupukController::class, 'index'])->name('api.pupuk.index'); // <--- DIUBAH: /ikan -> /pupuk
// Endpoint untuk mendapatkan informasi pupuk berdasarkan slug
Route::get('/pupuk/{pupuk:slug}', [PupukController::class, 'show'])->name('api.pupuk.show'); // <--- DIUBAH: /ikan/{ikan:slug} -> /pupuk/{pupuk:slug}


// == API Endpoints Otentikasi - Publik ==
// Endpoint untuk registrasi pengguna baru
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
// Endpoint untuk login dan mendapatkan token autentikasi
Route::post('/login', [AuthController::class, 'login'])->name('api.login');


// == API Endpoints yang Memerlukan Otentikasi (Sanctum Token) ==
// Semua endpoint yang memerlukan otentikasi berada di dalam grup ini
Route::middleware('auth:api')->group(function () {

    // --- Autentikasi & Profil Pengguna ---
    // Endpoint untuk melakukan logout dan menghapus token
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    // Endpoint untuk mendapatkan data pengguna yang sedang login
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
    // Endpoint untuk update foto profil pengguna
    Route::post('/user/profile-photo', [UserProfileController::class, 'updateProfilePhoto'])->name('user.photo.update');
    // Tambahkan route untuk menghapus foto jika diperlukan
    // Route::delete('/user/profile-photo', [UserProfileController::class, 'deleteProfilePhoto'])->name('user.photo.delete');


    // --- Manajemen Pesanan ---
    // Route resource API untuk pesanan (index, show, store, update, destroy)
    Route::apiResource('pesanan', PesananApiController::class); // <--- Menggantikan beberapa route manual di bawah ini
    // Route::get('/pesanan', [PesananApiController::class, 'index'])->name('api.pesanan.index'); // DIHAPUS, sudah ada di apiResource
    // Route::post('/pesanan', [PesananApiController::class, 'store'])->name('api.pesanan.store'); // DIHAPUS, sudah ada di apiResource
    // Route::get('/pesanan/{pesanan}', [PesananApiController::class, 'show'])->name('api.pesanan.show'); // DIHAPUS, sudah ada di apiResource
    // Route::put('/pesanan/{pesanan}', [PesananApiController::class, 'update'])->name('api.pesanan.update'); // DIHAPUS, sudah ada di apiResource
    // Route::delete('/pesanan/{pesanan}', [PesananApiController::class, 'destroy'])->name('api.pesanan.destroy'); // DIHAPUS, sudah ada di apiResource

    // Route kustom untuk pesanan
    Route::post('/pesanan/{pesanan}/submit-payment-proof', [PaymentProofController::class, 'submitProof'])
        ->name('api.pesanan.submitProof');
    Route::put('/pesanan/{pesanan}/tandai-selesai', [PesananApiController::class, 'tandaiSelesai'])->name('api.pesanan.tandaiSelesai');


    // --- Manajemen Keranjang Belanja ---
    // Route resource API untuk keranjang belanja (index, store, update, destroy)
    Route::apiResource('keranjang', KeranjangController::class)->except(['show']); // Asumsi tidak perlu show untuk item keranjang tunggal
    Route::delete('/keranjang/clear', [KeranjangController::class, 'clear'])->name('keranjang.clear');
    // Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang.index'); // DIHAPUS, sudah ada di apiResource
    // Route::post('/keranjang', [KeranjangController::class, 'store'])->name('keranjang.store'); // DIHAPUS, sudah ada di apiResource
    // Route::put('/keranjang/{keranjangItem}', [KeranjangController::class, 'update'])->name('keranjang.update'); // DIHAPUS, sudah ada di apiResource
    // Route::delete('/keranjang/{keranjangItem}', [KeranjangController::class, 'destroy'])->name('keranjang.destroy'); // DIHAPUS, sudah ada di apiResource

});


// Route fallback jika endpoint API tidak ditemukan (opsional)
// Jika endpoint yang diminta tidak ada, akan memberikan respons error 404
Route::fallback(function () {
    return response()->json(['message' => 'Endpoint tidak ditemukan.'], 404);
});

