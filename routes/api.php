<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\TemplateBahanPakaianDetail;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PesananCustomController;
use App\Http\Controllers\PesananProdukController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// routes/api.php
// routes/api.php
Route::get('/template-bahan-pakaian/{id}/bahan-baku', function ($id) {
    $templateBahanPakaian = \App\Models\TemplateDetail::with('details.bahanBaku')->findOrFail($id);
    return response()->json($templateBahanPakaian->details);
});

// routes/api.php
Route::get('/produk/{id}/ukuran', [ProdukController::class, 'getUkuranByProduk']);
Route::get('/warna-by-produk/{produk_id}', [ProdukController::class, 'getWarnaByProduk']);
Route::get('/ukuran-by-warna/{warna_id}', [ProdukController::class, 'getUkuranByWarna']);
Route::get('/get-warna-by-model/{modelId}', [PesananCustomController::class, 'getWarnaByModel']);
