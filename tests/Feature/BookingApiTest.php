<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_booking_successfully(): void
    {
        // Arrange
        $event = Event::create([
            'name' => 'Test Event',
            'available_tickets' => 10,
            'ticket_price' => 50.00,
        ]);

        $bookingData = [
            'event_id' => $event->id,
            'user_name' => 'John Doe',
            'num_tickets' => 3,
        ];

        // Act
        $response = $this->post('/api/bookings', $bookingData);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'event_id',
            'user_name',
            'num_tickets',
            'total_amount',
        ]);

        $response->assertJson([
            'event_id' => $event->id,
            'user_name' => 'John Doe',
            'num_tickets' => 3,
            'total_amount' => 150.00,
        ]);

        // Verify booking was created in database
        $this->assertDatabaseHas('bookings', [
            'event_id' => $event->id,
            'user_name' => 'John Doe',
            'num_tickets' => 3,
            'total_amount' => 150.00,
        ]);

        // Verify event tickets were decremented
        $event->refresh();
        $this->assertEquals(7, $event->available_tickets);
    }

    public function test_booking_validation_errors(): void
    {
        // Test missing event_id
        $response = $this->post('/api/bookings', [
            'user_name' => 'John Doe',
            'num_tickets' => 2,
        ]);
        $response->assertStatus(422);

        // Test missing user_name
        $response = $this->post('/api/bookings', [
            'event_id' => 1,
            'num_tickets' => 2,
        ]);
        $response->assertStatus(422);

        // Test missing num_tickets
        $response = $this->post('/api/bookings', [
            'event_id' => 1,
            'user_name' => 'John Doe',
        ]);
        $response->assertStatus(422);

        // Test invalid num_tickets (zero)
        $event = Event::create([
            'name' => 'Test Event',
            'available_tickets' => 10,
            'ticket_price' => 50.00,
        ]);

        $response = $this->post('/api/bookings', [
            'event_id' => $event->id,
            'user_name' => 'John Doe',
            'num_tickets' => 0,
        ]);
        $response->assertStatus(422);
    }

    public function test_booking_non_existent_event(): void
    {
        // Act
        $response = $this->post('/api/bookings', [
            'event_id' => 999,
            'user_name' => 'John Doe',
            'num_tickets' => 2,
        ]);

        // Assert
        $response->assertStatus(422);
    }

    public function test_booking_insufficient_tickets(): void
    {
        // Arrange
        $event = Event::create([
            'name' => 'Limited Event',
            'available_tickets' => 2,
            'ticket_price' => 25.00,
        ]);

        // Act
        $response = $this->post('/api/bookings', [
            'event_id' => $event->id,
            'user_name' => 'Jane Doe',
            'num_tickets' => 5,
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Not enough tickets available',
        ]);

        // Verify no booking was created
        $this->assertDatabaseCount('bookings', 0);

        // Verify event tickets remain unchanged
        $event->refresh();
        $this->assertEquals(2, $event->available_tickets);
    }
}
