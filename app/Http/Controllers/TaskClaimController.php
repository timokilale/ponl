<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskClaim;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskClaimController extends Controller
{
    /**
     * Display a listing of the user's task claims.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();

        // Get the user's active task claims
        $activeClaims = $user->activeTaskClaims()->with('task')->get();

        // Get the user's expired task claims from the last 7 days
        $recentExpiredClaims = $user->taskClaims()
            ->with('task')
            ->where('expires_at', '<=', now())
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tasks.claims.index', compact('activeClaims', 'recentExpiredClaims'));
    }

    /**
     * Claim a task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function claim(Request $request, Task $task)
    {
        $user = auth()->user();

        // Check if the user has the required VIP level
        if ($task->vip_level_required > $user->vip_level_id) {
            return redirect()->route('tasks.index')
                ->with('error', 'You do not have the required VIP level to claim this task.');
        }

        // Check if the task is active
        if (!$task->is_active) {
            return redirect()->route('tasks.index')
                ->with('error', 'This task is no longer available.');
        }

        // Check if the user already has an active claim for this task
        $existingClaim = TaskClaim::where('user_id', $user->id)
            ->where('task_id', $task->id)
            ->first();

        if ($existingClaim && $existingClaim->expires_at > now()) {
            return redirect()->route('tasks.index')
                ->with('error', 'You have already claimed this task. Wait for it to expire before claiming again.');
        }

        // Check if the user has reached their daily task limit
        $today = now()->startOfDay();
        $claimedTasksToday = $user->taskClaims()
            ->where('created_at', '>=', $today)
            ->count();

        $vipLevel = $user->vipLevel;
        $dailyLimit = $vipLevel->daily_tasks_limit;

        if ($claimedTasksToday >= $dailyLimit) {
            return redirect()->route('tasks.index')
                ->with('error', "You have reached your daily limit of {$dailyLimit} tasks.");
        }

        // Calculate the reward with VIP bonus
        $baseReward = $task->reward;
        $rewardMultiplier = $vipLevel->reward_multiplier;
        $finalReward = $baseReward * $rewardMultiplier;

        // Create the task claim with 24-hour expiration
        $expiresAt = now()->addHours(24);

        try {
            DB::beginTransaction();

            // Step 1: Create or update the task claim
            if ($existingClaim) {
                $taskClaim = $existingClaim;
                $taskClaim->reward = $finalReward;
                $taskClaim->claimed_at = now();
                $taskClaim->expires_at = $expiresAt;
                $taskClaim->save();
            } else {
                // Create a new task claim
                $taskClaim = new TaskClaim();
                $taskClaim->user_id = $user->id;
                $taskClaim->task_id = $task->id;
                $taskClaim->reward = $finalReward;
                $taskClaim->claimed_at = now();
                $taskClaim->expires_at = $expiresAt;
                $taskClaim->save();
            }

            // Step 2: Update user balance
            $user->balance += $finalReward;
            $user->save();

            // Step 3: Create transaction record
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $finalReward;
            $transaction->type = 'credit';
            $transaction->description = 'Task claim reward: ' . $task->title;
            $transaction->reference_id = $taskClaim->id;
            $transaction->reference_type = 'task_claim';
            $transaction->status = 'completed';
            $transaction->balance_after = $user->balance;
            $transaction->save();

            // Step 4: Create notification
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Task Claimed',
                'message' => "You have successfully claimed '{$task->title}' and received {$finalReward} USDT.",
                'type' => 'task',
            ]);

            // Step 5: Process referral reward if applicable (in a separate try-catch)
            try {
                $this->processReferralReward($user, $finalReward);
            } catch (\Exception $refErr) {
                \Illuminate\Support\Facades\Log::error('Referral reward error: ' . $refErr->getMessage());
            }

            DB::commit();

            return redirect()->route('tasks.index')
                ->with('success', "Task claimed successfully! You received {$finalReward} USDT.");

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Task claim error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());

            return redirect()->route('tasks.index')
                ->with('error', 'An error occurred while claiming the task: ' . $e->getMessage());
        }
    }

    /**
     * Process referral reward for a task claim.
     *
     * @param  \App\Models\User  $user
     * @param  float  $claimReward
     * @return void
     */
    private function processReferralReward($user, $claimReward)
    {
        try {
            // Check if the user was referred by someone
            if (empty($user->referred_by)) {
                return;
            }

            // Find the referral record
            $referral = Referral::where('referred_id', $user->id)->first();

            if (!$referral) {
                return;
            }

            // Get the referrer
            $referrer = $referral->referrer;

            if (!$referrer || !$referrer->is_active) {
                return;
            }

            // Get the referral reward percentage from settings
            $referralRewardPercentage = Setting::getValue('referral_reward_percentage', 10);

            // Calculate the referral reward
            $referralReward = ($claimReward * $referralRewardPercentage) / 100;

            if ($referralReward <= 0) {
                return;
            }

            // Update referrer's balance
            $referrer->balance += $referralReward;
            $referrer->save();

            // Update the referral record
            $referral->reward = $referral->reward + $referralReward;
            $referral->save();

            // Create transaction record for the referrer
            $transaction = new Transaction();
            $transaction->user_id = $referrer->id;
            $transaction->amount = $referralReward;
            $transaction->type = 'credit';
            $transaction->description = 'Referral reward from ' . $user->username;
            $transaction->reference_id = $referral->id;
            $transaction->reference_type = 'referral';
            $transaction->status = 'completed';
            $transaction->balance_after = $referrer->balance;
            $transaction->save();

            // Create notification for the referrer
            Notification::create([
                'user_id' => $referrer->id,
                'title' => 'Referral Reward',
                'message' => "You received {$referralReward} USDT as a referral reward from {$user->username}.",
                'type' => 'referral',
            ]);
        } catch (\Exception $e) {
            // Log the error but don't throw it (to prevent the main transaction from failing)
            \Illuminate\Support\Facades\Log::error('Referral reward error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
        }
    }
}
