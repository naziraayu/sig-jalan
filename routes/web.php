<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DRPController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BalaiController;
use App\Http\Controllers\IslandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KabupatenController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\RuasJalanController;
use App\Http\Controllers\InventarisasiJalanController;

// --------------------
// Public Routes
// --------------------
Route::get('/', function () {
    return view('auth.login');
});

// --------------------
// Authenticated Routes
// --------------------
Route::middleware(['auth'])->group(function () {

    // --------------------
    // Users Routes
    // --------------------
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:detail,user')->name('index');
        Route::get('/create', [UserController::class, 'create'])
            ->middleware('permission:add,user')->name('create');
        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:add,user')->name('store');
        Route::get('/{user}', [UserController::class, 'show'])
            ->middleware('permission:detail,user')->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:update,user')->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])
            ->middleware('permission:update,user')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:delete,user')->name('destroy');
    });

    // --------------------
    // Roles Routes
    // --------------------
    Route::resource('roles', RoleController::class);

    // --------------------
    // Province Routes
    // --------------------
    Route::prefix('provinces')->name('provinces.')->group(function () {
        Route::delete('/destroy-all', [ProvinceController::class, 'destroyAll'])
        ->name('destroyAll');

        Route::get('/', [ProvinceController::class, 'index'])
            ->middleware('permission:read,provinsi')->name('index');
        Route::get('/create', [ProvinceController::class, 'create'])
            ->middleware('permission:add,provinsi')->name('create');
        Route::post('/', [ProvinceController::class, 'store'])
            ->middleware('permission:add,provinsi')->name('store');
        Route::get('/{province}', [ProvinceController::class, 'show'])
            ->middleware('permission:read,provinsi')->name('show');
        Route::get('/{province}/edit', [ProvinceController::class, 'edit'])
            ->middleware('permission:update,provinsi')->name('edit');
        Route::put('/{province}', [ProvinceController::class, 'update'])
            ->middleware('permission:update,provinsi')->name('update');
        Route::delete('/{province}', [ProvinceController::class, 'destroy'])
            ->middleware('permission:delete,provinsi')->name('destroy');

        // Import & Export
        Route::post('/import', [ProvinceController::class, 'import'])
            ->middleware('permission:import,provinsi')->name('import');
        Route::get('/export', [ProvinceController::class, 'export'])
            ->middleware('permission:export,provinsi')->name('export');
    });

    // --------------------
    // Balai Routes
    // --------------------
    Route::prefix('balai')->name('balai.')->group(function () {
        Route::delete('/destroy-all', [BalaiController::class, 'destroyAll'])
        ->middleware('permission:delete,balai')->name('destroyAll');

        // Resource Balai
        Route::resource('balai', BalaiController::class);

        Route::get('/', [BalaiController::class, 'index'])
            ->middleware('permission:read,balai')->name('index');
        Route::get('/create', [BalaiController::class, 'create'])
            ->middleware('permission:add,balai')->name('create');
        Route::post('/', [BalaiController::class, 'store'])
            ->middleware('permission:add,balai')->name('store');
        Route::get('/{balai}/edit', [BalaiController::class, 'edit'])
            ->middleware('permission:update,balai')->name('edit');
        Route::put('/{balai}', [BalaiController::class, 'update'])
            ->middleware('permission:update,balai')->name('update');
        Route::delete('/{balai}', [BalaiController::class, 'destroy'])
            ->middleware('permission:delete,balai')->name('destroy');

        // Import & Export
        Route::post('/import', [BalaiController::class, 'import'])
            ->middleware('permission:import,balai')->name('import');
        Route::get('/export', [BalaiController::class, 'export'])
            ->middleware('permission:export,balai')->name('export');
    });

    // --------------------
    // Island Routes
    // --------------------
    Route::prefix('island')->name('island.')->group(function () {
        Route::delete('/destroy-all', [IslandController::class, 'destroyAll'])
            ->middleware('permission:delete,pulau')->name('destroyAll');

        Route::get('/', [IslandController::class, 'index'])
            ->middleware('permission:read,pulau')->name('index');
        Route::get('/create', [IslandController::class, 'create'])
            ->middleware('permission:add,pulau')->name('create');
        Route::post('/', [IslandController::class, 'store'])
            ->middleware('permission:add,pulau')->name('store');
        Route::get('/{island}/edit', [IslandController::class, 'edit'])
            ->middleware('permission:update,pulau')->name('edit');
        Route::put('/{island}', [IslandController::class, 'update'])
            ->middleware('permission:update,pulau')->name('update');
        Route::delete('/{island}', [IslandController::class, 'destroy'])
            ->middleware('permission:delete,pulau')->name('destroy');

        // Import & Export
        Route::post('/import', [IslandController::class, 'import'])
            ->middleware('permission:import,pulau')->name('import');
        Route::get('/export', [IslandController::class, 'export'])
            ->middleware('permission:export,pulau')->name('export');
    });

    // --------------------
    // Kabupaten Routes
    // --------------------
    Route::prefix('kabupaten')->name('kabupaten.')->group(function () {
        Route::delete('/destroy-all', [KabupatenController::class, 'destroyAll'])
            ->name('destroyAll');

        Route::get('/', [KabupatenController::class, 'index'])
            ->middleware('permission:read,kabupaten')->name('index');
        Route::get('/create', [KabupatenController::class, 'create'])
            ->middleware('permission:add,kabupaten')->name('create');
        Route::post('/', [KabupatenController::class, 'store'])
            ->middleware('permission:add,kabupaten')->name('store');
        Route::get('/{kabupaten}/edit', [KabupatenController::class, 'edit'])
            ->middleware('permission:update,kabupaten')->name('edit');
        Route::put('/{kabupaten}', [KabupatenController::class, 'update'])
            ->middleware('permission:update,kabupaten')->name('update');
        Route::delete('/{kabupaten}', [KabupatenController::class, 'destroy'])
            ->middleware('permission:delete,kabupaten')->name('destroy');

        // Import & Export
        Route::post('/import', [KabupatenController::class, 'import'])
            ->middleware('permission:import,kabupaten')->name('import');
        Route::get('/export', [KabupatenController::class, 'export'])
            ->middleware('permission:export,kabupaten')->name('export');
    });

    // --------------------
    // Kecamatan Routes
    // --------------------
    Route::prefix('kecamatan')->name('kecamatan.')->group(function () {
        Route::delete('/destroy-all', [KecamatanController::class, 'destroyAll'])
            ->name('destroyAll');

        Route::get('/', [KecamatanController::class, 'index'])
            ->middleware('permission:read,kecamatan')->name('index');
        Route::get('/create', [KecamatanController::class, 'create'])
            ->middleware('permission:add,kecamatan')->name('create');
        Route::post('/', [KecamatanController::class, 'store'])
            ->middleware('permission:add,kecamatan')->name('store');
        Route::get('/{kecamatan}/edit', [KecamatanController::class, 'edit'])
            ->middleware('permission:update,kecamatan')->name('edit');
        Route::put('/{kecamatan}', [KecamatanController::class, 'update'])
            ->middleware('permission:update,kecamatan')->name('update');
        Route::delete('/{kecamatan}', [KecamatanController::class, 'destroy'])
            ->middleware('permission:delete,kecamatan')->name('destroy');

        // Import & Export
        Route::post('/import', [KecamatanController::class, 'import'])
            ->middleware('permission:import,kecamatan')->name('import');
        Route::get('/export', [KecamatanController::class, 'export'])
            ->middleware('permission:export,kecamatan')->name('export');
    });

    // --------------------
    // Ruas Jalan Routes
    // --------------------
    Route::prefix('ruas-jalan')->name('ruas-jalan.')->group(function () {
        // Hapus semua
        Route::delete('/destroy-all', [RuasJalanController::class, 'destroyAll'])
            ->middleware('permission:delete,ruas_jalan')->name('destroyAll');

        // CRUD
        Route::get('/', [RuasJalanController::class, 'index'])
            ->middleware('permission:read,ruas_jalan')->name('index');
        Route::get('/create', [RuasJalanController::class, 'create'])
            ->middleware('permission:add,ruas_jalan')->name('create');
        Route::post('/', [RuasJalanController::class, 'store'])
            ->middleware('permission:add,ruas_jalan')->name('store');
        Route::get('/{ruas}', [RuasJalanController::class, 'show'])
            ->middleware('permission:detail,ruas_jalan')->name('show');
        Route::get('/{ruas}/edit', [RuasJalanController::class, 'edit'])
            ->middleware('permission:update,ruas_jalan')->name('edit');
        Route::put('/{ruas}', [RuasJalanController::class, 'update'])
            ->middleware('permission:update,ruas_jalan')->name('update');
        Route::delete('/{ruas}', [RuasJalanController::class, 'destroy'])
            ->middleware('permission:delete,ruas_jalan')->name('destroy');

        // Import & Export
        Route::post('/import', [RuasJalanController::class, 'import'])
            ->middleware('permission:import,ruas_jalan')->name('import');
        Route::get('/export', [RuasJalanController::class, 'export'])
            ->middleware('permission:export,ruas_jalan')->name('export');
    });

    // --------------------
    // DRP Routes
    // --------------------
    Route::prefix('drp')->name('drp.')->group(function () {
        // Hapus semua
        Route::delete('/destroy-all', [DRPController::class, 'destroyAll'])
            ->middleware('permission:delete,drp')->name('destroyAll');

        // CRUD
        Route::get('/', [DRPController::class, 'index'])
            ->middleware('permission:read,drp')->name('index');
        Route::get('/create', [DRPController::class, 'create'])
            ->middleware('permission:add,drp')->name('create');
        Route::post('/', [DRPController::class, 'store'])
            ->middleware('permission:add,drp')->name('store');
        Route::get('/{drp}/edit', [DRPController::class, 'edit'])
            ->middleware('permission:update,drp')->name('edit');
        Route::put('/{drp}', [DRPController::class, 'update'])
            ->middleware('permission:update,drp')->name('update');
        Route::delete('/{drp}', [DRPController::class, 'destroy'])
            ->middleware('permission:delete,drp')->name('destroy');

        // Import & Export
        Route::post('/import', [DRPController::class, 'import'])
            ->middleware('permission:import,drp')->name('import');
        Route::get('/export', [DRPController::class, 'export'])
            ->middleware('permission:export,drp')->name('export');

        // Custom: ambil DRP berdasarkan link_no (buat dropdown dinamis)
        Route::get('/by-link/{link_no}', [DRPController::class, 'getDrpByLink'])
            ->name('byLink');
    });

