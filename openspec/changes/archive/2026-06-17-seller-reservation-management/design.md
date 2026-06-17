## Context

El proyecto ya tenía `ReservationController` con métodos `create()`, `store()`, `clientHistory()` y `updateStatus()`. También existía `DashboardController` (stub) y una vista `dashboard/index.blade.php` con placeholder. No había interfaz para que el vendedor gestione sus pedidos de forma unificada.

Se implementó la gestión de pedidos directamente en `ReservationController` (sin crear un controlador nuevo) y se migró toda la UI del namespace "dashboard" al namespace "reservations".

## Goals / Non-Goals

**Goals:**
- Proveer interfaz de gestión de pedidos para vendedores con cards y selector de estado
- Comunicación asíncrona vía Fetch API (sin recargas de página)
- Filtros temporales: today, tomorrow, week, month
- CSS BEM Mobile-First con colores distintivos por estado
- Reutilizar `ReservationController` y modelo `Reservation` existentes

**Non-Goals:**
- Crear un nuevo controlador o modelo
- Modificar la lógica de negocio del lado del cliente (create/store)
- Agregar métricas o analytics (se abordará separadamente)
- Cambiar el motor de disponibilidad o reservas públicas
- Pasarela de pago o notificaciones

## Decisions

1. **Namespace "reservations" en vez de "dashboard"**
   - La funcionalidad es gestión de pedidos, no un dashboard genérico. Se aloja bajo `/reservations/manage` y los archivos se nombran `reservations.*`.
   - El navbar cambió de "Dashboard" a "Pedidos".

2. **Module Pattern con `reservationsManager` en vez de IIFE anónima**
   - Se exporta el objeto para facilitar debugging desde consola y posible testing futuro.
   - El patrón IIFE anónimo del módulo availability es más restrictivo; acá se prioriza accesibilidad controlada.

3. **Reutilización de `updateStatus()` existente con detección AJAX**
   - En vez de duplicar lógica con un nuevo endpoint, se modificó el método existente para responder JSON cuando `$request->expectsJson()` o `$request->ajax()` es true.
   - Esto mantiene compatibilidad con llamadas tradicionales (POST de formularios) y habilita las asíncronas.

4. **Filtro por `business_profile_id` del producto en vez de `user_id`**
   - Consistente con la arquitectura existente donde los productos pertenecen a `BusinessProfile`, no directamente al `User`.
   - El vendedor autenticado accede vía `$request->user()->businessProfile`.

5. **Fetch a `PATCH /reservations/{id}/status` con CSRF token**
   - Se reutiliza la ruta existente `reservations.update-status` en vez de crear una duplicada.
   - El JS envía `X-CSRF-TOKEN` desde el meta tag del layout.

## Risks / Trade-offs

- **[Ruta /dashboard eliminada]** La ruta anterior de dashboard fue eliminada. Si algún enlace externo o bookmark apuntaba a `/dashboard`, dejará de funcionar. *Mitigación:* se actualizó el navbar y se limpiaron referencias.
- **[DashboardController sin uso]** El controlador `DashboardController` ahora no tiene rutas asociadas. Queda como stub para futuras métricas. *Mitigación:* no se eliminó para no romper imports en web.php.
- **[Cambio en updateStatus]** El método existente ahora responde JSON para AJAX. Si había consumidores que esperaban solo redirect, no se ven afectados porque se mantiene el `back()` para requests no-AJAX.
