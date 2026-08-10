<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','phone','age','employment_date','work_days'
    ];

     protected $casts = [
        'work_days' => 'array'
    ];
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
}
