## Context

El módulo `AvailabilitySlot` tenía los estilos CSS embebidos en el template Blade (`<style>` inline de ~300 líneas) y el JavaScript en `public/js/reservations/availability-editor.js` con un patrón de módulo (`AvailabilityEditor` como objeto global) pero sin `'use strict'` y con IDs aleatorios (`data-slot-id="slot-0"`) en lugar de índices numéricos secuenciales. Además, los nombres de inputs usaban `$loop->index` que se reiniciaba por día, causando duplicados en el array PHP.

La refactorización unifica la arquitectura frontend del módulo bajo los directorios `public/css/sections/` y `public/js/sections/`, siguiendo la convención BEM y Mobile-First del proyecto.

## Goals / Non-Goals

**Goals:**
- Extraer todo CSS inline a archivo dedicado con BEM unificado
- Migrar JS a IIFE con `'use strict'` y `data-index` numérico secuencial
- Corregir bug de índices duplicados entre días en los `name` del formulario
- Eliminar el archivo JS legacy en `reservations/`
- Mantener la estructura visual y comportamiento actual (organización por día)

**Non-Goals:**
- Cambiar la lógica del controlador o el motor de disponibilidad
- Modificar el modelo `AvailabilitySlot` o la migración
- Alterar las rutas o la estructura de navegación
- Migrar la estructura de días (string) a numérica (0-6)
- Agregar nuevas funcionalidades

## Decisions

1. **BEM con raíz `.availability` en vez de clases planas**
   - *Alternativa considerada*: mantener `.day-section`, `.slot-row` como estaban sin namespacing
   - *Decisión*: se unifica bajo `availability__` para evitar colisiones con otros módulos y alinear con la convención BEM descripta en CONTEXT.md
   - .day-section → .availability__day-section
   - .slot-row → .availability__slot-row
   - .btn-add-slot → .availability__btn-add
   - .slot-remove → .availability__btn-remove
   - .btn-save → .availability__btn-save
   - .form-feedback → .availability__feedback
   - .empty-day → .availability__empty-day

2. **IIFE en vez de objeto global expuesto**
   - *Alternativa considerada*: mantener `AvailabilityEditor` como variable global
   - *Decisión*: IIFE con escucha `DOMContentLoaded` para evitar contaminación global. La API pública ya no es necesaria porque no hay otro script que consuma `AvailabilityEditor`.

3. **`data-index` numérico secuencial en vez de `data-slot-id` con ID aleatorio**
   - *Alternativa considerada*: mantener IDs únicos (slot-0, slot-1) y reindexar solo al enviar
   - *Decisión*: el `data-index` numérico simplifica la lógica de reindexación y hace el DOM más legible. Coincide 1:1 con el índice del array `slots[N]` que recibe PHP.

4. **Contador `$globalIndex` en Blade para eliminar dependencia de JS**
   - *Alternativa considerada*: mantener `$loop->index` por día y confiar en `reindex()` JS
   - *Decisión*: el contador PHP garantiza names únicos incluso si JS falla. Es más robusto y elimina el bug de sobreescritura en el backend.

5. **Variables CSS en el archivo en vez de valores hardcodeados**
   - *Alternativa considerada*: mantener los valores de color directos como estaban
   - *Decisión*: usar variables `--av-*` que referencian los mismos colores del theme oscuro (slate, green, indigo, rose) para facilitar mantenimiento y re-theme futuro.

## Risks / Trade-offs

- **[Regresión visual]** Los estilos BEM renombrados pueden no coincidir exactamente si algún selector no se mapeó correctamente. *Mitigación:* revisión visual post-despliegue y comparación con captura previa.
- **[Cache de Blade]** La vista compilada anterior (`storage/framework/views/`) referenciaba el JS legacy. *Mitigación:* ya se ejecutó `php artisan view:clear`.
- **[JS legacy referenciado]** Si otra vista importaba `availability-editor.js`, se rompería. *Mitigación:* se verificó con grep que ninguna otra vista o archivo lo referenciaba.
