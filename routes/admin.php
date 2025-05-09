<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TaskCompletionController;
use App\Http\Controllers\Admin\VipController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SocialLinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', UserController::class);

    // Tasks
    Route::resource('tasks', TaskController::class);

    // Task Completions
    Route::resource('task-completions', TaskCompletionController::class)->except(['create', 'store', 'destroy']);
    Route::post('task-completions/{taskCompletion}/approve', [TaskCompletionController::class, 'approve'])->name('task-completions.approve');
    Route::post('task-completions/{taskCompletion}/reject', [TaskCompletionController::class, 'reject'])->name('task-completions.reject');

    // VIP Levels
    Route::resource('vip-levels', VipController::class);

    // Withdrawals
    Route::resource('withdrawals', WithdrawalController::class)->except(['create', 'store', 'destroy']);
    Route::post('withdrawals/{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
    Route::post('withdrawals/{withdrawal}/reject', [WithdrawalController::class, 'reject'])->name('withdrawals.reject');

    // Transactions
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('transactions/statistics', [TransactionController::class, 'statistics'])->name('transactions.statistics');

    // Referrals
    Route::get('referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::get('referrals/{referral}', [ReferralController::class, 'show'])->name('referrals.show');
    Route::get('referrals/statistics', [ReferralController::class, 'statistics'])->name('referrals.statistics');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('settings/create', [SettingController::class, 'create'])->name('settings.create');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
    Route::get('settings/{setting}/edit', [SettingController::class, 'edit'])->name('settings.edit');
    Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy');

    // Social Links
    Route::resource('social-links', SocialLinkController::class);
});
