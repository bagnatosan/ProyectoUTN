# CONTEXT.md: Gestor de Ventas Online (Proyecto UTN - Junio 2026)

## 1. Visión del Proyecto y Problema Comercial
El **Gestor de Ventas Online** es un software ligero de gestión de reservas y automatización de pedidos diseñado específicamente para microemprendedores.

### El Problema
Los microemprendedores sufren pérdidas de ventas y errores logísticos debido a la descentralización de pedidos (mensajes dispersos en WhatsApp, Instagram, notas de voz, etc.). La falta de un canal unificado elimina la trazabilidad, generando confusión sobre las especificaciones del producto, el estado del pago y los plazos exactos de entrega.

### La Solución (Enfoque MVP)
Una plataforma web optimizada para móviles donde:
1. El vendedor configura su perfil, catálogo de productos y disponibilidad horaria.
2. El cliente interactúa con el catálogo, personaliza su pedido y reserva un bloque de fecha/hora según disponibilidad real.
3. El vendedor centraliza el control financiero (costeo automático basado en recetas) y operativo (agenda diaria/semanal de pedidos) en un solo lugar.

---

## 2. Límites del Alcance (Scope del MVP)

| 🟢 INCLUIDO en el MVP | 🔴 EXCLUIDO del MVP |
| :--- | :--- |
| Perfil del negocio, contacto y descripción. | Chat dentro de la aplicación / Mensajería. |
| Catálogo de productos público con fotos y filtros. | Pasarelas de pago integradas (se concreta fuera). |
| Costeo automático por ingredientes (recetas). | Seguimiento automático de inventario / stock diario. |
| Calendario de disponibilidad semanal y slots de tiempo. | Sistema de reseñas, valoraciones o fidelización. |
| Formulario de reserva con validación temporal. | Mercado multivendedor (es una solución monomarca). |
| Dashboard con métricas de ganancia y filtros. | App móvil nativa (se resuelve con Web Responsive). |

---

## 3. Arquitectura de Datos (Modelos y Relaciones)

El sistema se apoya en **8 modelos principales** con las siguientes interacciones lógicas:

[User] 1 ── 1 [BusinessProfile]
│
├── 1 ── 1..* [AvailabilitySlot] (Disponibilidad semanal)
├── 1 ── 1..* [Product] (Catálogo) ── 1..* [Category]
└── 1 ── 1..* [Reservation] (Pedidos/Turnos del cliente)
    │
    └── Contiene ── [Product]

[Product] 1 ── 1..* [ProductIngredient] (Pivot) ── *..1 [Ingredient] (Materia Prima)

---

## 3. Arquitectura de Datos y Base de Datos

El sistema se apoya en **8 modelos principales**. A continuación se detallan sus campos técnicos, tipos de datos y relaciones de Eloquent en Laravel:

### 3.1. User (Usuarios)
* **Campos:**
    * `id` (BigIncrements)
    * `name` (String)
    * `email` (String, Unique)
    * `password` (String)
    * `role` (Enum: `'admin'`, `'seller'`, `'client'`)
    * `timestamps`
* **Relaciones:**
    * `hasOne(BusinessProfile::class)` -> Si el rol es `seller`.
    * `hasMany(AvailabilitySlot::class)` -> Si el rol es `seller`.
    * `hasMany(Product::class)` -> Si el rol es `seller`.
    * `hasMany(Ingredient::class)` -> Si el rol es `seller`.
    * `hasMany(Reservation::class)` -> Si el rol es `client`.

### 3.2. BusinessProfile (Perfil Comercial)
* **Campos:**
    * `id` (BigIncrements)
    * `user_id` (UnsignedBigInteger, FK -> `users.id`, Cascade)
    * `brand_name` (String)
    * `description` (Text, Nullable)
    * `phone` (String)
    * `address` (String, Nullable)
    * `logo_path` (String, Nullable)
    * `timestamps`
* **Relaciones:**
    * `belongsTo(User::class)`

### 3.3. Category (Categorías)
* **Campos:**
    * `id` (BigIncrements)
    * `name` (String)
    * `slug` (String, Unique)
    * `timestamps`
* **Relaciones:**
    * `hasMany(Product::class)`

