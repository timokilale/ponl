<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'fee',
        'method',
        'status',
        'payment_details',
        'reference',
        'wallet_address',
        'network',
        'transaction_id',
        'blockchain_txid',
        'notes',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:6',
        'fee' => 'decimal:6',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the user that made this withdrawal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction associated with this withdrawal.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'reference_id')
            ->where('reference_type', 'withdrawal');
    }
}
