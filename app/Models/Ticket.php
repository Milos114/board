<?php

namespace App\Models;

use App\QueryFilters\SearchFilter;
use App\QueryFilters\StatusFilter;
use App\QueryFilters\UserFilter;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Pipeline;

class Ticket extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'lane_id',
        'priority_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function lane(): BelongsTo
    {
        return $this->belongsTo(Lane::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function scopeFilter($query)
    {
        return Pipeline::send($query)
            ->through([
                SearchFilter::class,
                StatusFilter::class,
                UserFilter::class,
            ])
            ->then(fn($query) => $query->paginate());
    }
}
