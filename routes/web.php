<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\VideoModerationController;
use App\Http\Controllers\Admin\CommentModerationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestPurchaseController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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

// User Dashboard
Route::middleware(['auth', 'check.suspended'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    
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
    
    // Video Routes (Authenticated)
    Route::get('/videos/create', [VideoController::class, 'create'])->name('videos.create');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::get('/videos/{video}/edit', [VideoController::class, 'edit'])->name('videos.edit');
    Route::patch('/videos/{video}', [VideoController::class, 'update'])->name('videos.update');
    Route::delete('/videos/{video}', [VideoController::class, 'destroy'])->name('videos.destroy');
    Route::post('/videos/{video}/comments', [VideoController::class, 'storeComment'])->name('videos.comments.store');
    Route::post('/videos/{video}/report', [VideoController::class, 'report'])->name('videos.report');
    Route::get('/my-videos', [VideoController::class, 'myVideos'])->name('videos.my-videos');
});

// Public Routes (Videos require auth)
Route::get('/profile/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
Route::middleware(['auth', 'check.suspended'])->group(function () {
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');
});

// Admin Routes
Route::middleware(['auth', 'admin', 'check.suspended'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
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
        Route::post('/{video}/approve', [VideoModerationController::class, 'approve'])->name('approve');
        Route::post('/{video}/reject', [VideoModerationController::class, 'reject'])->name('reject');
        Route::post('/{video}/hide', [VideoModerationController::class, 'hide'])->name('hide');
        Route::post('/{video}/toggle-featured', [VideoModerationController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::delete('/{video}', [VideoModerationController::class, 'destroy'])->name('destroy');
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
