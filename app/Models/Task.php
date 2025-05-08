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
}
