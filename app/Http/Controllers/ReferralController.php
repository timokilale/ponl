<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * @var ReferralService
     */
    protected $referralService;

    /**
     * Create a new controller instance.
     *
     * @param ReferralService $referralService
     * @return void
     */
    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Display the user's referrals.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Ensure the user has a referral code
        $referralCode = $this->referralService->ensureUserHasReferralCode($user);

        // Generate a referral link
        $referralLink = $this->referralService->getReferralUrl($user);

        // Get the user's referrals
        $referrals = \App\Models\Referral::with('referred')
            ->where('referrer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get the referral reward percentage
        $referralRewardPercentage = \App\Models\Setting::getValue('referral_reward_percentage', 10);

        // Calculate total earnings from referrals
        $totalEarnings = \App\Models\Transaction::where('user_id', $user->id)
            ->where('type', 'credit')
            ->where('reference_type', 'referral')
            ->sum('amount');

        return view('referrals.index', compact('referralCode', 'referralLink', 'referrals', 'referralRewardPercentage', 'totalEarnings'));
    }
}
