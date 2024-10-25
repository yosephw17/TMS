<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ServiceController;

use App\Http\Controllers\ServiceDetailController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\DailyActivityController;
use App\Http\Controllers\ProformaController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\SellerController;
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
    Route::get('projects/show/{customer}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('projects/view/{project}', [ProjectController::class, 'showProject'])->name('projects.view');
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/add-materials', [ProjectController::class, 'addMaterials'])->name('projects.addMaterials');
    Route::put('/projects/{project}/materials/{material}', [ProjectController::class, 'updateMaterial'])->name('projectMaterials.update');
    Route::delete('/projects/{project}/materials/{material}', [ProjectController::class, 'destroyMaterial'])->name('projects.materials.destroy');
    Route::post('/users/{user}/add-to-team', [UserController::class, 'addToTeam'])->name('users.addToTeam');
    Route::resource('teams', TeamController::class);
    Route::post('/projects/{project}/upload-files', [ProjectController::class, 'uploadFiles'])->name('projects.uploadFiles');
    Route::resource('daily_activities', DailyActivityController::class);
    Route::resource('proformas', ProformaController::class);
    Route::get('/aluminiumProfile/print/{id}', [ProformaController::class, 'print'])->name('print.aluminiumProfile');
    Route::get('/aluminiumAccessories/print/{id}', [ProformaController::class, 'printAccessories'])->name('print.accessories');
    Route::resource('settings', SettingController::class);
    Route::resource('purchase_requests', PurchaseRequestController::class);
    Route::post('/purchase_requests/{id}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase_requests.approve');
Route::post('/purchase_requests/{id}/decline', [PurchaseRequestController::class, 'decline'])->name('purchase_requests.decline');
    Route::get('/api/stock/{stock}/materials', [StockController::class, 'getMaterials']);
    Route::resource('sellers', SellerController::class);

    
});

require __DIR__.'/auth.php';
