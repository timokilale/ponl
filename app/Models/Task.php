<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'platform',
        'reward',
        'time_required',
        'difficulty',
        'vip_level_required',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reward' => 'decimal:2',
        'vip_level_required' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the VIP level that this task requires.
     */
    public function vipLevel()
    {
        return $this->belongsTo(VipLevel::class, 'vip_level_required');
    }

    /**
     * Get the task completions for this task.
     */
    public function completions()
    {
        return $this->hasMany(TaskCompletion::class);
    }

    /**
     * Get the task claims for this task.
     */
    public function claims()
    {
        return $this->hasMany(TaskClaim::class);
    }

    /**
     * Check if a user has an active claim for this task.
     *
     * @param int $userId
     * @return bool
     */
    public function isClaimedByUser($userId)
    {
        try {
            return $this->claims()
                ->where('user_id', $userId)
                ->where('expires_at', '>', now())
                ->exists();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('isClaimedByUser error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the active claim for a user for this task, if any.
     *
     * @param int $userId
     * @return \App\Models\TaskClaim|null
     */
    public function getActiveClaimForUser($userId)
    {
        try {
            return $this->claims()
                ->where('user_id', $userId)
                ->where('expires_at', '>', now())
                ->first();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('getActiveClaimForUser error: ' . $e->getMessage());
            return null;
        }
    }
}
