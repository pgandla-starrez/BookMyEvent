<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_name',
        'num_tickets',
        'total_amount',
    ];

    protected $casts = [
        'event_id' => 'integer',
        'num_tickets' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the event that the booking belongs to.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
