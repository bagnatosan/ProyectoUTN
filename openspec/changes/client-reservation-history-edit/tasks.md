## 1. Backend: Policy y Form Request

- [x] 1.1 Agregar método `modify()` en `ReservationPolicy` para clientes (dueño + status pending)
- [x] 1.2 Crear `UpdateReservationRequest` con reglas: product_id (existe, activo, mismo seller), reservation_date (futura, >= 2 días), reservation_time (formato, disponible), notes (nullable|max), validación custom de status pending

## 2. Backend: Controlador y Notificación

- [x] 2.1 Agregar método `edit($id)` en `ReservationController` con authorize + verificación de 2 días mínimos
- [x] 2.2 Agregar método `update($id, UpdateReservationRequest)` con transacción: liberar slot anterior, verificar nuevo slot, actualizar reserva
- [x] 2.3 Crear `ReservationModified` notification (database + mail) para el vendedor

## 3. Frontend: Vista de Historial (index.blade.php)

- [x] 3.1 Crear `resources/views/reservations/index.blade.php` con filtros por estado (JS vanilla)
- [x] 3.2 Incluir cards responsivas con foto producto, nombre, fecha/hora, estado, precio, emprendedor
- [x] 3.3 Botón "Modificar" condicional (visible solo en pending con >= 2 días de anticipación)
- [x] 3.4 Mantener funcionalidad de cancelación con modal

## 4. Frontend: Vista de Edición (edit.blade.php)

- [x] 4.1 Crear `resources/views/reservations/edit.blade.php` con formulario pre-cargado
- [x] 4.2 Selector de producto (mismo vendedor, solo activos, pre-seleccionado)
- [x] 4.3 Selector de fecha con input date (min = today+2)
- [x] 4.4 Carga dinámica de horarios vía Fetch API al cambiar fecha
- [x] 4.5 Envío del formulario con validación frontend + manejo de errores

## 5. CSS y Rutas

- [x] 5.1 Crear `public/css/sections/client-reservations.css` con BEM (prefijo `cr-`)
- [x] 5.2 Agregar rutas en `web.php`: GET /reservations/{reservation}/edit, PUT /reservations/{reservation} (bajo middleware client)
- [x] 5.3 Modificar `ReservationController@index` para usar nueva vista `reservations.index`