// --------------------
// Inventarisasi Jalan Routes
// --------------------
Route::prefix('inventarisasi-jalan')->name('inventarisasi-jalan.')->group(function () {
    // Hapus semua
    Route::delete('/destroy-all', [InventarisasiJalanController::class, 'destroyAll'])
        ->middleware('permission:delete,inventarisasi_jalan')->name('destroyAll');

    // CRUD - urutkan route yang spesifik dulu sebelum yang pakai parameter
    Route::get('/', [InventarisasiJalanController::class, 'index'])
        ->middleware('permission:read,inventarisasi_jalan')->name('index');
    Route::get('/create', [InventarisasiJalanController::class, 'create'])
        ->middleware('permission:add,inventarisasi_jalan')->name('create');
    Route::post('/', [InventarisasiJalanController::class, 'store'])
        ->middleware('permission:add,inventarisasi_jalan')->name('store');
    Route::get('/detail', [InventarisasiJalanController::class, 'getDetail'])
        ->name('getDetail');
    Route::get('/show/{link_no}', [InventarisasiJalanController::class, 'show'])
        ->middleware('permission:read,inventarisasi_jalan')->name('show');
    
    // Import & Export
    Route::post('/import', [InventarisasiJalanController::class, 'import'])
        ->middleware('permission:import,inventarisasi_jalan')->name('import');
    Route::get('/export', [InventarisasiJalanController::class, 'export'])
        ->middleware('permission:export,inventarisasi_jalan')->name('export');
    
    // Route dengan parameter di akhir agar tidak bentrok
    Route::get('/{inventarisasi}/edit', [InventarisasiJalanController::class, 'edit'])
        ->middleware('permission:update,inventarisasi_jalan')->name('edit');
    Route::put('/{inventarisasi}', [InventarisasiJalanController::class, 'update'])
        ->middleware('permission:update,inventarisasi_jalan')->name('update');
    Route::delete('/{inventarisasi}', [InventarisasiJalanController::class, 'destroy'])
        ->middleware('permission:delete,inventarisasi_jalan')->name('destroy');
});
    // --------------------
    // Profile Routes
    // --------------------
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        Route::put('/', [ProfileController::class, 'updatePhoto'])->name('update.photo');
    });

});

// --------------------
// Dashboard (Superadmin Only)
// --------------------
Route::middleware(['auth', 'role:superadmin'])->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

require __DIR__.'/auth.php';
