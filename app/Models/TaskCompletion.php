<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskCompletion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'task_id',
        'status',
        'proof',
        'reward',
        'admin_notes',
        'completed_at',
        'reviewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reward' => 'decimal:2',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user that completed this task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the task that was completed.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
