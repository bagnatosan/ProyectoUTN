## Why

Los vendedores necesitan un panel para visualizar y gestionar las reservas de sus productos sin recargar la página. El proyecto ya contaba con `ReservationController` y el modelo `Reservation`, pero no existía una interfaz unificada para que el vendedor vea sus pedidos, los filtre por fecha y cambie estados de forma asíncrona.

## What Changes

- **Nueva vista** `reservations/manage.blade.php` con grid de tarjetas, filtros temporales (today/tomorrow/week/month), loader y empty state
- **Nuevo endpoint JSON** `GET /reservations/manage/data` que devuelve reservas del vendedor autenticado filtradas por fecha
- **Modificado `updateStatus()`** existente para responder en JSON cuando la petición es AJAX
- **Nuevo método `manage()`** en `ReservationController` para renderizar la vista
- **CSS nativo** con BEM (`reservations-manage__*`), Mobile-First, colores por estado
- **JS Vanilla** con Module Pattern (`reservationsManager`), Fetch API y actualización visual sin recarga
- **Navbar actualizada**: link "Dashboard" reemplazado por "Pedidos" apuntando a `reservations.manage`

## Capabilities

### New Capabilities

- `seller-reservations-ui`: Interfaz de gestión de pedidos para vendedores con grid asíncrono, filtros, cambio de estado en un clic, CSS BEM y JS Module Pattern

### Modified Capabilities

*Sin cambios en specs existentes.*

## Impact

- `routes/web.php`: eliminadas rutas `/dashboard` y `/dashboard/reservations`; agregadas `GET /reservations/manage` y `GET /reservations/manage/data`
- `app/Http/Controllers/ReservationController.php`: agregado método `manage()` y modificado `updateStatus()` para JSON
- `app/Http/Controllers/DashboardController.php`: sin cambios funcionales (ruta eliminada)
- `resources/views/reservations/manage.blade.php`: **nuevo**
- `resources/views/layouts/app.blade.php`: navbar link actualizado
- `public/css/sections/reservations.css`: **nuevo**
- `public/js/sections/reservations-manager.js`: **nuevo**
- `resources/views/dashboard/index.blade.php`: **eliminado**
- `public/css/sections/dashboard.css`: **eliminado**
- `public/js/sections/dashboard.js`: **eliminado**
