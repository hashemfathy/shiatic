<?php

namespace App\Traits;


use App\Models\Log;
use App\Observers\ActionLog\ActionLogObserver;

trait ActionLog
{
    /**
     *  boot observer for logging
     */
    public static function bootActionLog()
    {
        if (config('action-log.enable')) {
            static::observe(ActionLogObserver::class);
        }
    }
    /**
     * Get all of the loggs for the post.
     */
    public function loggs()
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
