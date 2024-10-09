<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait Loggable
{
    public static function boot(): void
    {
        parent::boot();

        static::updated(static function (Model $model) {
            self::logActivity('updated', $model);
        });

        static::created(static function (Model $model) {
            self::logActivity('created', $model);
        });

        static::deleted(static function (Model $model) {
            self::logActivity('deleted', $model);
        });
    }

    protected static function logActivity($event, $model): void
    {
        $user = auth()->user();

        $oldValues = [];
        $newValues = [];

        match ($event) {
            'updated' => [
                $oldValues = array_intersect_key($model->getOriginal(), $model->getDirty()),
                $newValues = $model->getDirty(),
            ],
            'created' => [
                $newValues = $model->getAttributes(),
            ],
            'deleted' => [
                $oldValues = $model->getAttributes(),
            ],
            default => null,
        };

        ActivityLog::create([
            'event' => $event,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'user_id' => $user?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues
        ]);
    }

}
