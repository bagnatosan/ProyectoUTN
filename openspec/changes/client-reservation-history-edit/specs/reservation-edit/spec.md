## ADDED Requirements

### Requirement: Client can edit a pending reservation
The system SHALL allow a client to modify their reservation if it is in `pending` status and the reservation date is at least 2 days in the future.

#### Scenario: Access edit form
- **WHEN** a client navigates to `/reservations/{id}/edit` for their own reservation
- **AND** the reservation status is `pending`
- **AND** the reservation date is >= 2 days from today
- **THEN** the system SHALL display the edit form with current values pre-filled

#### Scenario: Access denied - wrong owner
- **WHEN** a client navigates to `/reservations/{id}/edit` for a reservation belonging to another user
- **THEN** the system SHALL return 403 Forbidden

#### Scenario: Access denied - not pending
- **WHEN** a client navigates to `/reservations/{id}/edit` for a reservation with status `confirmed`, `completed`, or `cancelled`
- **THEN** the system SHALL redirect back with an error message: "Solo se pueden modificar reservas pendientes"

#### Scenario: Access denied - less than 2 days
- **WHEN** a client navigates to `/reservations/{id}/edit` for a reservation whose date is less than 2 days from today
- **THEN** the system SHALL redirect back with an error message: "No podés modificar una reserva con menos de 2 días de anticipación"

### Requirement: Edit form has product, date, time, and notes fields
The edit form SHALL contain a product selector (same seller), a date picker (future dates only), dynamic time slots loaded via Fetch API, and a notes field.

#### Scenario: Product selector shows same-seller products
- **WHEN** the edit form loads
- **THEN** the product selector SHALL display only active products from the same seller (business profile) as the original reservation
- **AND** the current product SHALL be pre-selected

#### Scenario: Date picker restricts to future dates
- **WHEN** the client opens the date picker
- **THEN** only today and future dates SHALL be selectable

#### Scenario: Time slots load dynamically
- **WHEN** the client selects or changes the date
- **THEN** the system SHALL fetch available slots via GET `/available-slots/{sellerId}/{date}`
- **AND** display them as selectable time buttons
- **AND** show a loading indicator while fetching
- **AND** show "No hay horarios disponibles" if none available

#### Scenario: Time slots exclude current reservation's slot
- **WHEN** fetching available slots for the edit form
- **THEN** the current reservation's time SHALL be included as available (since it's the client's own slot)
- **AND** if the client changes the date, only truly available slots SHALL be shown

### Requirement: Update reservation validates all business rules
The system SHALL validate all business rules when a client submits the edit form.

#### Scenario: Successful update
- **WHEN** a client submits the edit form with valid data
- **AND** the new slot is available
- **AND** the reservation is still pending
- **AND** the date is at least 2 days from today
- **THEN** the system SHALL update the reservation with the new product_id, reservation_date, reservation_time, and notes
- **AND** the system SHALL send a `ReservationModified` notification to the seller
- **AND** redirect to `/my-reservations` with a success message

#### Scenario: Slot no longer available
- **WHEN** a client submits the edit form
- **AND** the selected time slot is already taken by another reservation
- **THEN** the system SHALL reject with an error: "El horario seleccionado ya no está disponible"

#### Scenario: Validation errors
- **WHEN** a client submits with invalid data (missing product, past date, invalid time)
- **THEN** the system SHALL display validation errors on the form
- **AND** the form SHALL retain the submitted values

### Requirement: Seller receives notification on modification
The system SHALL notify the seller when a client modifies a reservation.

#### Scenario: Seller receives database notification
- **WHEN** a reservation is successfully modified by the client
- **THEN** the seller SHALL receive a database notification with: reservation_id, product name, old date/time, new date/time, and a message "Reserva modificada por el cliente"

#### Scenario: Seller receives email notification
- **WHEN** a reservation is successfully modified by the client
- **THEN** the seller SHALL receive an email notification with the modification details
