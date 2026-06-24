## ADDED Requirements

### Requirement: Client can view reservation history with filters
The system SHALL display the authenticated client's complete reservation history in a dedicated view with filter tabs.

#### Scenario: View all reservations
- **WHEN** a client navigates to `/my-reservations`
- **THEN** the system SHALL display all reservations belonging to the client, ordered by date descending
- **AND** each reservation card SHALL show: product photo, product name, date, time, status badge, price, and entrepreneur name

#### Scenario: Filter by status
- **WHEN** a client clicks a filter tab (Pendientes, Confirmadas, Completadas, Canceladas)
- **THEN** the system SHALL display only reservations matching that status
- **AND** the active filter tab SHALL be visually highlighted

#### Scenario: Empty state per filter
- **WHEN** a client selects a filter with no matching reservations
- **THEN** the system SHALL display an empty state message: "No hay reservas [estado]" with a link to explore catalogs

#### Scenario: Modify button visibility
- **WHEN** a reservation has status `pending` AND its date is at least 2 days in the future
- **THEN** the system SHALL display a "Modificar" button on that reservation card
- **AND** the button SHALL link to `/reservations/{id}/edit`

#### Scenario: Modify button hidden
- **WHEN** a reservation has status other than `pending` OR its date is less than 2 days from now
- **THEN** the system SHALL NOT display the "Modificar" button

### Requirement: Client can cancel a reservation from history
The system SHALL allow the client to cancel a cancellable reservation directly from the history view.

#### Scenario: Cancel from history
- **WHEN** a client clicks "Cancelar" on a reservation card that has status `pending` or `confirmed`
- **THEN** the system SHALL show a confirmation modal with reason field
- **AND** upon confirmation, cancel the reservation and refresh the list
