## Context

El proyecto tiene un `ReservationController` con método `index()` que retorna `reservations.client_history` (vista básica sin filtros, sin botón de modificar). No existe lógica de edición de reservas para el cliente. El `AvailabilityService` ya provee `getAvailableSlots()` y `isSlotAvailable()`. El endpoint `/available-slots/{seller}/{date}` ya existe. Se necesita añadir historial con filtros y edición completa de reservas.

## Goals / Non-Goals

**Goals:**
- Vista de historial del cliente con filtros por estado y cards responsivas
- Vista de edición de reserva con selector de producto, fecha, horarios dinámicos
- Validación completa: solo pending, mínimo 2 días de anticipación, disponibilidad del nuevo slot
- Liberación automática del slot anterior
- Notificación al vendedor cuando se modifica una reserva
- Mobile-first, CSS nativo con BEM, JS vanilla

**Non-Goals:**
- No se modifica la vista existente `client_history.blade.php` (se crea nueva)
- No se modifican las migraciones ni el schema de base de datos
- No se agregan dependencias externas

## Decisions

### 1. Reutilizar el endpoint `/available-slots/{seller}/{date}` existente
En lugar de crear un nuevo endpoint, se reutiliza el existente `AvailabilityController@availableSlots` que ya devuelve slots libres en formato JSON. El JS del formulario de edición hará fetch a este endpoint.

### 2. Nueva vista `index.blade.php` sin reemplazar `client_history.blade.php`
Se crea una vista completamente nueva en `reservations/index.blade.php` para el historial con filtros. El método `index()` del controlador se modifica para apuntar a la nueva vista.

### 3. Nueva notificación `ReservationModified`
Se crea una notificación específica para modificaciones (no se reutiliza `ReservationCancelled`) ya que el mensaje, asunto y contenido son diferentes. Se notifica al vendedor con los detalles del cambio.

### 4. Transacción DB con lockForUpdate para evitar race conditions
El `update()` usa una transacción que primero verifica disponibilidad del nuevo slot con `lockForUpdate()` (vía `AvailabilityService::isSlotAvailable()`), y si es exitoso actualiza la reserva. Esto garantiza consistencia bajo concurrencia.

### 5. Validación en `UpdateReservationRequest`
Toda la lógica de validación (estado pending, anticipación mínima 2 días, pertenencia al usuario, disponibilidad del nuevo slot) se centraliza en un Form Request para mantener el controlador limpio y reutilizar reglas.

### 6. CSS separado en `public/css/sections/client-reservations.css`
Sigue el patrón existente de CSS seccionado (ej: `reservations.css`, `seller-reservations.css`). Usa BEM con prefijo `cr-` (client-reservations).

## Risks / Trade-offs

| Riesgo | Mitigación |
|--------|-----------|
| Race condition si dos clientes modifican al mismo tiempo | `isSlotAvailable()` usa `lockForUpdate()` dentro de transacción |
| Cliente modifica y luego otro cliente reserva el slot liberado | La transacción primero libera el slot anterior (al actualizar la reserva), luego verifica el nuevo. Si la verificación falla, rollback. El slot anterior queda liberado igual (es intencional). |
| Usuario con sesión expirada en el formulario de edición | El formulario verifica autenticación vía middleware `client`. Si expira, redirect a login. |
