<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Protocol extends Model
{
    use HasFactory;
    protected $guarded = [];
    /**
     *  define one to many relation Visit model
     * @return HasMany
     */
    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
