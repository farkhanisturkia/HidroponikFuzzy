<?php

use App\Models\Data;
use App\Models\Hidroponik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PpmController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\FuzzyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HidroponikController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('splade')->group(function () {
    // Registers routes to support the interactive components...
    Route::spladeWithVueBridge();

    // Registers routes to support password confirmation in Form and Link components...
    Route::spladePasswordConfirmation();

    // Registers routes to support Table Bulk Actions and Exports...
    Route::spladeTable();

    // Registers routes to support async File Uploads with Filepond...
    Route::spladeUploads();

    Route::get('/', function () {
        return view('welcome');
    })->name('wellcome');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', function () {

            $hidroponik = Hidroponik::where('user_id', Auth::id())->get();
            
            $hidroponiks = [];
            $hidroponiks_id = [];
            foreach ($hidroponik as $hidroponik) {
                if ($hidroponik) {
                    $hidroponiks[]      = $hidroponik->code;
                    $hidroponiks_id[]   = $hidroponik->id;
                }
            }
            // dd($hidroponiks, $hidroponiks_id);
            
            $jumlahs =[];
            $ppms =[];
            foreach ($hidroponiks_id as $hidroponiks_id) {
                if ($hidroponiks_id) {
                    $datas = Data::select('jumlah','ppm')->where('hidroponik_id', $hidroponiks_id)->orderBy('id', 'desc')->first();
                    if ($datas) {
                        $jumlahs[]  = $datas->jumlah;
                        $ppms[]     = $datas->ppm;
                    }else {
                        $jumlahs[] = '';
                        $ppms[] = '';
                    }
                }
            }
            // dd($jumlahs, $ppms);

            return view('dashboard',[
                'hidroponiks'   => $hidroponiks,
                'ppms'          => $ppms,
                'jumlahs'       => $jumlahs
            ]);
        })->middleware(['verified'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('data')->name('data.')->group(function () {
        Route::get('/{hidroponik}', [DataController::class, 'index'])->name('index');
        Route::get('new/create', [DataController::class, 'create'])->name('create');
        Route::post('/', [DataController::class, 'store'])->name('store');
        // Route::post('/{data}', [DataController::class, 'show'])->name('show');
        Route::get('/edit/{data}', [DataController::class, 'edit'])->name('edit');
        Route::put('/{data}', [DataController::class, 'update'])->name('update');
        Route::delete('/{data}', [DataController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('fuzzy')->name('fuzzy.')->group(function (){
        Route::get('/{data}', [FuzzyController::class, 'show'])->name('show')->middleware('auth');
    });

    Route::resource('hidroponik', HidroponikController::class)->middleware('auth');
    Route::resource('ppm', PpmController::class)->middleware('auth');

    require __DIR__.'/auth.php';
});
