<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController   ;
use App\Http\Controllers\ServiceController   ;

use App\Http\Controllers\ServiceDetailController   ;
use App\Http\Controllers\CustomerController   ;
use App\Http\Controllers\MaterialController   ;
use App\Http\Controllers\StockController   ;
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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('welcome');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('service-details', ServiceDetailController::class);
    Route::get('services/{id}', [ServiceController::class, 'show'])->name('services.show');
    Route::resource('customers', CustomerController::class);
    Route::resource('materials', MaterialController::class);
    Route::resource('stocks', StockController::class);
    Route::post('stocks/{stock}/add-material', [StockController::class, 'addMaterial'])->name('stocks.addMaterial');
    Route::post('stocks/{stock}/remove-material/{material}', [StockController::class, 'removeMaterial'])->name('stocks.removeMaterial');
    

});

require __DIR__.'/auth.php';
