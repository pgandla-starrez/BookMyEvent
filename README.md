# BookMyEvent: Event Ticket Booking API 

## 1. Goal

In this 30-40 minute live coding session, you will implement two core API endpoints for a simplified online ticket booking system using PHP and the Laravel framework. The focus will be on implementing the logic correctly, writing clean code, and creating unit tests for the booking functionality.

**Prerequisites:** Please ensure you have PHP, Laravel (via Composer), your preferred IDE (e.g., VS Code), and any necessary database tools (SQLite is recommended) installed and ready before the session.

## 2. Overview

We'll focus on a specific event and the process of booking tickets for it.

## 3. Core Functionalities

1.  **View a specific event:** Get details for a single event.
2.  **Book tickets for an event:** Create a new booking, ensuring ticket availability and updating the count.

## 4. Data Models

* **Event:**
    * `id` (integer, primary key)
    * `name` (string)
    * `available_tickets` (integer)
    * `ticket_price` (decimal/float)

* **Booking:**
    * `id` (integer, primary key)
    * `event_id` (integer, foreign key to Event)
    * `user_name` (string) 
    * `num_tickets` (integer)
    * `total_amount` (decimal/float)

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
        * **400 Bad Request (or 422):** If `num_tickets` requested exceeds `available_tickets`.
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
* **Code Quality:** Focus on clean, readable code. Use a Service class for the booking logic.
* **Validation:** Implement basic input validation for the `POST /api/bookings` request.
* **Concurrency Handling:** Ensure the decrement of `available_tickets` and creation of a booking occur within a database transaction.

## 7. Testing Requirements (Focus for this session)

* **Unit Tests:** Write unit tests primarily for the service class method that handles the booking logic. Focus on:
    * Successful booking and `available_tickets` decrement.
    * Attempting to book more tickets than available.
    * Calculation of `total_amount`.
    * (If time permits, test edge cases for `num_tickets`, like 0 or negative).

## 8. What to Skip (Due to time constraints)

* Listing all events/bookings.
* Updating or deleting events/bookings.
* Complex error response structures (simple JSON message is fine).
* Extensive README documentation (verbal explanation is fine).
* Authentication/Authorization.
* Bonus features from the longer exercise.
* Full feature/integration tests for API endpoints (focus on unit tests for logic).

---

During the session, please explain your thought process as you code. Good luck!
