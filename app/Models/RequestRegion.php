<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestRegion extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'region_number',
        'repetitions',
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
