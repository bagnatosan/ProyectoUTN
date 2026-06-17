## 1. Backend — Controlador y Rutas

- [x] 1.1 Agregar método `ReservationController@manage()` que renderiza `reservations.manage`
- [x] 1.2 Agregar método `ReservationController@getReservations()` con filtros today/tomorrow/week/month
- [x] 1.3 Modificar `ReservationController@updateStatus()` para responder JSON en peticiones AJAX
- [x] 1.4 Actualizar rutas: eliminar `/dashboard` y `/dashboard/reservations`, agregar `/reservations/manage` y `/reservations/manage/data`

## 2. Frontend — Vista Blade

- [x] 2.1 Crear `resources/views/reservations/manage.blade.php` con header, filtros, loader, empty state, grid
- [x] 2.2 Enlazar CSS (`reservations.css`) y JS (`reservations-manager.js`) desde la vista

## 3. CSS Nativo con BEM

- [x] 3.1 Crear `public/css/sections/reservations.css` con bloque `.reservations-manage`
- [x] 3.2 Implementar Mobile-First (1 columna mobile, grid auto-fill en desktop 640px+)
- [x] 3.3 Definir variables CSS `--rm-*` para el theme oscuro
- [x] 3.4 Estilos de badge por estado (pending=amarillo, confirmed=azul, completed=verde, cancelled=rojo)
- [x] 3.5 Estilos para card, avatar, info, notas, selector, toast

## 4. JavaScript Module Pattern

- [x] 4.1 Crear `public/js/sections/reservations-manager.js` con `reservationsManager` module
- [x] 4.2 Implementar carga asíncrona de pedidos con Fetch API
- [x] 4.3 Implementar filtros con cambio dinámico sin recargar
- [x] 4.4 Implementar actualización de estado vía PATCH con CSRF token
- [x] 4.5 Implementar toast de feedback (éxito/error)
- [x] 4.6 Implementar renderizado de tarjetas con datos del cliente, producto, fecha y notas

## 5. Navegación y limpieza

- [x] 5.1 Actualizar navbar: cambiar "Dashboard" por "Pedidos" apuntando a `route('reservations.manage')`
- [x] 5.2 Eliminar vista `resources/views/dashboard/index.blade.php`
- [x] 5.3 Eliminar `public/css/sections/dashboard.css`
- [x] 5.4 Eliminar `public/js/sections/dashboard.js`
