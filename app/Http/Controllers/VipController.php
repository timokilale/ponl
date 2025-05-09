<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VipController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware is now applied in the routes file
    }

    /**
     * Display the VIP levels and user's current status.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get all VIP levels
        $vipLevels = \App\Models\VipLevel::orderBy('deposit_required')->get();

        // Get the user's current VIP level
        $currentVipLevel = $user->vipLevel;

        // Get the next VIP level
        $nextVipLevel = \App\Models\VipLevel::where('deposit_required', '>', $currentVipLevel->deposit_required)
            ->orderBy('deposit_required')
            ->first();

        // Calculate progress to next level
        $progress = 0;
        if ($nextVipLevel) {
            $totalDeposits = \App\Models\Transaction::where('user_id', $user->id)
                ->where('type', 'credit')
                ->where('reference_type', 'deposit')
                ->where('status', 'completed')
                ->sum('amount');

            $currentLevelDeposit = $currentVipLevel->deposit_required;
            $nextLevelDeposit = $nextVipLevel->deposit_required;

            $depositNeeded = $nextLevelDeposit - $currentLevelDeposit;
            $depositMade = $totalDeposits - $currentLevelDeposit;

            $progress = ($depositMade / $depositNeeded) * 100;
            $progress = min(100, max(0, $progress)); // Ensure progress is between 0 and 100
        }

        return view('vip.index', compact('vipLevels', 'currentVipLevel', 'nextVipLevel', 'progress'));
    }
}
