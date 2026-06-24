## Why

Los clientes registrados actualmente solo pueden ver su historial de reservas en una vista básica sin filtros ni acciones. No existe la posibilidad de modificar una reserva —si necesitan cambiar producto, fecha u horario deben cancelar y crear una nueva. Esto genera fricción, pérdida de trazabilidad y mala experiencia de usuario.

## What Changes

- **Nueva vista "Historial del Cliente"** con filtros por estado (pendiente, confirmada, completada, cancelada), cards responsivas con foto del producto, fecha/hora, estado, precio, nombre del emprendedor, y botón "Modificar" condicional.
- **Nueva vista "Editar Reserva"** con selector de producto (mismo vendedor), selector de fecha (solo futuras), horarios dinámicos vía Fetch API, y campo de notas.
- **Nuevo método `edit()` y `update()`** en ReservationController para clientes autenticados.
- **Nuevo Form Request** `UpdateReservationRequest` para validación backend.
- **Nueva Notificación** `ReservationModified` para notificar al vendedor.
- **Nuevo permiso en Policy** para permitir modificación solo al dueño de la reserva en estado `pending`.
- **Liberación automática del slot anterior** al modificar fecha/hora.

## Capabilities

### New Capabilities
- `reservation-history`: Vista de historial del cliente con filtros, cards informativas y botón de modificación condicional.
- `reservation-edit`: Formulario completo de edición de reserva con validación de disponibilidad, cambio de producto/fecha/hora/notas, y notificación al vendedor.

### Modified Capabilities

_(none — no existing specs are being changed)_

## Impact

- **Controllers**: `ReservationController.php` — agregar `edit()` y `update()`
- **Routes**: `web.php` — agregar rutas `GET /reservations/{reservation}/edit` y `PUT /reservations/{reservation}`
- **Policies**: `ReservationPolicy.php` — agregar método `modify()`
- **Form Requests**: nuevo `UpdateReservationRequest.php`
- **Notifications**: nueva `ReservationModified.php`
- **Views**: nuevos `reservations/index.blade.php` y `reservations/edit.blade.php`
- **CSS**: nuevo `public/css/sections/client-reservations.css`
- **JS**: nuevo `public/js/reservations/edit-reservation-form.js`
