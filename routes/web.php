<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChartController;
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
use App\Http\Controllers\ProformaImageController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ProformaWorkController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectAgreementController; // Added this line
use Illuminate\Support\Facades\Artisan;

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

Route::get('/run-migrations', function () {
    Artisan::call('migrate', ["--force" => true]);
    return "Migrations executed!";
});
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/chart-data', [ChartController::class, 'getMonthlyRegistrations']);
    Route::get('/customers-chart-data', [ChartController::class, 'getCustomers']);
    Route::get('/projects-chart-data', [ChartController::class, 'getChartData'])->middleware('auth');

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
    Route::post('stocks/{stock}/use-material/{material}/{pivot}', [StockController::class, 'updateMaterialQuantity'])->name('stocks.useMaterial');
    Route::get('stocks/{stock}/print-reference', [StockController::class, 'printByReference'])->name('stocks.printReference');
    Route::get('stocks/{stock}/print-all', [StockController::class, 'printStock'])->name('stocks.printAll');
    Route::get('stocks/{stock}/print-active', [StockController::class, 'printActiveStock'])->name('stocks.printActive');
    Route::get('stocks/{stock}/materials', [StockController::class, 'getMaterials'])->name('stocks.getMaterials');
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
    Route::get('/work/print/{id}', [ProformaController::class, 'printWork'])->name('print.work');
    Route::resource('settings', SettingController::class);
    Route::resource('purchase_requests', PurchaseRequestController::class);
    Route::post('/purchase_requests/{id}/approve', [PurchaseRequestController::class, 'approve'])->name('purchase_requests.approve');
    Route::post('/purchase_requests/{id}/decline', [PurchaseRequestController::class, 'decline'])->name('purchase_requests.decline');
    Route::get('/api/stock/{stock}/materials', [StockController::class, 'getMaterials']);
    Route::resource('sellers', SellerController::class);
    Route::get('/proforma_images/{seller_id}', [ProformaImageController::class, 'index'])->name('proforma_images.index');
    Route::resource('proforma_images', ProformaImageController::class)->except(['index']);
    Route::post('proforma_images/{id}/approve', [ProformaImageController::class, 'approve'])->name('proforma_images.approve');
    Route::post('proforma_images/{id}/decline', [ProformaImageController::class, 'decline'])->name('proforma_images.decline');
    Route::get('/projects/{project}/materials/print', [ProjectController::class, 'printMaterials'])->name('projects.materials.print');
    Route::resource('proforma_work', ProformaWorkController::class);
    Route::resource('frontends', FrontendController::class);
    Route::get('frontnds/delete/{id}', [FrontendController::class, 'destroy'])->name('frontends.delete');

    // Notification Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/notifications', [NotificationController::class, 'indexPage'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.delete');
        Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    });

    // API Routes for AJAX calls
    Route::middleware(['auth'])->prefix('api')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/dropdown', [NotificationController::class, 'dropdown']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    });
});

require __DIR__.'/auth.php';
