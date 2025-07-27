<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TipperController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\Api\TripController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/trips/{id}/pdf', [TripController::class, 'exportTripPDF'])->name('trips.pdf');
Route::get('/batches/{batchId}/pdf', [TripController::class, 'exportBatchPDF'])->name('batches.pdf');
Route::get('/trips/reports/preview', [TripController::class, 'previewReportPDF'])->name('trips.reports.preview');
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Specific routes first
    Route::get('/tippers/expiring', [TipperController::class, 'getExpiringLicenses'])->name('tippers.expiring');
    Route::get('/tippers/create', [TipperController::class, 'create'])->name('tippers.create');
    Route::get('/tippers', [TipperController::class, 'index'])->name('tippers.index');

    // POST route for storing new tippers
    Route::post('/tippers', [TipperController::class, 'store'])->name('tippers.store');

    // Parameterized routes last
    Route::get('/tippers/{tipper_number}', [TipperController::class, 'show'])->name('tippers.show');
    Route::get('/tippers/{tipper_number}/edit', [TipperController::class, 'edit'])->name('tippers.edit');
    Route::put('/tippers/{tipper_number}', [TipperController::class, 'update'])->name('tippers.update');
    Route::patch('/tippers/{tipper_number}', [TipperController::class, 'update'])->name('tippers.patch');
    Route::delete('/tippers/{tipper_number}', [TipperController::class, 'destroy'])->name('tippers.destroy');

    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
    Route::get('/drivers/create', [DriverController::class, 'create'])->name('drivers.create');
    Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
    Route::get('/drivers/{driver}', [DriverController::class, 'show'])->name('drivers.show');
    Route::get('/drivers/{driver}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
    Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');
    Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');

    // Plant CRUD routes
    Route::get('/plants', [PlantController::class, 'index'])->name('plants.index');
    Route::get('/plants/create', [PlantController::class, 'create'])->name('plants.create');
    Route::post('/plants', [PlantController::class, 'store'])->name('plants.store');
    Route::get('/plants/{id}', [PlantController::class, 'show'])->name('plants.show');
    Route::get('/plants/{id}/edit', [PlantController::class, 'edit'])->name('plants.edit');
    Route::put('/plants/{id}', [PlantController::class, 'update'])->name('plants.update');
    Route::delete('/plants/{id}', [PlantController::class, 'destroy'])->name('plants.destroy');

    // Trip CRUD routes - FIXED: Use only one approach
    Route::prefix('trips')->group(function () {
        Route::get('/reports', [TripController::class, 'reports'])->name('trips.reports');
        Route::get('/', [TripController::class, 'index'])->name('trips.index');
        Route::get('/create', [TripController::class, 'create'])->name('trips.create');
        Route::post('/', [TripController::class, 'store'])->name('trips.store');
        Route::get('/{id}', [TripController::class, 'show'])->name('trips.show'); // FIXED: lowercase 'show'
        Route::get('/{id}/edit', [TripController::class, 'edit'])->name('trips.edit');
        Route::put('/{id}', [TripController::class, 'update'])->name('trips.update');
        Route::delete('/{id}', [TripController::class, 'destroy'])->name('trips.destroy');
    });

    // REMOVED: Duplicate resource route definition
});

require __DIR__ . '/auth.php';
