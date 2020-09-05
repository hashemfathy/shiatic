<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentValue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'value', 'name', 'payment_item_id'
    ];
    /**
     *  define one to many relation Visit model
     * @return HasMany
     */
    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class);
    }
}
