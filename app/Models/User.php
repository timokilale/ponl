<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updated(function ($user) {
            // Check if balance was updated
            if ($user->isDirty('balance')) {
                // Check and upgrade VIP level if needed
                app(\App\Services\VipService::class)->checkAndUpgradeVipLevel($user);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'phone_number',
        'is_phone_verified',
        'phone_verified_at',
        'balance',
        'vip_level_id',
        'referral_code',
        'referred_by',
        'is_admin',
        'is_active',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'is_phone_verified' => 'boolean',
            'balance' => 'decimal:6',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the VIP level that the user belongs to.
     */
    public function vipLevel()
    {
        return $this->belongsTo(VipLevel::class);
    }

    /**
     * Get the task completions for the user.
     */
    public function taskCompletions()
    {
        return $this->hasMany(TaskCompletion::class);
    }

    /**
     * Get the task claims for the user.
     */
    public function taskClaims()
    {
        return $this->hasMany(TaskClaim::class);
    }

    /**
     * Get the active task claims for the user.
     */
    public function activeTaskClaims()
    {
        return $this->taskClaims()->where('expires_at', '>', now());
    }

    /**
     * Get the withdrawals for the user.
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the referrals where the user is the referrer.
     */
    public function referrals()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get the referral where the user was referred.
     */
    public function referredBy()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the activity logs for the user.
     */
    public function activityLogs()
    {
        return $this->hasMany(UserActivityLog::class);
    }
}
