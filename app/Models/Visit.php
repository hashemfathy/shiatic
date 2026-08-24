<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint', 'price', 'date', 'hour','type', 'duration', 'client_id', 'protocol_id','notes','paid','due_to','due_from','discount_percentage','improvement_percentage', 'request_id', 'coupon_code', 'coupon_discount'
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
    public function request()
    {
        return $this->belongsTo(Request::class, 'request_id');
    }

    /**
     * @return HasMany
     */
    public function protocol()
    {
        return $this->belongsTo(Protocol::class, 'protocol_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
