<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\VideoModerationController;
use App\Http\Controllers\Admin\CommentModerationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestPurchaseController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

// Maintenance page (always accessible)
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// Emergency access key validation
Route::post('/maintenance/validate-key', function (\Illuminate\Http\Request $request) {
    $request->validate(['key' => 'required|string']);
    
    $storedKey = \App\Models\SiteSetting::get('emergency_access_key', '');
    
    if (empty($storedKey)) {
        return response()->json([
            'success' => false,
            'message' => 'Sistema de acesso de emergência não configurado.'
        ], 400);
    }
    
    // Use hash comparison to prevent timing attacks
    if (hash_equals($storedKey, $request->key)) {
        return response()->json(['success' => true]);
    }
    
    // Log failed attempt
    \App\Models\ActivityLog::create([
        'user_id' => null,
        'action' => 'emergency_key_failed',
        'description' => 'Tentativa de acesso de emergência com chave inválida',
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);
    
    return response()->json([
        'success' => false,
        'message' => 'Chave de emergência inválida.'
    ], 403);
})->name('maintenance.validate-key');

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// =================================================================
// NEWS FEED - PÚBLICO (PayWall apenas nas mídias)
// =================================================================
Route::prefix('news')->name('news.')->group(function () {
    // Feed principal - público
    Route::get('/', [NewsController::class, 'index'])->name('index');
    
    // Pesquisa de notícias - público
    Route::get('/search', [NewsController::class, 'search'])->name('search');
    
    // Feed por categoria - público
    Route::get('/category/{category}', [NewsController::class, 'category'])->name('category');
    
    // Feed por tag - público
    Route::get('/tag/{slug}', [NewsController::class, 'tag'])->name('tag');
    
    // Notícia individual - público (mídia protegida por paywall)
    Route::get('/{video:slug}', [NewsController::class, 'show'])->name('show');
});

// Redirect antigos URLs /videos/* para /news/*
Route::get('/videos', function () {
    return redirect()->route('news.index', [], 301);
});

Route::get('/videos/{video}', function ($video) {
    // Tentar encontrar por ID ou slug
    $videoModel = \App\Models\Video::find($video) 
        ?? \App\Models\Video::where('slug', $video)->first();
    
    if ($videoModel) {
        return redirect()->route('news.show', $videoModel->slug, 301);
    }
    
    return redirect()->route('news.index', [], 301);
})->where('video', '.*');

// Invite Validation (Public)
Route::get('/invite', [InviteController::class, 'showValidationForm'])->name('invite.validate');
Route::post('/invite/validate', [InviteController::class, 'validate'])->name('invite.check');

// Guest Purchase Routes (No Auth Required)
Route::prefix('buy-invite')->name('guest.purchase.')->group(function () {
    Route::get('/', [GuestPurchaseController::class, 'index'])->name('index');
    Route::post('/checkout', [GuestPurchaseController::class, 'checkout'])->name('checkout');
    Route::post('/process', [GuestPurchaseController::class, 'store'])->name('store');
    Route::post('/{id}/confirm', [GuestPurchaseController::class, 'confirmPayment'])->name('confirm');
    Route::get('/{id}/success', [GuestPurchaseController::class, 'success'])->name('success');
});

// Auth Routes (Custom Registration with Invite)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Purchase Routes for Authenticated Users (Separated)
Route::middleware('auth')->prefix('dashboard/buy-invite')->name('purchase.')->group(function () {
    Route::get('/', [PurchaseController::class, 'index'])->name('index');
    Route::post('/checkout', [PurchaseController::class, 'create'])->name('create');
    Route::post('/process', [PurchaseController::class, 'store'])->name('store');
    Route::post('/{id}/confirm', [PurchaseController::class, 'confirmPayment'])->name('confirm');
});

// User Dashboard (Admin Only Now)
Route::middleware(['auth', 'admin', 'check.suspended'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard Settings Toggle (Rate Limited: 10 requests per minute)
    Route::post('/dashboard/settings/toggle', [DashboardController::class, 'toggleSetting'])
        ->name('dashboard.settings.toggle')
        ->middleware('throttle:10,1');
    
    // Regenerate Emergency Key (Rate Limited: 5 requests per minute)
    Route::post('/dashboard/regenerate-key', [DashboardController::class, 'regenerateEmergencyKey'])
        ->name('dashboard.regenerate-key')
        ->middleware('throttle:5,1');
});

// Authenticated User Routes (non-admin)
Route::middleware(['auth', 'check.suspended'])->group(function () {
    // Profile Routes
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::get('/profile/invites', [App\Http\Controllers\ProfileController::class, 'invites'])->name('profile.invites');
    
    // Invites Management (moved to profile)
    Route::prefix('profile/invites')->name('profile.invites.')->group(function () {
        Route::post('/', [InviteController::class, 'store'])->name('store');
        Route::delete('/{id}', [InviteController::class, 'destroy'])->name('destroy');
    });
    
    // Purchase History
    Route::get('/dashboard/purchases/{id}', [PurchaseController::class, 'show'])->name('dashboard.purchases.show');
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [App\Http\Controllers\NotificationController::class, 'getUnread'])->name('unread');
        Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('destroy-all');
    });
    
    // Video Upload/Edit Routes (Authenticated - agora cria notícias)
    Route::get('/submit', [VideoController::class, 'create'])->name('news.create');
    Route::post('/submit', [VideoController::class, 'store'])->name('news.store');
    Route::get('/news/{video:slug}/edit', [VideoController::class, 'edit'])->name('news.edit');
    Route::patch('/news/{video:slug}', [VideoController::class, 'update'])->name('news.update');
    Route::delete('/news/{video:slug}', [VideoController::class, 'destroy'])->name('news.destroy');
    Route::post('/news/{video:slug}/comments', [VideoController::class, 'storeComment'])->name('news.comments.store');
    Route::delete('/news/comments/{comment}', [VideoController::class, 'destroyComment'])->name('news.comments.destroy');
    Route::post('/news/{video:slug}/report', [VideoController::class, 'report'])->name('news.report');
    Route::get('/my-submissions', [VideoController::class, 'myVideos'])->name('news.my-submissions');
});

