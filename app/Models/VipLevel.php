<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VipLevel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'deposit_required',
        'reward_multiplier',
        'daily_tasks_limit',
        'withdrawal_fee_discount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deposit_required' => 'decimal:2',
        'reward_multiplier' => 'decimal:2',
        'daily_tasks_limit' => 'integer',
        'withdrawal_fee_discount' => 'decimal:2',
    ];

    /**
     * Get the users that belong to this VIP level.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the tasks that require this VIP level.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'vip_level_required');
    }
}
