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
        $this->middleware('auth');
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
        $vipLevels = \App\Models\VipLevel::orderBy('points_required')->get();

        // Get the user's current VIP level
        $currentVipLevel = $user->vipLevel;

        // Get the next VIP level
        $nextVipLevel = \App\Models\VipLevel::where('points_required', '>', $currentVipLevel->points_required)
            ->orderBy('points_required')
            ->first();

        // Calculate progress to next level
        $progress = 0;
        if ($nextVipLevel) {
            $currentPoints = $user->vip_points;
            $currentLevelPoints = $currentVipLevel->points_required;
            $nextLevelPoints = $nextVipLevel->points_required;

            $pointsNeeded = $nextLevelPoints - $currentLevelPoints;
            $pointsGained = $currentPoints - $currentLevelPoints;

            $progress = ($pointsGained / $pointsNeeded) * 100;
            $progress = min(100, max(0, $progress)); // Ensure progress is between 0 and 100
        }

        return view('vip.index', compact('vipLevels', 'currentVipLevel', 'nextVipLevel', 'progress'));
    }
}
