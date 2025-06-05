<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Event;
use App\Services\BookingService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $bookingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookingService = new BookingService(app(DatabaseManager::class));
    }

    public function test_successful_booking_and_available_tickets_decrement(): void
    {
        // Arrange
        $event = Event::create([
            'name' => 'Test Event',
            'available_tickets' => 10,
            'ticket_price' => 50.00,
        ]);

        // Act
        $booking = $this->bookingService->createBooking(
            eventId: $event->id,
            userName: 'John Doe',
            numTickets: 3
        );

        // Assert
        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertEquals($event->id, $booking->event_id);
        $this->assertEquals('John Doe', $booking->user_name);
        $this->assertEquals(3, $booking->num_tickets);
        $this->assertEquals(150.00, $booking->total_amount);

        // Check that available tickets were decremented
        $event->refresh();
        $this->assertEquals(7, $event->available_tickets);

        // Verify booking was saved to database
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'event_id' => $event->id,
            'user_name' => 'John Doe',
            'num_tickets' => 3,
            'total_amount' => 150.00,
        ]);
    }

    public function test_attempting_to_book_more_tickets_than_available(): void
    {
        // Arrange
        $event = Event::create([
            'name' => 'Limited Event',
            'available_tickets' => 2,
            'ticket_price' => 25.00,
        ]);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Not enough tickets available');

        $this->bookingService->createBooking(
            eventId: $event->id,
            userName: 'Jane Doe',
            numTickets: 5
        );

        // Verify no booking was created
        $this->assertDatabaseCount('bookings', 0);

        // Verify available tickets remain unchanged
        $event->refresh();
        $this->assertEquals(2, $event->available_tickets);
    }

    public function test_calculation_of_total_amount(): void
    {
        // Arrange
        $event = Event::create([
            'name' => 'Premium Event',
            'available_tickets' => 100,
            'ticket_price' => 99.99,
        ]);

        // Act
        $booking = $this->bookingService->createBooking(
            eventId: $event->id,
            userName: 'Alice Smith',
            numTickets: 4
        );

        // Assert
        $expectedTotal = 99.99 * 4;
        $this->assertEquals($expectedTotal, $booking->total_amount);
    }

    public function test_exception_handling_for_non_existent_events(): void
    {
        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Event not found');

        $this->bookingService->createBooking(
            eventId: 999, // Non-existent event ID
            userName: 'Bob Wilson',
            numTickets: 1
        );

        // Verify no booking was created
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_booking_exactly_all_available_tickets(): void
    {
        // Arrange
        $event = Event::create([
            'name' => 'Small Event',
            'available_tickets' => 1,
            'ticket_price' => 100.00,
        ]);

        // Act
        $booking = $this->bookingService->createBooking(
            eventId: $event->id,
            userName: 'Charlie Brown',
            numTickets: 1
        );

        // Assert
        $this->assertEquals(1, $booking->num_tickets);
        $this->assertEquals(100.00, $booking->total_amount);

        // Check that available tickets are now zero
        $event->refresh();
        $this->assertEquals(0, $event->available_tickets);
    }

    public function test_attempting_to_book_zero_tickets(): void
    {
        // Arrange
        $event = Event::create([
            'name' => 'Test Event',
            'available_tickets' => 10,
            'ticket_price' => 50.00,
        ]);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Number of tickets must be at least 1');

        $this->bookingService->createBooking(
            eventId: $event->id,
            userName: 'David Davis',
            numTickets: 0
        );

        // Verify no booking was created
        $this->assertDatabaseCount('bookings', 0);

        // Verify available tickets remain unchanged
        $event->refresh();
        $this->assertEquals(10, $event->available_tickets);
    }

    public function test_attempting_to_book_negative_tickets(): void
    {
        // Arrange
        $event = Event::create([
            'name' => 'Test Event',
            'available_tickets' => 10,
            'ticket_price' => 50.00,
        ]);

        // Act & Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Number of tickets must be at least 1');

        $this->bookingService->createBooking(
            eventId: $event->id,
            userName: 'Eve Wilson',
            numTickets: -2
        );

        // Verify no booking was created
        $this->assertDatabaseCount('bookings', 0);

        // Verify available tickets remain unchanged
        $event->refresh();
        $this->assertEquals(10, $event->available_tickets);
    }
}
