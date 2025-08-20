<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictionController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Simple health check route (no auth required)
Route::get('/health', function() {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'message' => 'System is running'
    ]);
});

// Responsive design test page (no auth required)
Route::get('/responsive-test', function () {
    return view('responsive-test');
})->name('responsive-test');

// Authentication routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Prediction routes - ALL specific routes must come BEFORE parameterized routes
    Route::get('/predictions', [PredictionController::class, 'index'])->name('predictions.index');
    Route::get('/predictions/create', [PredictionController::class, 'create'])->name('predictions.create');
    Route::post('/predictions', [PredictionController::class, 'store'])->name('predictions.store');
    Route::get('/predictions/history', [PredictionController::class, 'history'])->name('predictions.history');
    Route::get('/predictions/fix-data', [PredictionController::class, 'fixData'])->name('predictions.fix-data');
    Route::get('/predictions/debug-data', [PredictionController::class, 'debugData'])->name('predictions.debug-data');
    Route::get('/predictions/debug-auth', [PredictionController::class, 'debugAuth'])->name('predictions.debug-auth');
    Route::get('/predictions/debug-ownership/{predictionId}', [PredictionController::class, 'debugPredictionOwnership'])->name('predictions.debug-ownership');
    Route::get('/predictions/api/test', [PredictionController::class, 'testApi'])->name('predictions.test-api');
    Route::get('/predictions/simple-test', [PredictionController::class, 'simpleTest'])->name('predictions.simple-test');
    
    // Parameterized routes must come LAST
    Route::get('/predictions/{prediction}', [PredictionController::class, 'show'])->name('predictions.show');
    Route::get('/predictions/{prediction}/model-info', [PredictionController::class, 'showModelInfo'])->name('predictions.model-info');
    Route::get('/predictions/{prediction}/export', [PredictionController::class, 'export'])->name('predictions.export');
});

require __DIR__.'/auth.php';
