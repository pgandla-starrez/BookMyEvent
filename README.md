# BookMyEvent: Event Ticket Booking API

## Goal

In this 30-40 minute live coding session, you will implement two core API endpoints for a simplified online ticket booking system using PHP and the Laravel framework. The focus will be on implementing the logic correctly, writing clean code, and creating unit tests for the booking functionality.

**Prerequisites:** Please ensure you have the following installed and ready:
* PHP 8.1+ with SQLite extension enabled
* SQLite database (comes with most PHP installations)
* Composer for dependency management
* Laravel CLI (`composer global require laravel/installer`)
* Your preferred IDE (e.g., VS Code with PHP extensions)
* Basic Laravel project created (`laravel new bookmyevent` or provide skeleton)

## 2. Overview

We'll focus on a specific event and the process of booking tickets for it.

## 3. Core Functionalities

1.  **View a specific event:** Get details for a single event.
2.  **Book tickets for an event:** Create a new booking, ensuring ticket availability and updating the count.

## 4. Data Models

* **Event:**
    * `id` (integer, primary key, auto-increment)
    * `name` (string, varchar(255))
    * `available_tickets` (integer, unsigned)
    * `ticket_price` (decimal(8,2))
    * `created_at` (timestamp)
    * `updated_at` (timestamp)

* **Booking:**
    * `id` (integer, primary key, auto-increment)
    * `event_id` (integer, unsigned, foreign key to Event)
    * `user_name` (string, varchar(255))
    * `num_tickets` (integer, unsigned)
    * `total_amount` (decimal(10,2))
    * `created_at` (timestamp)
    * `updated_at` (timestamp)

**Laravel Relationships:**
* Event: `hasMany(Booking::class)`
* Booking: `belongsTo(Event::class)`

**Key Logic:** When a booking is made, `available_tickets` for the Event must decrease. This operation should be handled safely (e.g., within a database transaction).

## 5. API Endpoints (To Implement)

---

1.  **`GET /api/events/{id}` - Get Specific Event Details**
    * **Description:** Returns key details for a single event.
    * **Success Response (200 OK):**
        ```json
        {
            "id": 1,
            "name": "Tech Conference 2025",
            "available_tickets": 50,
            "ticket_price": 75.00
        }
        ```
    * **Error Response (404 Not Found):** If the event doesn't exist.

2.  **`POST /api/bookings` - Create a New Booking**
    * **Description:** Books tickets for a specific event.
    * **Request Body:**
        ```json
        {
            "event_id": 1,
            "user_name": "Test User",
            "num_tickets": 2
        }
        ```
    * **Success Response (201 Created):**
        ```json
        {
            "id": 123, // Booking ID
            "event_id": 1,
            "user_name": "Test User",
            "num_tickets": 2,
            "total_amount": 150.00
        }
        ```
    * **Error Responses:**
        * **422 Unprocessable Entity:** For validation errors (e.g., `num_tickets` not positive, `event_id` missing).
        * **422 Unprocessable Entity:** If `num_tickets` requested exceeds `available_tickets`.
            ```json
            {
                "message": "Not enough tickets available."
            }
            ```
        * **404 Not Found:** If `event_id` does not exist.

## 6. Technical Requirements (Focus for this session)

* **Language/Framework:** PHP with Laravel.
* **Database:** Use SQLite. Please create necessary migrations for `events` and `bookings` tables. A simple seeder for 1-2 events is helpful.
* **API Design:** Use appropriate HTTP verbs and status codes.
* **Code Quality:** Focus on clean, readable code. Use a Service class (e.g., `app/Services/BookingService.php`) for the booking logic.
* **Routes:** Define API routes in `routes/api.php`.
* **Validation:** Implement Laravel Form Request validation for the `POST /api/bookings` request with rules:
  * `event_id`: required, exists:events,id
  * `user_name`: required, string, max:255
  * `num_tickets`: required, integer, min:1
* **Concurrency Handling:** Ensure the decrement of `available_tickets` and creation of a booking occur within a database transaction.
* **Models:** Create Eloquent models with proper relationships defined.

## 7. Testing Requirements (Focus for this session)

* **Unit Tests:** Write unit tests for the `BookingService` class in `tests/Unit/BookingServiceTest.php`. Focus on:
    * Successful booking and `available_tickets` decrement.
    * Attempting to book more tickets than available.
    * Calculation of `total_amount`.
    * Exception handling for non-existent events.
    * (If time permits, test edge cases for `num_tickets`, like 0 or negative).
* **Test Setup:** Use Laravel's database testing features with `RefreshDatabase` trait.
* **Test Structure:** Create at least 3-4 test methods covering the main scenarios.

## 8. Implementation Guidance

**Suggested Development Order:**
1. Create migrations and models (5-8 minutes)
2. Seed sample data (2-3 minutes)
3. Create EventController for GET endpoint (5-7 minutes)
4. Create BookingService class (8-10 minutes)
5. Create BookingController for POST endpoint (5-7 minutes)
6. Write unit tests for BookingService (10-12 minutes)

**Sample Seeder Data:**
```php
Event::create([
    'name' => 'Tech Conference 2025',
    'available_tickets' => 100,
    'ticket_price' => 75.00
]);

Event::create([
    'name' => 'Music Festival',
    'available_tickets' => 50,
    'ticket_price' => 120.00
]);
```

## 9. What to Skip (Due to time constraints)

* Listing all events/bookings.
* Updating or deleting events/bookings.
* Complex error response structures (simple JSON message is fine).
* Extensive README documentation (verbal explanation is fine).
* Authentication/Authorization.
* Bonus features from the longer exercise.
* Full feature/integration tests for API endpoints (focus on unit tests for logic).

---

During the session, please explain your thought process as you code. Good luck!
