<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralController extends Controller
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
     * Display a listing of the referrals.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Referral::with(['referrer', 'referred']);

        // Filter by search term
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('referrer', function($uq) use ($search) {
                    $uq->where('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('referred', function($uq) use ($search) {
                    $uq->where('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Filter by referrer
        if ($request->has('referrer_id') && $request->referrer_id != '') {
            $query->where('referrer_id', $request->referrer_id);
        }

        // Order by
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        // Paginate results
        $referrals = $query->paginate(15)->withQueryString();

        // Get top referrers
        $topReferrers = User::select(
                'users.id',
                'users.username',
                DB::raw('COUNT(referrals.id) as referral_count'),
                DB::raw('SUM(transactions.amount) as total_earnings')
            )
            ->join('referrals', 'users.id', '=', 'referrals.referrer_id')
            ->leftJoin('transactions', function($join) {
                $join->on('transactions.reference_id', '=', 'referrals.id')
                    ->where('transactions.reference_type', '=', 'referral')
                    ->where('transactions.type', '=', 'credit');
            })
            ->groupBy('users.id', 'users.username')
            ->orderBy('referral_count', 'desc')
            ->limit(10)
            ->get();

        // Get referral statistics
        $totalReferrals = Referral::count();
        $totalReferralEarnings = Transaction::where('reference_type', 'referral')
            ->where('type', 'credit')
            ->sum('amount');

        // Get referral reward percentage
        $referralRewardPercentage = Setting::getValue('referral_reward_percentage', 10);

        return view('admin.referrals.index', compact(
            'referrals',
            'topReferrers',
            'totalReferrals',
            'totalReferralEarnings',
            'referralRewardPercentage'
        ));
    }

    /**
     * Display the specified referral.
     *
     * @param  \App\Models\Referral  $referral
     * @return \Illuminate\View\View
     */
    public function show(Referral $referral)
    {
        // Load relationships
        $referral->load(['referrer', 'referred']);

        // Get related transactions
        $transactions = Transaction::where('reference_id', $referral->id)
            ->where('reference_type', 'referral')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.referrals.show', compact('referral', 'transactions'));
    }

    /**
     * Show referral statistics.
     *
     * @return \Illuminate\View\View
     */
    public function statistics()
    {
        // Get daily referral statistics for the last 30 days
        $dailyStats = Referral::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get referral earnings statistics for the last 30 days
        $earningsStats = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->where('reference_type', 'referral')
            ->where('type', 'credit')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get top referrers of all time
        $topReferrers = User::select(
                'users.id',
                'users.username',
                DB::raw('COUNT(referrals.id) as referral_count'),
                DB::raw('SUM(transactions.amount) as total_earnings')
            )
            ->join('referrals', 'users.id', '=', 'referrals.referrer_id')
            ->leftJoin('transactions', function($join) {
                $join->on('transactions.reference_id', '=', 'referrals.id')
                    ->where('transactions.reference_type', '=', 'referral')
                    ->where('transactions.type', '=', 'credit');
            })
            ->groupBy('users.id', 'users.username')
            ->orderBy('referral_count', 'desc')
            ->limit(20)
            ->get();

        // Get referral conversion statistics
        $totalUsers = User::count();
        $referredUsers = User::whereNotNull('referred_by')->count();
        $conversionRate = $totalUsers > 0 ? ($referredUsers / $totalUsers) * 100 : 0;

        return view('admin.referrals.statistics', compact(
            'dailyStats',
            'earningsStats',
            'topReferrers',
            'totalUsers',
            'referredUsers',
            'conversionRate'
        ));
    }
}
