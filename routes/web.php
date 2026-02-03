<?php

use App\Models\RoadInventory;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DRPController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BalaiController;
use App\Http\Controllers\IslandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\AlignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KabupatenController;
use App\Http\Controllers\KecamatanController;
use App\Http\Controllers\LinkClassController;
use App\Http\Controllers\RuasJalanController;
use App\Http\Controllers\YearFilterController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\LinkKecamatanController;
use App\Http\Controllers\RoadConditionController;
use App\Http\Controllers\InventarisasiJalanController;

// --------------------
// Public Routes
// --------------------
Route::get('/', function () {
    return view('auth.login');
});

Route::get('/test', function () {
    return 'Laravel OK';
});

Route::get('/debug-compare', [App\Http\Controllers\DebugController::class, 'compareQueries']);


// --------------------
// Authenticated Routes
// --------------------
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
    });

    // --------------------
    // Ruas Jalan Routes
    // --------------------
    Route::prefix('ruas-jalan')->name('ruas-jalan.')->group(function () {
        Route::get('/data', [RuasJalanController::class, 'getData'])->name('data');
        
        Route::delete('/destroy-all', [RuasJalanController::class, 'destroyAll'])
            ->middleware('permission:delete,ruas_jalan')->name('destroyAll');

        Route::get('/', [RuasJalanController::class, 'index'])
            ->middleware('permission:read,ruas_jalan')->name('index');
        Route::get('/create', [RuasJalanController::class, 'create'])
            ->middleware('permission:add,ruas_jalan')->name('create');
        Route::post('/', [RuasJalanController::class, 'store'])
            ->middleware('permission:add,ruas_jalan')->name('store');
        
        // ✅ PERBAIKI: Parameter {id} bukan {ruas}
        Route::get('/{id}', [RuasJalanController::class, 'show'])
            ->middleware('permission:detail,ruas_jalan')->name('show');
        Route::get('/{id}/edit', [RuasJalanController::class, 'edit'])
            ->middleware('permission:update,ruas_jalan')->name('edit');
        Route::put('/{id}', [RuasJalanController::class, 'update'])
            ->middleware('permission:update,ruas_jalan')->name('update');
        Route::delete('/{id}', [RuasJalanController::class, 'destroy'])
            ->middleware('permission:delete,ruas_jalan')->name('destroy');

        Route::post('/generate-codes', [RuasJalanController::class, 'generateCodes'])->name('generateCodes');
    });

    // --------------------
    // DRP Routes
    // --------------------
    Route::prefix('drp')->name('drp.')->group(function () {
        // Hapus semua
        Route::delete('/destroy-all', [DRPController::class, 'destroyAll'])
            ->middleware('permission:delete,drp')->name('destroyAll');

        // AJAX Routes untuk data
        Route::get('/get-detail', [DRPController::class, 'getDetail'])->name('getDetail');
        Route::get('/get-kabupaten', [DRPController::class, 'getKabupaten'])->name('getKabupaten');
        Route::get('/get-links', [DRPController::class, 'getLinks'])->name('getLinks');
        
        // Route khusus untuk create page (hanya ruas yang belum punya DRP)
        Route::get('/get-links-for-create', [DRPController::class, 'getLinksForCreate'])->name('getLinksForCreate');
        
        // Route untuk cek apakah DRP sudah ada
        Route::get('/check-drp-exists', [DRPController::class, 'checkDRPExists'])->name('checkDRPExists');

        // CRUD Routes
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
    });

    // --------------------
    // Kelas Jalan Routes
    // --------------------
    Route::prefix('kelas-jalan')->name('kelas-jalan.')->group(function () {
        // Hapus semua
        Route::delete('/destroy-all', [LinkClassController::class, 'destroyAll'])
            ->middleware('permission:delete,kelas_jalan')->name('destroyAll');

        // CRUD - urutkan route yang spesifik dulu sebelum yang pakai parameter
        Route::get('/', [LinkClassController::class, 'index'])
            ->middleware('permission:read,kelas_jalan')->name('index');
        Route::get('/create', [LinkClassController::class, 'create'])
            ->middleware('permission:add,kelas_jalan')->name('create');
        Route::post('/', [LinkClassController::class, 'store'])
            ->middleware('permission:add,kelas_jalan')->name('store');
        Route::get('/detail', [LinkClassController::class, 'getDetail'])
            ->name('getDetail');
        Route::get('/show/{link_no}', [LinkClassController::class, 'show'])
            ->middleware('permission:read,kelas_jalan')->name('show');
        
        // Route dengan parameter di akhir agar tidak bentrok
        Route::get('/{linkclass}/edit', [LinkClassController::class, 'edit'])
            ->middleware('permission:update,kelas_jalan')->name('edit');
        Route::put('/{linkclass}', [LinkClassController::class, 'update'])
            ->middleware('permission:update,kelas_jalan')->name('update');
        Route::delete('/{linkclass}', [LinkClassController::class, 'destroy'])
            ->middleware('permission:delete,kelas_jalan')->name('destroy');
    });

    // --------------------
    // Ruas Jalan Kecamatan Routes
    // --------------------
    Route::prefix('ruas-jalan-kecamatan')->name('ruas-jalan-kecamatan.')->group(function () {
        // Hapus semua
        Route::delete('/destroy-all', [LinkKecamatanController::class, 'destroyAll'])
            ->middleware('permission:delete,ruas_jalan_kecamatan')->name('destroyAll');

        // CRUD - urutkan route yang spesifik dulu sebelum yang pakai parameter
        Route::get('/', [LinkKecamatanController::class, 'index'])
            ->middleware('permission:read,ruas_jalan_kecamatan')->name('index');
        Route::get('/create', [LinkKecamatanController::class, 'create'])
            ->middleware('permission:add,ruas_jalan_kecamatan')->name('create');
        Route::post('/', [LinkKecamatanController::class, 'store'])
            ->middleware('permission:add,ruas_jalan_kecamatan')->name('store');
        Route::get('/detail', [LinkKecamatanController::class, 'getDetail'])
            ->name('getDetail');
        Route::get('/show/{link_no}', [LinkKecamatanController::class, 'show'])
            ->middleware('permission:read,ruas_jalan_kecamatan')->name('show');
        
        // Route dengan parameter di akhir agar tidak bentrok
        Route::get('/{linkKecamatan}/edit', [LinkKecamatanController::class, 'edit'])
            ->middleware('permission:update,ruas_jalan_kecamatan')->name('edit');
        Route::put('/{linkKecamatan}', [LinkKecamatanController::class, 'update'])
            ->middleware('permission:update,ruas_jalan_kecamatan')->name('update');
        Route::delete('/{linkKecamatan}', [LinkKecamatanController::class, 'destroy'])
            ->middleware('permission:delete,ruas_jalan_kecamatan')->name('destroy');
    });

    // --------------------
    // Inventarisasi Jalan Routes
    // --------------------
    Route::prefix('inventarisasi-jalan')->name('inventarisasi-jalan.')->group(function () {
        // Hapus semua
        Route::delete('/destroy-all', [InventarisasiJalanController::class, 'destroyAll'])
            ->middleware('permission:delete,inventarisasi_jalan')->name('destroyAll');
        Route::get('/get-years', function () {
            $years = RoadInventory::select('year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year');
            return response()->json($years);
        });

        // Index
        Route::get('/', [InventarisasiJalanController::class, 'index'])
            ->middleware('permission:read,inventarisasi_jalan')->name('index');
        
        // Create
        Route::get('/create', [InventarisasiJalanController::class, 'create'])
            ->middleware('permission:add,inventarisasi_jalan')->name('create');
        
        // Store
        Route::post('/', [InventarisasiJalanController::class, 'store'])
            ->middleware('permission:add,inventarisasi_jalan')->name('store');
        
        // Get ruas detail
        Route::get('/get-ruas-detail/{linkNo}', [InventarisasiJalanController::class, 'getRuasDetail'])
            ->name('get-ruas-detail');
        
        // Get detail for table
        Route::get('/detail', [InventarisasiJalanController::class, 'getDetail'])
            ->name('getDetail');
        
        // ✅ EDIT - Harus sebelum show agar tidak bentrok
        Route::get('/edit/{link_no}', [InventarisasiJalanController::class, 'edit'])
            ->middleware('permission:update,inventarisasi_jalan')->name('edit');
        
        // ✅ UPDATE
        Route::put('/update/{link_no}', [InventarisasiJalanController::class, 'update'])
            ->middleware('permission:update,inventarisasi_jalan')->name('update');
        
        // Show - Di akhir agar tidak bentrok dengan route lain
        Route::get('/show/{link_no}', [InventarisasiJalanController::class, 'show'])
            ->middleware('permission:read,inventarisasi_jalan')->name('show');
        
        // ✅ DELETE (jika diperlukan untuk hapus per ruas)
        Route::delete('/delete/{link_no}', [InventarisasiJalanController::class, 'destroy'])
            ->middleware('permission:delete,inventarisasi_jalan')->name('destroy');
    });

    // --------------------
    // Kondisi Jalan Routes
    // --------------------
    Route::prefix('kondisi-jalan')->name('kondisi-jalan.')->group(function () {
        // Hapus semua
        Route::delete('/destroy-all', [RoadConditionController::class, 'destroyAll'])
            ->middleware('permission:delete,kondisi_jalan')->name('destroyAll');
        
        // CRUD
        Route::get('/', [RoadConditionController::class, 'index'])
            ->middleware('permission:read,kondisi_jalan')->name('index');
        Route::get('/create', [RoadConditionController::class, 'create'])
            ->middleware('permission:add,kondisi_jalan')->name('create');
        Route::post('/', [RoadConditionController::class, 'store'])
            ->middleware('permission:add,kondisi_jalan')->name('store');
        
        // AJAX Routes untuk form
        Route::get('/detail', [RoadConditionController::class, 'getDetail'])
            ->name('getDetail');
        Route::get('/get-years', [RoadConditionController::class, 'getYears'])
            ->name('getYears');
        Route::get('/get-ruas-by-year', [RoadConditionController::class, 'getRuasByYear'])
            ->name('getRuasByYear');
        Route::get('/get-chainage-by-ruas', [RoadConditionController::class, 'getChainageByRuas'])
            ->name('getChainageByRuas'); // ✅ ROUTE BARU
        Route::get('/segment-detail', [RoadConditionController::class, 'getSegmentDetail'])
            ->name('getSegmentDetail');
            // Di dalam group kondisi-jalan
        Route::get('/get-last-chainage', [RoadConditionController::class, 'getLastChainage'])
            ->name('getLastChainage');
        
        // Show dengan parameter year optional
        Route::get('/show/{link_no}/{year?}', [RoadConditionController::class, 'show'])
            ->middleware('permission:read,kondisi_jalan')->name('show');
        
        // Format: /kondisi-jalan/{link_no}/{chainage_from}/{chainage_to}/{year}/edit
        Route::get('/{link_no}/{chainage_from}/{chainage_to}/{year}/edit', [RoadConditionController::class, 'edit'])
            ->middleware('permission:update,kondisi_jalan')->name('edit');
        Route::put('/{link_no}/{chainage_from}/{chainage_to}/{year}', [RoadConditionController::class, 'update'])
            ->middleware('permission:update,kondisi_jalan')->name('update');
        Route::delete('/{link_no}/{chainage_from}/{chainage_to}/{year}', [RoadConditionController::class, 'destroy'])
            ->middleware('permission:delete,kondisi_jalan')->name('destroy');
    });

    // --------------------
    // Map Routes
    // --------------------

    // --------------------
    // Import Export Routes (Terpusat)
    // --------------------
    Route::prefix('import-export')->name('import_export.')->group(function () {
        Route::get('/', [ImportExportController::class, 'index'])
            ->middleware('permission:detail,import_export')->name('index');
        
        Route::post('/export', [ImportExportController::class, 'export'])
            ->name('export');
        
        Route::post('/import', [ImportExportController::class, 'import'])
            ->name('import');
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

    // View peta
    Route::get('/peta/alignment', [AlignmentController::class, 'showMap']);
    Route::prefix('api/alignment')->group(function () {
    
    // Get list kecamatan dengan jumlah segmen
    Route::get('/kecamatan-list', [AlignmentController::class, 'getKecamatanList']);
    
    // Get koordinat dengan SDI berdasarkan kecamatan
    Route::get('/coords-sdi-by-kecamatan', [AlignmentController::class, 'getCoordsWithSDIByKecamatan']);
    
    // Get semua koordinat dengan SDI (untuk semua kecamatan)
    Route::get('/coords-sdi', [AlignmentController::class, 'getCoordsWithSDI']);
    
    // Get koordinat saja (tanpa SDI)
    Route::get('/coords', [AlignmentController::class, 'getCoords']);
    
    // Get list tahun yang tersedia
    Route::get('/available-years', [AlignmentController::class, 'getAvailableYears']);
});

    Route::prefix('year-filter')->name('year.filter.')->group(function () {
        Route::get('/available', [YearFilterController::class, 'getAvailableYears'])->name('available');
        Route::get('/current', [YearFilterController::class, 'getCurrentYear'])->name('current');
        Route::post('/set', [YearFilterController::class, 'setYear'])->name('set');
    });
});

// --------------------
// Dashboard (Superadmin Only)
// --------------------
// Route::middleware(['auth', 'role:superadmin'])->get('/dashboard', [DashboardController::class, 'index'])
//     ->name('dashboard');

require __DIR__.'/auth.php';