### 3.4. Product (Productos)
* **Campos:**
    * `id` (BigIncrements)
    * `user_id` (UnsignedBigInteger, FK -> `users.id`, Cascade) -> *Dueño del producto*
    * `category_id` (UnsignedBigInteger, FK -> `categories.id`, Set Null)
    * `name` (String)
    * `description` (Text)
    * `price` (Decimal: 10, 2)
    * `image_path` (String, Nullable)
    * `estimated_cost` (Decimal: 10, 2, Default: 0.00) -> *Calculado por ingredientes*
    * `suggested_price` (Decimal: 10, 2, Default: 0.00) -> *Calculado por ingredientes ($\text{costo} \times 3$)*
    * `is_active` (Boolean, Default: True)
    * `timestamps`
    * `deleted_at` (**Soft Deletes obligatorios para no romper el historial de reservas**)
* **Relaciones:**
    * `belongsTo(User::class)`
    * `belongsTo(Category::class)`
    * `belongsToMany(Ingredient::class)->withPivot('quantity')` -> *Receta*
    * `hasMany(Reservation::class)`

### 3.5. Ingredient (Ingredientes / Materia Prima)
* **Campos:**
    * `id` (BigIncrements)
    * `user_id` (UnsignedBigInteger, FK -> `users.id`, Cascade) -> *Vendedor dueño del insumo*
    * `name` (String)
    * `unit_of_measure` (Enum: `'gr'`, `'ml'`, `'unit'`)
    * `cost_per_unit` (Decimal: 10, 4)
    * `timestamps`
* **Relaciones:**
    * `belongsTo(User::class)`
    * `belongsToMany(Product::class)->withPivot('quantity')`

### 3.6. ProductIngredient (Tabla Pivot - Recetas)
* **Campos:**
    * `id` (BigIncrements)
    * `product_id` (UnsignedBigInteger, FK -> `products.id`, Cascade)
    * `ingredient_id` (UnsignedBigInteger, FK -> `ingredients.id`, Cascade)
    * `quantity` (Decimal: 10, 2) -> *Cantidad usada en el producto según su unidad de medida*

### 3.7. AvailabilitySlot (Disponibilidad Horaria)
* **Campos:**
    * `id` (BigIncrements)
    * `user_id` (UnsignedBigInteger, FK -> `users.id`, Cascade) -> *Vendedor*
    * `day_of_week` (UnsignedTinyInteger) -> *0 (Domingo) a 6 (Sábado)*
    * `start_time` (Time) -> *Ej: 09:00:00*
    * `end_time` (Time) -> *Ej: 13:00:00*
    * `timestamps`
* **Relaciones:**
    * `belongsTo(User::class)`

### 3.8. Reservation (Turnos / Pedidos)
* **Campos:**
    * `id` (BigIncrements)
    * `user_id` (UnsignedBigInteger, FK -> `users.id`, Cascade) -> *Cliente que reserva*
    * `product_id` (UnsignedBigInteger, FK -> `products.id`, Restrict)
    * `date` (Date)
    * `time` (Time)
    * `notes` (Text, Nullable)
    * `status` (Enum: `'pending'`, `'confirmed'`, `'completed'`, `'cancelled'`, Default: `'pending'`)
    * `timestamps`
* **Relaciones:**
    * `belongsTo(User::class, 'user_id')`
    * `belongsTo(Product::class)`

---

### Modelos y Atributos Clave:
*   **`User`**: Roles definidos (`admin`, `seller`, `client`). El sistema base de autenticación ya está provisto.
*   **`BusinessProfile`**: Información comercial del emprendedor (`name`, `description`, `phone`, `address`, `logo_path`).
*   **`Category`**: Taxonomía de los artículos (`name`, `slug`).
*   **`Ingredient`**: Insumos puros (`name`, `unit_of_measure` [gr, ml, unidad], `cost_per_unit`).
*   **`ProductIngredient`**: Tabla asociativa de la receta (`product_id`, `ingredient_id`, `quantity`).
*   **`Product`**: Datos de venta (`name`, `description`, `price`, `image_path`, `is_active`, `estimated_cost`, `suggested_price`). *Utiliza Soft Deletes*.
*   **`AvailabilitySlot`**: Configuración de atención (`day_of_week`, `start_time`, `end_time`).
*   **`Reservation`**: Control del pedido (`user_id`, `product_id`, `date`, `time`, `notes`, `status` [`pending`, `confirmed`, `completed`, `cancelled`]).

---

## 4. Stack Tecnológico y Reglas de Desarrollo

Este proyecto sigue una estricta política de desarrollo limpio y nativo para garantizar ligereza y mantenibilidad.

