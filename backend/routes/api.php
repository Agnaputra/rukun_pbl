<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KeluargaController;
use App\Http\Controllers\Api\RtController;
use App\Http\Controllers\Api\RwController;
use App\Http\Controllers\Api\WargaController;
use App\Http\Controllers\Api\WargaVerificationController;
use App\Http\Controllers\Api\TokoController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\MarketplaceController;

use App\Http\Controllers\Api\JenisIuranController;
use App\Http\Controllers\Api\TagihanController;
use App\Http\Controllers\Api\PembayaranController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\DokumenWargaController;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {


    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user()->load('role', 'warga', 'warga.toko');
    });
    Route::post('/warga/verifikasi-identitas', [WargaVerificationController::class, 'verifyIdentity']);


    Route::apiResource('dokumen', DokumenWargaController::class)->only(['index', 'store', 'destroy']);
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::post('/notifikasi/{id}/baca', [NotifikasiController::class, 'markAsRead']);



    Route::get('/marketplace/produk', [MarketplaceController::class, 'listProduk']);
    Route::get('/marketplace/produk/{id}', [MarketplaceController::class, 'showProduk']);
    Route::get('/marketplace/toko', [MarketplaceController::class, 'listToko']);
    Route::get('/marketplace/toko/{id}', [MarketplaceController::class, 'showToko']);


    Route::post('/pesanan', [PesananController::class, 'store']);
    Route::get('/pesanan/saya', [PesananController::class, 'getMyPesanan']);
    Route::post('/produk/{id}/review', [ReviewController::class, 'store']);


    Route::post('/toko', [TokoController::class, 'store']);
    Route::get('/toko/saya', [TokoController::class, 'getMyToko']);
    Route::put('/toko/saya', [TokoController::class, 'updateMyToko']);

    Route::middleware('hasToko')->group(function () {
        Route::apiResource('produk', ProdukController::class)->except(['show', 'update']);

    });


    Route::get('/tagihan/saya', [TagihanController::class, 'getMyTagihan']);
    Route::post('/pembayaran', [PembayaranController::class, 'store']);


    Route::get('/rw', [RwController::class, 'index']);
    Route::get('/rw/{id}', [RwController::class, 'show']);
    Route::get('/rt', [RtController::class, 'index']);
    Route::get('/rt/{id}', [RtController::class, 'show']);
    Route::get('/keluarga', [KeluargaController::class, 'index']);
    Route::get('/keluarga/{id}', [KeluargaController::class, 'show']);

    Route::get('/warga/{id}', [WargaController::class, 'show']);
    Route::put('/warga/{id}', [WargaController::class, 'update']);
    Route::patch('/warga/{id}', [WargaController::class, 'update']);




    Route::middleware('role:Admin')->group(function () {
        Route::apiResource('jenis-iuran', JenisIuranController::class);
    });


    Route::middleware('role:Admin,Bendahara')->group(function () {
        Route::apiResource('tagihan', TagihanController::class)->except(['show']);
        Route::post('/pembayaran/{id}/verifikasi', [PembayaranController::class, 'verifikasiPembayaran']);
    });


    Route::middleware('role:Admin')->group(function () {
        Route::apiResource('rw', RwController::class)->except(['index', 'show']);
    });
    Route::middleware('role:Admin,Ketua RW')->group(function () {
        Route::apiResource('rt', RtController::class)->except(['index', 'show']);
    });
    Route::middleware('role:Admin,Ketua RW,Ketua RT')->group(function () {
        Route::apiResource('keluarga', KeluargaController::class)->except(['index', 'show']);
        Route::apiResource('warga', WargaController::class)->except(['show', 'update']);
    });
});