// Public Routes (Profile é público)
Route::get('/profile/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');

// Admin Routes
Route::middleware(['auth', 'admin', 'check.suspended'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Settings Toggle (Rate Limited: 10 requests per minute)
    Route::post('/settings/toggle', [AdminDashboardController::class, 'toggleSetting'])
        ->name('settings.toggle')
        ->middleware('throttle:10,1');
    
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
    Route::get('/users/{id}', [AdminDashboardController::class, 'userShow'])->name('users.show');
    Route::post('/users/{id}/suspend', [AdminDashboardController::class, 'userSuspend'])->name('users.suspend');
    Route::post('/users/{id}/unsuspend', [AdminDashboardController::class, 'userUnsuspend'])->name('users.unsuspend');
    
    Route::get('/invites', [AdminDashboardController::class, 'invites'])->name('invites.index');
    Route::get('/purchases', [AdminDashboardController::class, 'purchases'])->name('purchases');
    Route::get('/activity', [AdminDashboardController::class, 'activityLogs'])->name('activity');
    
    // Video Moderation
    Route::prefix('videos')->name('videos.')->group(function () {
        Route::get('/', [VideoModerationController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [VideoModerationController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        Route::patch('/{id}', [VideoModerationController::class, 'update'])->name('update')->where('id', '[0-9]+');
        Route::post('/{id}/approve', [VideoModerationController::class, 'approve'])->name('approve')->where('id', '[0-9]+');
        Route::post('/{id}/reject', [VideoModerationController::class, 'reject'])->name('reject')->where('id', '[0-9]+');
        Route::post('/{id}/hide', [VideoModerationController::class, 'hide'])->name('hide')->where('id', '[0-9]+');
        Route::post('/{id}/toggle-featured', [VideoModerationController::class, 'toggleFeatured'])->name('toggle-featured')->where('id', '[0-9]+');
        Route::delete('/{id}', [VideoModerationController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
    });
    
    // Report Management
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [VideoModerationController::class, 'reports'])->name('index');
        Route::post('/{report}/review', [VideoModerationController::class, 'reviewReport'])->name('review');
    });
    
    // Comment Moderation
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [CommentModerationController::class, 'index'])->name('index');
        Route::post('/{comment}/hide', [CommentModerationController::class, 'hide'])->name('hide');
        Route::post('/{comment}/approve', [CommentModerationController::class, 'approve'])->name('approve');
        Route::delete('/{comment}', [CommentModerationController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
