<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ResetController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Auth::routes();

//reset password
Route::post('login/{provider}/callback', 'Auth\LoginController@handleCallback');
Route::get('/email/verify', [ResetController::class, 'verify_email'])->name('verify');
Route::get('/password/reset', [ResetController::class, 'index'])->name('index');
Route::post('/password/reset/store', [ResetController::class, 'store'])->name('store');

Route::group(['middleware' => ['auth', 'notkaryawan']], function () {
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');

    // index
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan');
    Route::get('/user', [UserController::class, 'index'])->name('user.index');

    // create
    Route::get('/create-user', [UserController::class, 'create_form'])->name('user.form');
    Route::post('/create-user', [UserController::class, 'create'])->name('user.create');
    Route::get('/create-karyawan', [KaryawanController::class, 'create_form'])->name('karyawan.form');
    Route::post('/create-karyawan', [KaryawanController::class, 'create'])->name('karyawan.create');

    // edit
    Route::get('/edit-karyawan/{id}', [KaryawanController::class, 'edit_form'])->name('karyawan.form.edit');
    Route::post('/edit-karyawan/{id}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::get('/edit-user/{id}', [UserController::class, 'edit_form'])->name('user.form.edit');
    Route::post('/edit-user/{id}', [UserController::class, 'update'])->name('user.update');



    // export
    Route::get('/export-karyawan', [KaryawanController::class, 'exportExcel'])->name('export.karyawan');
    Route::get('/export-rekap', [HomeController::class, 'exportExcel'])->name('export.rekap');
    Route::get('/export-kehadiran', [HomeController::class, 'exportExcelKehadiran'])->name('export.kehadiran');
    Route::get('/export-users', [UserController::class, 'exportUsers'])->name('export.users');

    //import
    Route::post('/import-users', [UserController::class, 'importUsers'])->name('import.users');

    // delete
    Route::delete('/delete-karyawan/{id}', [KaryawanController::class, 'delete'])->name('karyawan.delete');
    Route::delete('/delete-user/{id}', [UserController::class, 'delete'])->name('user.delete');
    
    Route::post('user/updateStatus/{id}', [UserController::class, 'updateStatus'])->name('user.updateStatus');

});


Route::get('/not-authorize', [HomeController::class, 'notauthorize'])->name('notauthorize');