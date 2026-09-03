<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCredential extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_id',
        'label',
        'url',
        'username',
        'password',
        'port',
        'instructions',
        'additional_info',
        'is_visible_to_customer',
    ];

    protected function casts(): array
    {
        return [
            'is_visible_to_customer' => 'boolean',
            'password' => 'encrypted',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
