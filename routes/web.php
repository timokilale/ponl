<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCompletionController;
use App\Http\Controllers\VipController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Payment\CoinbaseController;
use App\Http\Controllers\Webhook\CoinbaseWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Task routes
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');

    // Task completion routes
    Route::post('/tasks/{task}/complete', [TaskCompletionController::class, 'store'])->name('tasks.complete');
    Route::get('/tasks/completions', [TaskCompletionController::class, 'index'])->name('tasks.completions');

    // VIP routes
    Route::get('/vip', [VipController::class, 'index'])->name('vip.index');

    // Referral routes
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');

    // Withdrawal routes
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals', [WithdrawalController::class, 'store'])->name('withdrawals.store');

    // Transaction routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Payment routes
    Route::prefix('payment')->name('payment.')->group(function () {
        // Coinbase routes
        Route::get('/deposit', [CoinbaseController::class, 'showDepositForm'])->name('deposit');
        Route::post('/deposit/coinbase', [CoinbaseController::class, 'createCharge'])->name('coinbase.charge');
        Route::get('/deposit/coinbase/success', [CoinbaseController::class, 'success'])->name('coinbase.success');
        Route::get('/deposit/coinbase/cancel', [CoinbaseController::class, 'cancel'])->name('coinbase.cancel');
    });
});

// Webhook routes
Route::post('/webhooks/coinbase', [CoinbaseWebhookController::class, 'handle'])->name('webhooks.coinbase');

// Admin routes are now in admin.php

require __DIR__.'/auth.php';
