---
capability: seller-reservations-ui
description: Interfaz de gestión de pedidos para vendedores con grid asíncrono, filtros temporales, cambio de estado en un clic y CSS BEM Mobile-First
---

# Seller Reservations UI

## Purpose

Define los requisitos de frontend y backend para que los vendedores puedan visualizar y gestionar las reservas de sus productos desde un panel unificado, con comunicación asíncrona vía Fetch API, filtros temporales y actualización de estados sin recarga de página.

## Requirements

### Requirement: Vista de gestión de pedidos del vendedor
El sistema SHALL proveer una vista accesible en `GET /reservations/manage` donde el vendedor pueda visualizar y gestionar las reservas de sus productos.

#### Scenario: Acceso a la vista de gestión
- **WHEN** el vendedor autenticado visita `/reservations/manage`
- **THEN** se renderiza la vista `reservations.manage` con filtros temporales, loader y grid de tarjetas

#### Scenario: Filtros temporales
- **WHEN** el vendedor hace clic en un filtro (Hoy, Mañana, Semana, Mes)
- **THEN** se envía una petición GET a `/reservations/manage/data?filter=<valor>` vía Fetch API
- **THEN** el grid se actualiza con los resultados sin recargar la página
- **THEN** el filtro activo cambia visualmente (clase `--active`)

### Requirement: Endpoint JSON de reservas del vendedor
El sistema SHALL exponer un endpoint `GET /reservations/manage/data` que devuelva las reservas de los productos del vendedor autenticado en formato JSON.

#### Scenario: Filtro "today"
- **WHEN** se solicita `/reservations/manage/data?filter=today`
- **THEN** se devuelven solo reservas con `reservation_date` igual a la fecha actual

#### Scenario: Filtro "tomorrow"
- **WHEN** se solicita `/reservations/manage/data?filter=tomorrow`
- **THEN** se devuelven solo reservas con `reservation_date` igual al día siguiente

#### Scenario: Filtro "week"
- **WHEN** se solicita `/reservations/manage/data?filter=week`
- **THEN** se devuelven reservas con `reservation_date` entre el inicio y fin de la semana actual

#### Scenario: Filtro "month"
- **WHEN** se solicita `/reservations/manage/data?filter=month`
- **THEN** se devuelven reservas con `reservation_date` entre el inicio y fin del mes actual

#### Scenario: Respuesta JSON exitosa
- **WHEN** hay reservas que coinciden con el filtro
- **THEN** la respuesta tiene `{ "success": true, "data": [...] }`

#### Scenario: Sin perfil de negocio
- **WHEN** el vendedor no tiene `BusinessProfile`
- **THEN** la respuesta tiene `{ "success": false, "message": "..." }` con código 403

#### Scenario: Propiedad de datos
- **WHEN** se devuelven reservas
- **THEN** solo incluye reservas cuyo `product.business_profile_id` coincida con el `BusinessProfile` del vendedor autenticado

### Requirement: Actualización asíncrona de estado
El sistema SHALL permitir cambiar el estado de una reserva desde el panel de gestión sin recargar la página, usando `PATCH /reservations/{id}/status` con `Content-Type: application/json`.

#### Scenario: Cambio de estado exitoso
- **WHEN** el vendedor selecciona un nuevo estado en el selector de una tarjeta
- **THEN** se envía PATCH con `{ "status": "<nuevo>" }` y headers `X-CSRF-TOKEN` y `Accept: application/json`
- **THEN** la tarjeta se actualiza visualmente (badge, clase de color)
- **THEN** se muestra un toast de éxito

#### Scenario: Error de propiedad
- **WHEN** se intenta cambiar el estado de una reserva que no pertenece a un producto del vendedor
- **THEN** la respuesta tiene `{ "success": false, "message": "..." }` con código 403
- **THEN** el selector vuelve al estado anterior

#### Scenario: Estado inválido
- **WHEN** se envía un estado no válido (no es pending/confirmed/completed/cancelled)
- **THEN** la respuesta tiene errores de validación

### Requirement: UI de tarjetas con estados visuales
Cada reserva SHALL mostrarse como una tarjeta con información del cliente, producto, fecha, hora, notas y un selector de estado. Los estados SHALL tener colores distintivos.

#### Scenario: Colores por estado
- **WHEN** el estado es `pending`
- **THEN** el badge tiene clase `reservations-manage__badge--pending` (amarillo)
- **WHEN** el estado es `confirmed`
- **THEN** el badge tiene clase `reservations-manage__badge--confirmed` (azul)
- **WHEN** el estado es `completed`
- **THEN** el badge tiene clase `reservations-manage__badge--completed` (verde)
- **WHEN** el estado es `cancelled`
- **THEN** el badge tiene clase `reservations-manage__badge--cancelled` (rojo)

#### Scenario: Grid responsive
- **WHEN** la pantalla es menor a 640px
- **THEN** el grid muestra una columna
- **WHEN** la pantalla es de 640px o más
- **THEN** el grid usa `grid-template-columns: repeat(auto-fill, minmax(300px, 1fr))`
