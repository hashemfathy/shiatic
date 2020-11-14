<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gender', 'phone', 'code', 'called', 'new_client'
    ];
    /**
     *  define one to many relation Visit model
     * @return HasMany
     */
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
    public function getNewClientAttribute()
    {
        return
            $this->created_at->format('m') == Carbon::now()->month;
    }
}
