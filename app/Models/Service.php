<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'user_id',
        'order_id',
        'plan_id',
        'type',
        'name',
        'status',
        'start_date',
        'expiry_date',
        'activated_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'expiry_date' => 'date',
            'activated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function domain(): HasOne
    {
        return $this->hasOne(Domain::class);
    }

    public function hosting(): HasOne
    {
        return $this->hasOne(Hosting::class);
    }

    public function emailService(): HasOne
    {
        return $this->hasOne(EmailService::class);
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(ServiceCredential::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(ServiceTransfer::class, 'from_user_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(ServiceTransfer::class, 'to_user_id');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
