<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description',
        'reference_id',
        'reference_type',
        'status',
        'balance_after',
        'wallet_address',
        'network',
        'blockchain_txid',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:6',
        'balance_after' => 'decimal:6',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model based on reference_type.
     */
    public function reference()
    {
        if ($this->reference_type === 'task_completion') {
            return $this->belongsTo(TaskCompletion::class, 'reference_id');
        } elseif ($this->reference_type === 'withdrawal') {
            return $this->belongsTo(Withdrawal::class, 'reference_id');
        } elseif ($this->reference_type === 'referral') {
            return $this->belongsTo(Referral::class, 'reference_id');
        }

        return null;
    }
}
