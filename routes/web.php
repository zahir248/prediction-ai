<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\DataAnalysisController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\SuperAdminLoginController;

Route::get('/', function () {
    return view('dashboard');
})->name('home');

// Simple health check route (no auth required)
Route::get('/health', function() {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'message' => 'System is running'
    ]);
});

// Chatbot route (no auth required for public access)
Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat');

// Responsive design test page (no auth required)
Route::get('/responsive-test', function () {
    return view('responsive-test');
})->name('responsive-test');

// Documentation download route (no auth required)
Route::get('/documentation', [DocumentationController::class, 'download'])->name('documentation.download');

// Analyze pages (accessible without auth, but buttons disabled if not logged in)
Route::get('/predictions/create', [PredictionController::class, 'create'])->name('predictions.create');
Route::get('/social-media', [SocialMediaController::class, 'index'])->name('social-media.index');
Route::get('/data-analysis', [DataAnalysisController::class, 'index'])->name('data-analysis.index');

// Authentication routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/image', [ProfileController::class, 'removeImage'])->name('profile.image.remove');
    
    // Profile image route (serves images through Laravel to avoid 403 errors in cPanel)
    Route::get('/profile-image/{filename}', function ($filename) {
        // Security: Only allow image files
        if (!preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|gif)$/i', $filename)) {
            abort(404);
        }
        
        $path = storage_path('app/public/profile-images/' . $filename);
        
        if (!file_exists($path)) {
            abort(404);
        }
        
        $file = file_get_contents($path);
        $type = mime_content_type($path);
        
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Cache-Control', 'public, max-age=31536000');
    })->name('profile.image');
    
    // Default profile image route (generates avatar with initials)
    Route::get('/default-avatar/{initials}', function ($initials) {
        // Limit to 2 characters and sanitize
        $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $initials), 0, 2));
        if (empty($initials)) {
            $initials = 'U';
        }
        
        // Create SVG avatar
        $size = 200;
        $svg = '<svg width="' . $size . '" height="' . $size . '" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                </linearGradient>
            </defs>
            <rect width="' . $size . '" height="' . $size . '" fill="url(#grad)" rx="12"/>
            <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="' . ($size * 0.4) . '" font-weight="700" fill="white" text-anchor="middle" dominant-baseline="central">' . htmlspecialchars($initials) . '</text>
        </svg>';
        
        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=31536000');
    })->name('profile.default.avatar');
    
    // Prediction routes - ALL specific routes must come BEFORE parameterized routes
    Route::get('/predictions', [PredictionController::class, 'index'])->name('predictions.index');
    Route::post('/predictions', [PredictionController::class, 'store'])->name('predictions.store');
    Route::get('/predictions/history', [PredictionController::class, 'history'])->name('predictions.history');
    Route::get('/analytics', [PredictionController::class, 'analytics'])->name('analytics');
    Route::get('/predictions/fix-data', [PredictionController::class, 'fixData'])->name('predictions.fix-data');
    Route::get('/predictions/debug-data', [PredictionController::class, 'debugData'])->name('predictions.debug-data');
    Route::get('/predictions/debug-auth', [PredictionController::class, 'debugAuth'])->name('predictions.debug-auth');
    Route::get('/predictions/debug-ownership/{predictionId}', [PredictionController::class, 'debugPredictionOwnership'])->name('predictions.debug-ownership');
    Route::get('/predictions/api/test', [PredictionController::class, 'testApi'])->name('predictions.test-api');
    Route::get('/predictions/simple-test', [PredictionController::class, 'simpleTest'])->name('predictions.simple-test');
    Route::get('/predictions/debug-auth', [PredictionController::class, 'debugAuth'])->name('predictions.debug-auth');
    Route::get('/predictions/test-delete/{prediction}', [PredictionController::class, 'testDelete'])->name('predictions.test-delete');
    Route::post('/predictions/validate-urls', [PredictionController::class, 'validateUrls'])->name('predictions.validate-urls');
    Route::get('/test-ai-providers', function() {
        $results = [];
        
        // Test Gemini
        try {
            $geminiService = app(\App\Services\GeminiService::class);
            $geminiResult = $geminiService->testConnection();
            $results['gemini'] = $geminiResult;
        } catch (\Exception $e) {
            $results['gemini'] = ['success' => false, 'message' => $e->getMessage()];
        }
        
        // Test ChatGPT
        try {
            $chatgptService = app(\App\Services\ChatGPTService::class);
            $chatgptResult = $chatgptService->testConnection();
            $results['chatgpt'] = $chatgptResult;
        } catch (\Exception $e) {
            $results['chatgpt'] = ['success' => false, 'message' => $e->getMessage()];
        }
        
        // Test AI Service Factory
        try {
            $aiService = \App\Services\AIServiceFactory::create();
            $factoryResult = $aiService->testConnection();
            $results['factory'] = $factoryResult;
            $results['current_provider'] = \App\Services\AIServiceFactory::getCurrentProvider();
        } catch (\Exception $e) {
            $results['factory'] = ['success' => false, 'message' => $e->getMessage()];
        }
        
        return response()->json($results);
    })->name('test-ai-providers');
    Route::get('/predictions/test-url-validation', [PredictionController::class, 'testUrlValidation'])->name('predictions.test-url-validation');
    
    // Social Media Analysis routes
    Route::get('/social-media/history', [SocialMediaController::class, 'history'])->name('social-media.history');
    Route::get('/social-media/{socialMediaAnalysis}', [SocialMediaController::class, 'show'])->name('social-media.show');
    Route::get('/social-media/{socialMediaAnalysis}/export', [SocialMediaController::class, 'export'])->name('social-media.export');
    Route::get('/social-media/{socialMediaAnalysis}/analysis-html', [SocialMediaController::class, 'getAnalysisHtml'])->name('social-media.analysis-html');
    Route::delete('/social-media/{socialMediaAnalysis}', [SocialMediaController::class, 'destroy'])->name('social-media.destroy');
    Route::post('/social-media/analyze', [SocialMediaController::class, 'analyze'])->name('social-media.analyze');
    Route::post('/social-media/search-all', [SocialMediaController::class, 'searchAll'])->name('social-media.search-all');
    Route::post('/social-media/get-existing-data', [SocialMediaController::class, 'getExistingPlatformData'])->name('social-media.get-existing-data');
    Route::post('/social-media/ai-analysis', [SocialMediaController::class, 'aiAnalysis'])->name('social-media.ai-analysis');
    Route::post('/social-media/{socialMediaAnalysis}/re-analyze', [SocialMediaController::class, 'reAnalyze'])->name('social-media.re-analyze');
    Route::post('/social-media/facebook', [SocialMediaController::class, 'getFacebookInfo'])->name('social-media.facebook');
    Route::post('/social-media/instagram', [SocialMediaController::class, 'getInstagramInfo'])->name('social-media.instagram');
    Route::post('/social-media/instagram-from-page', [SocialMediaController::class, 'getInstagramFromFacebookPage'])->name('social-media.instagram-from-page');
    
    // Data Analysis routes
    Route::get('/data-analysis/history', [DataAnalysisController::class, 'history'])->name('data-analysis.history');
    Route::post('/data-analysis/upload', [DataAnalysisController::class, 'upload'])->name('data-analysis.upload');
    Route::get('/data-analysis/{dataAnalysis}/analysis-html', [DataAnalysisController::class, 'getAnalysisHtml'])->name('data-analysis.analysis-html');
    Route::get('/data-analysis/{dataAnalysis}/excel-preview', [DataAnalysisController::class, 'excelPreview'])->name('data-analysis.excel-preview');
    Route::get('/data-analysis/{dataAnalysis}/dashboard', [DataAnalysisController::class, 'dashboard'])->name('data-analysis.dashboard');
    Route::get('/data-analysis/{dataAnalysis}', [DataAnalysisController::class, 'show'])->name('data-analysis.show');
    Route::delete('/data-analysis/{dataAnalysis}', [DataAnalysisController::class, 'destroy'])->name('data-analysis.destroy');
    
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
    
    // Messages routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{message}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
    
    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages/{partner}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/unread/{partner}', [ChatController::class, 'getUnreadCount'])->name('chat.unread');
    Route::post('/chat/mark-read/{partner}', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
    Route::delete('/chat/message/{message}', [ChatController::class, 'deleteMessage'])->name('chat.delete-message');
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
    Route::patch('/admins/{user}/client-limit', [SuperAdminController::class, 'setClientLimit'])->name('admins.set-client-limit');
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
    Route::post('/ai-provider', [SuperAdminController::class, 'updateAIProvider'])->name('ai-provider.update');
    Route::post('/ai-provider/test', [SuperAdminController::class, 'testAIProvider'])->name('ai-provider.test');
    
    // Analytics routes
    Route::get('/analytics', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/data', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'getData'])->name('analytics.data');
    Route::get('/analytics/export', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'export'])->name('analytics.export');
    Route::get('/analytics/user/{userId}', [App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'getUserAnalytics'])->name('analytics.user');
    
    // Messages routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{message}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
    
    // Chat routes
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages/{partner}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/unread/{partner}', [ChatController::class, 'getUnreadCount'])->name('chat.unread');
    Route::post('/chat/mark-read/{partner}', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
    Route::delete('/chat/message/{message}', [ChatController::class, 'deleteMessage'])->name('chat.delete-message');
});

require __DIR__.'/auth.php';
