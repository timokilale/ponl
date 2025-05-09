<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskClaim extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'task_id',
        'reward',
        'claimed_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reward' => 'decimal:2',
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that claimed this task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the task that was claimed.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Scope a query to only include active claims.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope a query to only include expired claims.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}
