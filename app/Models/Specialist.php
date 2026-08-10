<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name'
    ];

   
    /**
     *  define one to many relation Visit model
     * @return HasMany
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
