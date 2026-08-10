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
        'is_urgent'
    ];

    protected $casts = [
        'packages' => 'array',
        'is_urgent' => 'boolean',
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

    protected static function booted()
    {
        static::created(function ($request) {
            try {
                if ($request->parent_id || request()->has('attendees')) {
                    return;
                }
                $recipient = config('mail.to_address') ?? config('mail.from.address');

                if ($recipient && env('RESEND_API_KEY')) {
                    \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                        'Content-Type' => 'application/json',
                    ])->post('https://api.resend.com/emails', [
                        'from' => config('mail.from.address') ?? 'onboarding@resend.dev',
                        'to' => $recipient,
                        'subject' => 'طلب حجز جديد - ' . $request->name,
                        'html' => view('emails.new_request', ['bookingRequest' => $request])->render(),
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send new request email notification: ' . $e->getMessage());
            }
        });
    }
}
