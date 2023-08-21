<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
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

Route::group(['middleware' => ['auth', 'notsiswa']], function () {
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');

    // index
    Route::get('/siswa', [StudentController::class, 'index'])->name('siswa');
    Route::get('/user', [UserController::class, 'index'])->name('user.index');

    // create
    Route::get('/create-user', [UserController::class, 'create_form'])->name('user.form');
    Route::post('/create-user', [UserController::class, 'create'])->name('user.create');
    Route::get('/create-siswa', [StudentController::class, 'create_form'])->name('siswa.form');
    Route::post('/create-siswa', [StudentController::class, 'create'])->name('siswa.create');

    // edit
    Route::get('/edit-siswa/{id}', [StudentController::class, 'edit_form'])->name('siswa.form.edit');
    Route::post('/edit-siswa/{id}', [StudentController::class, 'update'])->name('siswa.update');

    // export
    Route::get('/export-siswa', [StudentController::class, 'exportExcel'])->name('export.siswa');
    Route::get('/export-rekap', [HomeController::class, 'exportExcel'])->name('export.rekap');
    Route::get('/export-kehadiran', [HomeController::class, 'exportExcelKehadiran'])->name('export.kehadiran');

    // delete
    Route::delete('/delete-siswa/{id}', [StudentController::class, 'delete'])->name('siswa.delete');

    
    
});


Route::get('/not-authorize', [HomeController::class, 'notauthorize'])->name('notauthorize');