<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_name', 'complaint', 'price', 'date', 'hour', 'duration', 'client_id', 'specialist_id'
    ];
    /**
     * @return HasMany
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    /**
     * @return HasMany
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id');
    }
}
