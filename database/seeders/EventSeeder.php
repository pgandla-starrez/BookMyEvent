<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::create([
            'name' => 'Tech Conference 2025',
            'available_tickets' => 100,
            'ticket_price' => 75.00,
        ]);

        Event::create([
            'name' => 'Music Festival',
            'available_tickets' => 50,
            'ticket_price' => 120.00,
        ]);
    }
}