*   **Backend:** Laravel 10+ / 11+ (PHP).
*   **Frontend Templating:** Blade.
*   **Estilos:** HTML5 semántico y CSS3 Nativo. Uso de variables CSS (`--primary-color`) y metodología **BEM** (Block-Element-Modifier). Enfoque **Mobile-First**.
*   **Interactividad:** **JavaScript Vanilla** exclusivamente (ECMAScript moderno).
*   **Restricción absoluta:** No está permitido instalar frameworks de CSS (Tailwind, Bootstrap), librerías de JS (React, Vue, jQuery) o complementos de terceros (Swiper, GSAP, librerías de calendarios) sin previa autorización explícita del Líder Técnico.

---

## 5. Cronograma y Distribución por Programador

El desarrollo dura **4 semanas**: las semanas 1-3 son exclusivamente de Backend y la semana 4 es de Frontend e integración.

### 🧑‍💻 Programador 1: Gestión de Perfil de Negocio y Seguridad
*   **Backend (Sem. 1-3):** CRUD de `BusinessProfile`. Lógica de almacenamiento local, validación y reemplazo de logos (`Storage`). Middlewares de seguridad para protección de rutas según rol `seller`.
*   **Frontend (Sem. 4):** Vista `business_profile/edit.blade.php` con validaciones nativas. Maquetación de la Navbar global interactiva en `layouts/app.blade.php` (cambia dinámicamente según si el `User` es Cliente o Emprendedor).

### 🧑‍💻 Programador 2: Catálogo de Productos y Categorías
*   **Backend (Sem. 1-3):** CRUD de `Category`. CRUD de `Product` con subida de imágenes descriptivas. Implementación de **Soft Deletes** en `Product`. Controlador público del catálogo (filtra por `is_active = true`).
*   **Frontend (Sem. 4):** Panel del vendedor (`products/index.blade.php`) en formato tabla interactiva con toggles de estado (activo/inactivo). Formularios de creación y edición. Vista pública del catálogo (`catalog/show.blade.php`) usando UI de tarjetas (*cards*) responsivas.

### 🧑‍💻 Programador 3: Inventario de Ingredientes y Constructor de Recetas
*   **Backend (Sem. 1-3):** CRUD de `Ingredient` (materia prima y costos base). Lógica de vinculación en tabla pivot `ProductIngredient` para estructurar recetas por cantidades. Métodos para limpiar o reescribir recetas.
*   **Frontend (Sem. 4):** Vistas CRUD de ingredientes (`ingredients/*`). **Constructor de Recetas interactivo** en `recipes/edit.blade.php`: interfaz dinámica en JS Vanilla que permite añadir/quitar filas de ingredientes y definir sus cantidades en tiempo real antes de guardar.

### 🧑‍💻 Programador 4: Disponibilidad y Motor de Reservas
*   **Backend (Sem. 1-3):** Controlador de `AvailabilitySlot` (franjas horarias comerciales). **Motor de Disponibilidad:** Algoritmo que recibe una fecha y retorna los slots libres, cruzando los horarios del perfil con las filas ya existentes en `Reservation`. Control y mutación de estados de reserva (`pending`, `confirmed`, etc.).
*   **Frontend (Sem. 4):** Configuración de agenda del vendedor (`availability/edit.blade.php`). Formulario público de reservas del cliente (`reservations/create.blade.php`) con un **calendario interactivo nativo en JS** que consume de forma asíncrona (Fetch API) las horas libres del motor. Historial de reservas del cliente.

### 🧑‍💻 Programador 5: Costos, Analítica Comercial y Control de Calidad
*   **Backend (Sem. 1-3):** **Observer/Trigger en Laravel** para calcular de forma automatizada el `estimated_cost` de un producto sumando (`quantity` * `cost_per_unit`) de sus ingredientes, y definiendo el `suggested_price` ($\text{costo} \times 3$). Consultas SQL de analítica (ganancia real, reservas activas, ratios de rentabilidad). Seeders y Factory de datos realistas para testing. Controladores de filtros avanzados de reservas.
*   **Frontend (Sem. 4):** Dashboard principal del Emprendedor (`dashboard/index.blade.php`) con tarjetas métricas visuales. Integración del listado operativo de pedidos diarios con buscadores y botones de cambio de estado dinámicos en un clic.

---

## 6. Flujo de Trabajo en Semana 4 (Integración)
Durante la última semana, el equipo congelará cambios en la base de datos y unificará la experiencia de usuario:
1.  **Consistencia Estilística:** Uso compartido de la hoja de estilos global y variables CSS.
2.  **Pruebas Cruzadas:** Cada programador testeará exhaustivamente de forma funcional el módulo de un compañero (ej: P1 testea a P2, P2 a P3, etc.) para garantizar la resiliencia del MVP ante fallos o ingresos de datos erróneos por parte del usuario.
