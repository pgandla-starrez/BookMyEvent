<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'available_tickets',
        'ticket_price',
    ];

    protected $casts = [
        'available_tickets' => 'integer',
        'ticket_price' => 'decimal:2',
    ];

    /**
     * Get the bookings for the event.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
