<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\SuperAdminLoginController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

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
    Route::get('/predictions/analytics', [PredictionController::class, 'analytics'])->name('predictions.analytics');
    Route::get('/predictions/fix-data', [PredictionController::class, 'fixData'])->name('predictions.fix-data');
    Route::get('/predictions/debug-data', [PredictionController::class, 'debugData'])->name('predictions.debug-data');
    Route::get('/predictions/debug-auth', [PredictionController::class, 'debugAuth'])->name('predictions.debug-auth');
    Route::get('/predictions/debug-ownership/{predictionId}', [PredictionController::class, 'debugPredictionOwnership'])->name('predictions.debug-ownership');
    Route::get('/predictions/api/test', [PredictionController::class, 'testApi'])->name('predictions.test-api');
    Route::get('/predictions/simple-test', [PredictionController::class, 'simpleTest'])->name('predictions.simple-test');
    Route::get('/predictions/debug-auth', [PredictionController::class, 'debugAuth'])->name('predictions.debug-auth');
    Route::get('/predictions/test-delete/{prediction}', [PredictionController::class, 'testDelete'])->name('predictions.test-delete');
    Route::post('/predictions/validate-urls', [PredictionController::class, 'validateUrls'])->name('predictions.validate-urls');
    Route::get('/predictions/test-url-validation', [PredictionController::class, 'testUrlValidation'])->name('predictions.test-url-validation');
    
    // Parameterized routes must come LAST
    Route::get('/predictions/{prediction}', [PredictionController::class, 'show'])->name('predictions.show');
    Route::get('/predictions/{prediction}/model-info', [PredictionController::class, 'showModelInfo'])->name('predictions.model-info');
    Route::get('/predictions/{prediction}/export', [PredictionController::class, 'export'])->name('predictions.export');
    Route::delete('/predictions/{prediction}', [PredictionController::class, 'destroy'])->name('predictions.destroy');
});

// Admin and Superadmin Login Routes (no auth required)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login']);
    
    Route::get('/superadmin/login', [SuperAdminLoginController::class, 'showLoginForm'])->name('superadmin.login');
    Route::post('/superadmin/login', [SuperAdminLoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
    Route::post('/superadmin/logout', [SuperAdminLoginController::class, 'logout'])->name('superadmin.logout');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    Route::get('/predictions', [AdminController::class, 'predictions'])->name('predictions.index');
    Route::get('/predictions/{prediction}', [AdminController::class, 'showPrediction'])->name('predictions.show');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    
    // Analytics routes
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [App\Http\Controllers\Admin\AnalyticsController::class, 'getData'])->name('analytics.data');
    Route::get('/analytics/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('analytics.export');
    Route::get('/analytics/user/{userId}', [App\Http\Controllers\Admin\AnalyticsController::class, 'getUserAnalytics'])->name('analytics.user');
});

// Superadmin routes
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
    Route::get('/admins', [SuperAdminController::class, 'admins'])->name('admins.index');
    Route::post('/admins', [SuperAdminController::class, 'storeAdmin'])->name('admins.store');
    Route::get('/admins/{user}', [SuperAdminController::class, 'showAdmin'])->name('admins.show');
    Route::patch('/admins/{user}', [SuperAdminController::class, 'updateAdmin'])->name('admins.update');
    Route::delete('/admins/{user}', [SuperAdminController::class, 'deleteAdmin'])->name('admins.destroy');
    Route::patch('/admins/{user}/role', [SuperAdminController::class, 'updateAdminRole'])->name('admins.update-role');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
    Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [SuperAdminController::class, 'showUser'])->name('users.show');
    Route::patch('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [SuperAdminController::class, 'deleteUser'])->name('users.destroy');
    Route::get('/predictions', [SuperAdminController::class, 'predictions'])->name('predictions.index');
    Route::get('/predictions/{prediction}', [SuperAdminController::class, 'showPrediction'])->name('predictions.show');
    Route::get('/logs', [SuperAdminController::class, 'logs'])->name('logs');
    Route::get('/system-health', [SuperAdminController::class, 'getSystemHealth'])->name('system-health');
    Route::put('/profile', [SuperAdminController::class, 'updateProfile'])->name('profile.update');
});

require __DIR__.'/auth.php';
