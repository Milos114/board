<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lane extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeFilter($query)
    {
        $query->with([
            'tickets' => [
                'user',
                'priority',
            ]
        ])
            ->withCount('tickets');

        return $query;
    }
}
