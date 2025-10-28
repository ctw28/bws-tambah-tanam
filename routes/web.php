<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('front');
});

Route::get('/form', function () {
    return view('form');
})->name('form');
Route::get('/login', function () {
    return view('login');
})->name('login-form');
Route::get('/admin/data', function () {
    return view('admin/data');
});

Route::get('/admin/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
Route::get('/admin/form-data', fn() => view('admin.data'))->name('form.data');
Route::get('/admin/form-permasalahan', fn() => view('admin.permasalahan'))->name('form.permasalahan');
Route::get('/admin/form-progres', fn() => view('admin.form-progres'))->name('form.progres');
Route::get('/admin/master/daerah-irigasi', fn() => view('admin.master.daerah-irigasi'))->name('master.di');
Route::get('/admin/master/saluran', fn() => view('admin.master.saluran'))->name('master.saluran');
Route::get('/admin/master/p3a', fn() => view('admin.master.p3a'))->name('master.p3a');
Route::get('/admin/juru', fn() => view('admin.juru'))->name('admin.juru');

Route::get('/admin/pengamat', fn() => view('admin.pengamat'))->name('admin.pengamat');
Route::get('/admin/upi', fn() => view('admin.upi'))->name('admin.upi');

Route::get('/pengamat', fn() => view('pengamat'))->name('pengamat');
Route::get('/upi', fn() => view('upi'))->name('upi');
Route::get('/komisi-irigasi', fn() => view('komisi-irigasi'))->name('komisi-irigasi');
Route::get('/form-preview', fn() => view('form-preview'))->name('form-preview');
Route::get('import-form', [ImportController::class, 'showForm'])->name('master.import.form');
Route::post('import-irigasi', [ImportController::class, 'import'])->name('import.submit');
