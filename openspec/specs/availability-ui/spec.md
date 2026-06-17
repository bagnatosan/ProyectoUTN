---
capability: availability-ui
description: Frontend del módulo de disponibilidad horaria (CSS nativo BEM, JS Vanilla IIFE, Mobile-First)
---

# Availability UI

## Purpose

Define los requisitos de frontend para la configuración de la disponibilidad horaria semanal del vendedor. Cubre la presentación visual (CSS nativo con BEM), la interactividad del editor de horarios (JavaScript Vanilla) y la correcta serialización de datos del formulario (índices globales en Blade).

## Requirements

### Requirement: Visor de configuración de disponibilidad con CSS nativo BEM
El frontend del módulo de disponibilidad SHALL utilizar CSS nativo sin frameworks externos, con metodología BEM (bloque `availability`) y enfoque Mobile-First.

#### Scenario: Carga del CSS externo
- **WHEN** la vista `availability.edit` se renderiza
- **THEN** el `<head>` contiene un `<link>` a `{{ asset('css/sections/availability.css') }}`

#### Scenario: BEM en estructura mobile
- **WHEN** la vista se visualiza en pantallas menores a 768px
- **THEN** cada fila de horario (`.availability__slot-row`) se muestra como tarjeta vertical con `flex-direction: column`
- **THEN** el botón de eliminar (`.availability__btn-remove`) se posiciona en la esquina superior derecha con `position: absolute`

#### Scenario: BEM en estructura desktop
- **WHEN** la vista se visualiza en pantallas de 768px o más
- **THEN** cada fila de horario se muestra en horizontal con `flex-direction: row`
- **THEN** el botón de eliminar pierde el posicionamiento absoluto y se alinea verticalmente

#### Scenario: Variables CSS para consistencia visual
- **WHEN** se aplican los estilos del módulo
- **THEN** los colores se definen mediante variables `--av-*` (fondo, borde, texto, primario, peligro, etc.)

### Requirement: Editor de disponibilidad con JavaScript Vanilla IIFE
El editor de horarios SHALL implementarse como una IIFE (Immediately Invoked Function Expression) con `'use strict'` y sin dependencias externas.

#### Scenario: Inicialización al cargar el DOM
- **WHEN** el DOM se carga completamente
- **THEN** el script escucha `DOMContentLoaded` e inicializa los listeners de botones "Agregar horario" y "Eliminar"

#### Scenario: Agregar nueva fila de horario
- **WHEN** el usuario hace clic en "Agregar horario" de un día específico
- **THEN** se clona el template oculto (`.availability__slot-template`) con `cloneNode(true)`
- **THEN** se asigna un `data-index` numérico secuencial único
- **THEN** los valores de los inputs se limpian (vacíos)
- **THEN** el input `slot-weekday` se establece al día correspondiente
- **THEN** la fila se agrega al contenedor `[data-day-slots="<day>"]`
- **THEN** el foco se mueve al primer input de tiempo

#### Scenario: Eliminar fila de horario con protección
- **WHEN** el usuario hace clic en el botón eliminar de una fila
- **THEN** se busca la fila contenedora con `.closest('.availability__slot-row')`
- **WHEN** es la única fila restante en ese día
- **THEN** se muestra un `alert()` y NO se elimina la fila
- **WHEN** hay más de una fila en el día
- **THEN** la fila se elimina del DOM

#### Scenario: Reindexación secuencial al enviar
- **WHEN** el formulario se envía (submit)
- **THEN** se ejecuta `reindex()` que recorre todas las filas `.availability__slot-row` y reescribe los `name` de los inputs como `slots[0][...]`, `slots[1][...]`, etc.

#### Scenario: Validación antes del envío
- **WHEN** el formulario se envía
- **THEN** se valida que todos los horarios tengan hora de inicio y fin completas
- **THEN** se valida que `end_time > start_time` en cada fila
- **THEN** se valida que no haya solapamientos de horarios dentro del mismo día
- **WHEN** hay errores de validación
- **THEN** se previene el envío (`preventDefault()`)
- **THEN** se muestran los errores en `.availability__feedback`

### Requirement: Índices globales únicos en Blade
Los nombres de los inputs en el formulario SHALL usar índices globales secuenciales (no reiniciados por día) para garantizar que PHP reciba un array sin colisiones.

#### Scenario: Contador global en renderizado Blade
- **WHEN** la vista renderiza slots existentes y el template
- **THEN** cada slot usa `name="slots[{{ $globalIndex }}][...]"` con `$globalIndex` incrementándose en cada iteración sin reiniciarse por día

#### Scenario: Sin dependencia de JS para índices correctos
- **WHEN** el formulario se envía sin JavaScript (fallback)
- **THEN** los índices PHP son únicos y no hay sobreescritura de datos entre días
