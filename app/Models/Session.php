<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $fillable = [
        'price','employee_id','notes','time_or_num','improvement_percentage','type','visit_id'
    ];
    /**
     * @return HasMany
     */
    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }
    /**
     * @return HasMany
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function protocol()
    {
        return $this->belongsTo(Protocol::class, 'protocol_id');
    }
}
