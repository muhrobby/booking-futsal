<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'field_id','time_slot_id','booking_date',
        'customer_name','customer_phone','status','notes','user_id'
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function field(): BelongsTo {
        return $this->belongsTo(Field::class);
    }

    public function timeSlot(): BelongsTo {
        return $this->belongsTo(TimeSlot::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
