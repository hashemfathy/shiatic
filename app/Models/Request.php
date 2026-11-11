<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'phone',
        'description',
        'date',
        'status',
        'gender',
        'time',
        'deposit',
        'booking_type',
        'service_type',
        'packages',
        'total_price',
        'total_duration',
        'user_agreement',
        'is_urgent',
        'coupon_code',
        'coupon_discount',
        'numbness',
        'payment_status',
        'transaction_id',
        'payment_method',
        'paid_amount',
        'paid_at',
        'payment_meta',
    ];

    protected $casts = [
        'packages' => 'array',
        'is_urgent' => 'boolean',
        'paid_at' => 'datetime',
        'paid_amount' => 'float',
    ];

    public function regions()
    {
        return $this->hasMany(RequestRegion::class);
    }

    public function children()
    {
        return $this->hasMany(Request::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Request::class, 'parent_id');
    }
}
