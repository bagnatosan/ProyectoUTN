## 1. Estructura de archivos

- [x] 1.1 Crear directorios `public/css/sections/` y `public/js/sections/`
- [x] 1.2 Eliminar archivo legacy `public/js/reservations/availability-editor.js`

## 2. CSS Nativo con BEM

- [x] 2.1 Crear `public/css/sections/availability.css` con BEM (bloque `.availability`)
- [x] 2.2 Definir variables CSS `--av-*` para el theme oscuro
- [x] 2.3 Implementar Mobile-First: filas como tarjetas verticales en mobile
- [x] 2.4 Agregar Media Query para escritorio (768px+): filas horizontales
- [x] 2.5 Posicionar botón eliminar absoluto en mobile, estático en desktop
- [x] 2.6 Estilos para feedback, empty state, botones y footer del formulario

## 3. JavaScript Vanilla IIFE

- [x] 3.1 Crear `public/js/sections/availability.js` con patrón IIFE y `'use strict'`
- [x] 3.2 Inicializar listeners al `DOMContentLoaded`
- [x] 3.3 Implementar `addSlot(day)` con `cloneNode(true)` y `data-index` secuencial
- [x] 3.4 Implementar `removeSlot(btn)` con `.closest()` y alert si es la última fila
- [x] 3.5 Implementar `reindex()` con índices correlativos antes del submit
- [x] 3.6 Implementar validación client-side (end > start, sin solapamientos)
- [x] 3.7 Implementar `showFeedback()` para errores de validación

## 4. Blade View

- [x] 4.1 Eliminar bloque `<style>` inline del template
- [x] 4.2 Agregar `<link>` a `availability.css`
- [x] 4.3 Actualizar script src a `{{ asset('js/sections/availability.js') }}`
- [x] 4.4 Migrar clases a BEM (`.day-section` → `.availability__day-section`, etc.)
- [x] 4.5 Corregir bug de índices duplicados con contador `$globalIndex`
- [x] 4.6 Limpiar caché de vistas (`php artisan view:clear`)
