## Why

El módulo de disponibilidad (`AvailabilitySlot`) tenía estilos CSS embebidos en el Blade, JS desorganizado en `reservations/` con nomenclatura inconsistente, y un bug de índices duplicados que causaba pérdida de datos al enviar múltiples slots por día sin JavaScript. Se refactoriza para alinear con los estándares del proyecto: CSS nativo con BEM, JS Vanilla en IIFE, y arquitectura de archivos limpia.

## What Changes

- **CSS extraído** del `<style>` inline del Blade a `public/css/sections/availability.css` con metodología BEM y Mobile-First
- **JS migrado** de `public/js/reservations/availability-editor.js` a `public/js/sections/availability.js` con patrón IIFE y `'use strict'`
- **Bug corregido**: índices de `name` duplicados entre días reemplazados por contador global `$globalIndex` en Blade
- **Archivo obsoleto eliminado**: `public/js/reservations/availability-editor.js`
- **Vista actualizada**: clases migradas a BEM, enlaces a CSS/JS externos, sin estilos inline

## Capabilities

### New Capabilities

- `availability-ui`: Módulo frontend de disponibilidad con CSS nativo BEM y JS Vanilla IIFE, Mobile-First, integrado con el layout existente

### Modified Capabilities

*Sin cambios en specs existentes (solo refactorización de frontend, no hay cambios de comportamiento).*

## Impact

- `resources/views/availability/edit.blade.php`: eliminado bloque `<style>` (−299 líneas), agregados enlaces a CSS/JS externos, corregido bug de índices
- `public/css/sections/availability.css`: **nuevo** (280 líneas)
- `public/js/sections/availability.js`: **nuevo** (200 líneas)
- `public/js/reservations/availability-editor.js`: **eliminado**
- `app/Http/Controllers/AvailabilitySlotController.php`: sin cambios
