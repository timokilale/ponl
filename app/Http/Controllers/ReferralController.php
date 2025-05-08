<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReferralController extends Controller
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
     * Display the user's referrals.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get the user's referral code
        $referralCode = $user->referral_code;

        // Generate a referral link
        $referralLink = url('/register') . '?ref=' . $referralCode;

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
