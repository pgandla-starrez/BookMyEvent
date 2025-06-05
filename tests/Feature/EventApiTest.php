<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_event_details(): void
    {
        // Arrange
        $event = Event::create([
            'name' => 'Test Conference',
            'available_tickets' => 75,
            'ticket_price' => 99.50,
        ]);

        // Act
        $response = $this->get("/api/events/{$event->id}");

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $event->id,
            'name' => 'Test Conference',
            'available_tickets' => 75,
            'ticket_price' => 99.50,
        ]);
    }

    public function test_returns_404_for_non_existent_event(): void
    {
        // Act
        $response = $this->get('/api/events/999');

        // Assert
        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Event not found'
        ]);
    }
}
