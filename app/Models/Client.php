<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
          'name', 'gender', 'phone', 'code', 'last_call_at','date_of_birth','work','age','weight','governorate','type','notes','suggested_by',
          'injury','doctor_report','injury_first_date','injury_reason','doctor_name','scan_type','most_paineful_position','most_restful_position',
          'num_sessions_available','best_dates_for_sessions','numbness_in_limbs','is_previous_surgery',
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
