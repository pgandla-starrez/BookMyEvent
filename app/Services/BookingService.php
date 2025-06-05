<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Database\DatabaseManager;
use InvalidArgumentException;

class BookingService
{
    public function __construct(
        private DatabaseManager $db
    ) {}

    /**
     * Create a new booking for an event.
     *
     * @param int $eventId
     * @param string $userName
     * @param int $numTickets
     * @return Booking
     * @throws InvalidArgumentException
     */
    public function createBooking(int $eventId, string $userName, int $numTickets): Booking
    {
        // Validate number of tickets
        if ($numTickets <= 0) {
            throw new InvalidArgumentException('Number of tickets must be at least 1');
        }

        return $this->db->transaction(function () use ($eventId, $userName, $numTickets) {
            // Find the event with lock for update to handle concurrency
            $event = Event::lockForUpdate()->find($eventId);

            if (!$event) {
                throw new InvalidArgumentException('Event not found');
            }

            // Check if enough tickets are available
            if ($event->available_tickets < $numTickets) {
                throw new InvalidArgumentException('Not enough tickets available');
            }

            // Calculate total amount
            $totalAmount = $event->ticket_price * $numTickets;

            // Create the booking
            $booking = Booking::create([
                'event_id' => $eventId,
                'user_name' => $userName,
                'num_tickets' => $numTickets,
                'total_amount' => $totalAmount,
            ]);

            // Update available tickets
            $event->decrement('available_tickets', $numTickets);

            return $booking;
        });
    }
}
