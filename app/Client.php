<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'gender', 'phone', 'code'
    ];
    /**
     *  define one to many relation Visit model
     * @return HasMany
     */
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
