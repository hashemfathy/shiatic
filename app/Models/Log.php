<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['description', 'user_id'];
    /**
     *  define relation of loggable
     */
    public function loggable()
    {
        return $this->morph();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function scopeFilterByUser(Builder $query, $value)
    {
        return  $query->whereHas('user', function ($q) use ($value) {
            $q->where('name', 'like', "%$value%");
        });
    }
}
