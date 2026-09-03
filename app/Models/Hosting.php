<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hosting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hosting';

    protected $fillable = [
        'service_id',
        'user_id',
        'domain_id',
        'domain_name',
        'plan_name',
        'status',
        'start_date',
        'expiry_date',
        'storage',
        'bandwidth',
        'max_emails',
        'max_databases',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
