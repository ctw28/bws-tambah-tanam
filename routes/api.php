<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KabupatenController;
use App\Http\Controllers\Api\DaerahIrigasiController;
use App\Http\Controllers\Api\PetugasController;
use App\Http\Controllers\Api\SaluranController;
use App\Http\Controllers\Api\BangunanController;
use App\Http\Controllers\Api\DaerahIrigasiUpiController;
use App\Http\Controllers\Api\PetakController;
use App\Http\Controllers\Api\MasterPermasalahanController;
use App\Http\Controllers\Api\FormPengisianController;
use App\Http\Controllers\Api\FormPermasalahanController;
use App\Http\Controllers\Api\FormValidasiController;
use App\Http\Controllers\Api\KoordinatorController;
use App\Http\Controllers\Api\PengamatController;
use App\Http\Controllers\Api\SesiController;
use App\Http\Controllers\Api\UpiController;
use App\Http\Controllers\AuthController;

Route::post('login', [AuthController::class, 'login']);


Route::middleware(['auth:api'])->group(function () {
    Route::middleware(['role:koordinator'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('user-dis', [FormPengisianController::class, 'getUserDis']);
        Route::get('koordinator-di', [KoordinatorController::class, 'getDaerahIrigasiUser']);


        Route::prefix('master')->group(function () {
            Route::apiResource('sesis', SesiController::class);
            Route::apiResource('kabupaten', KabupatenController::class);
            Route::apiResource('daerah-irigasi', DaerahIrigasiController::class);
            Route::post('daerah-irigasi/import', [DaerahIrigasiController::class, 'import'])->name('daerah-irigasi.import');
            Route::apiResource('saluran', SaluranController::class);
            Route::apiResource('bangunan', BangunanController::class);
            Route::apiResource('petak', PetakController::class);
            Route::apiResource('permasalahan', MasterPermasalahanController::class);
            Route::apiResource('petugas', PetugasController::class);
            Route::apiResource('pengamat', PengamatController::class);
            Route::apiResource('upi', UpiController::class);
        });
        Route::post('/petugas/{petugas}/send-kode', [PetugasController::class, 'sendKode']);
        Route::post('/pengamat/{pengamat}/send-kode', [PengamatController::class, 'sendKode']);
        Route::post('/upi/{upi}/send-kode', [UpiController::class, 'sendKode']);
    });
});

Route::prefix('master')->group(function () {
    Route::get('sesi', [SesiController::class, 'index']);
    Route::get('kabupaten', [KabupatenController::class, 'index']);
    Route::get('daerah-irigasi', [DaerahIrigasiController::class, 'index']);
    Route::get('saluran', [SaluranController::class, 'index']);
    Route::get('bangunan', [BangunanController::class, 'index']);
    Route::get('petak', [PetakController::class, 'index']);
    Route::get('permasalahan', [MasterPermasalahanController::class, 'index']);
    Route::get('petugas', [PetugasController::class, 'index']);
    Route::get('pengamat', [PengamatController::class, 'index']);
    Route::get('upi', [UpiController::class, 'index']);
});

//validasi kode
Route::post('petugas/validasi-kode', [PetugasController::class, 'validasiKode']);
Route::post('pengamat/validasi-kode', [PengamatController::class, 'validasiKode']);
Route::post('upi/validasi-kode', [UpiController::class, 'validasiKode']);

Route::post('/pengamat/validasi/{id}', [FormValidasiController::class, 'validateByPengamat']);
Route::post('/upi/validasi/{id}', [FormValidasiController::class, 'validateByUpi']);

// Transaksi
Route::apiResource('form-pengisian', FormPengisianController::class);
Route::apiResource('form-permasalahan', FormPermasalahanController::class);
