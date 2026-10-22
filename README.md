# Sistema de Gestión Empresarial (SGE)

## Información del proyecto

| Dato | Descripción |
| --- | --- |
| Nombre | Sistema de Gestión Empresarial (SGE) |
| Tipo | Aplicación web académica |
| Propósito | Administrar inventario, ventas, compras y usuarios de una empresa comercial |
| Moneda | Pesos colombianos (COP) |
| Base de datos | MySQL |
| Estado | Aplicación funcional en entorno local con Docker y Laravel Sail |

## Resumen

El Sistema de Gestión Empresarial centraliza la administración de productos, categorías, clientes, proveedores, ventas, compras, medios de pago y usuarios.

El proyecto incluye control de acceso por roles y permisos, manejo de inventario, cálculo de impuestos por producto, órdenes de compra, recepción parcial de mercancía, generación de documentos PDF y un dashboard con indicadores.

El catálogo de demostración contiene productos de distintas categorías, como tecnología, aseo y cuidado personal, hogar, alimentos y bebidas.

> Los PDF generados son documentos internos del sistema. La aplicación no está integrada con la DIAN y no genera facturas electrónicas autorizadas.

## Objetivos

### Objetivo general

Desarrollar una aplicación web que permita gestionar las operaciones básicas de una empresa comercial y controlar el acceso de los usuarios según sus funciones.

### Objetivos específicos

- Administrar productos, categorías, clientes y proveedores.
- Controlar las existencias del inventario mediante ventas y recepciones de compra.
- Registrar ventas con sus detalles, impuestos y medios de pago.
- Crear órdenes de compra sin modificar el stock hasta recibir la mercancía.
- Gestionar usuarios, roles y permisos.
- Consultar indicadores operativos desde un dashboard.
- Conservar información histórica de productos y categorías eliminados lógicamente.

## Tecnologías utilizadas

| Tecnología | Uso |
| --- | --- |
| Laravel 13 | Framework de la aplicación |
| PHP 8.3 o superior | Lenguaje del servidor |
| MySQL 8 | Base de datos relacional |
| Docker y Laravel Sail | Entorno local de desarrollo |
| Blade | Plantillas HTML del servidor |
| Tailwind CSS | Estilos de la interfaz |
| Alpine.js | Interacciones en las vistas |
| Vite | Compilación de recursos |
| Spatie Laravel Permission | Roles y permisos |
| DomPDF | Generación de documentos PDF |

## Módulos funcionales

### Dashboard

Muestra información de la operación, entre ella:

- Total de ventas del mes.
- Total de compras del mes.
- Cantidad de clientes activos.
- Cantidad de productos activos.
- Últimas ventas.
- Productos con existencias bajas.
- Productos más vendidos.

### Productos e inventario

Permite:

- Crear, consultar y editar productos.
- Asignar productos a categorías.
- Registrar costo, margen de ganancia, precio de venta e IVA.
- Consultar las existencias.
- Buscar y filtrar productos.
- Desactivar o eliminar lógicamente productos sin perder su historial relacionado.

Cuando se recibe una compra, el sistema actualiza el costo del producto y calcula de nuevo su precio público usando el margen configurado y la tarifa de IVA de venta del producto.

### Categorías

Permite organizar productos por tipo. Las categorías admiten búsqueda, consulta y eliminación lógica, conservando las relaciones históricas necesarias.

### Clientes y proveedores

Permite registrar y consultar la información de clientes y proveedores vinculados a las ventas y las compras.

### Ventas

Permite:

- Seleccionar cliente, medio de pago, fecha y productos.
- Registrar cantidades y comprobar existencias.
- Calcular subtotales, IVA y total.
- Descontar del inventario las unidades vendidas.
- Consultar el detalle de la venta.
- Descargar un comprobante interno en PDF.
- Anular ventas y devolver al inventario las cantidades correspondientes.

Las ventas utilizan consecutivos con formato `CV-00001`.

### Compras y órdenes de compra

Permite:

- Crear órdenes con proveedor, productos, cantidades, costo unitario y medio de pago.
- Registrar fecha de la orden, fecha estimada de llegada y plazo de pago.
- Consultar el estado de la orden y del pago.
- Registrar observaciones.
- Descargar la orden en PDF.
- Recibir la mercancía total o parcialmente.
- Guardar observaciones de faltantes, averías u otras novedades.

Crear la orden no aumenta el inventario. El stock se actualiza únicamente al registrar la recepción. El sistema impide recibir más unidades que las que permanecen pendientes.

Las órdenes utilizan consecutivos con formato `OC-000001`.

### Usuarios, roles y permisos

La aplicación utiliza Spatie Laravel Permission para asignar permisos a roles y proteger las rutas y acciones. Las vistas también pueden ocultar acciones que el usuario no tiene autorizadas; la seguridad se valida además en el servidor.

## Reglas de negocio

| N.º | Regla | Módulo | Aplicación |
| --- | --- | --- | --- |
| 1 | Una venta debe tener cliente, medio de pago, fecha y al menos un producto. | Ventas | Validación de la solicitud de venta |
| 2 | Las cantidades deben ser enteros positivos. | Ventas y compras | Validaciones de formularios |
| 3 | La venta no puede exceder las existencias disponibles. | Ventas | `app/Services/SaleService.php` |
| 4 | La venta y sus detalles se registran dentro de una transacción. | Ventas | `app/Services/SaleService.php` |
| 5 | Al registrar una venta se descuentan del inventario las unidades vendidas. | Ventas e inventario | `app/Services/SaleService.php` |
| 6 | Al anular una venta se conserva su historial y se devuelve el stock. | Ventas e inventario | `app/Http/Controllers/SaleController.php` |
| 7 | Cada producto tiene una tarifa de IVA propia. | Productos y ventas | Modelo y formulario de producto |
| 8 | El precio público incluye el IVA cuando aplica; el impuesto se desglosa sin sumarlo dos veces al total. | Ventas | `app/Services/SaleService.php` |
| 9 | Crear una orden de compra no modifica el inventario. | Compras | `app/Http/Controllers/PurchaseController.php` |
| 10 | La recepción de mercancía puede ser parcial. | Compras | Acción de recepción de compra |
| 11 | El inventario solo aumenta por las unidades recibidas. | Compras e inventario | Acción de recepción de compra |
| 12 | No se puede recibir más cantidad que la pendiente en la orden. | Compras | Validación de recepción |
| 13 | Cada recepción puede guardar observaciones sobre novedades del pedido. | Compras | Formulario y registro de recepción |
| 14 | Las operaciones restringidas requieren permiso en el servidor. | Seguridad | Middleware de permisos en rutas |
| 15 | La eliminación lógica conserva productos y categorías relacionados con operaciones históricas. | Productos, categorías, ventas y compras | Soft deletes y relaciones de modelos |

## Roles y matriz de seguridad

La asignación definitiva se encuentra en `database/seeders/RolePermissionSeeder.php`. La siguiente matriz resume el alcance configurado:

| Acción | Superadmin | Admin | Vendedor | Almacenista |
| --- | --- | --- | --- | --- |
| Consultar productos | Sí | Sí | Sí | Sí |
| Buscar productos | Sí | Sí | Sí | Sí |
| Crear o editar productos | Sí | Sí | No | Sí |
| Consultar categorías | Sí | Sí | No | Sí |
| Consultar clientes | Sí | Sí | Sí | No |
| Crear clientes | Sí | Sí | Sí | No |
| Consultar ventas | Sí | Sí | Sí | No |
| Crear ventas | Sí | Sí | Sí | No |
| Consultar compras | Sí | Sí | No | Sí |
| Crear órdenes de compra | Sí | Sí | No | Sí |
| Recibir compras | Sí | Sí | No | Sí |
| Gestionar usuarios | Sí | Sí | No | No |
| Gestionar roles y permisos | Sí | No | No | No |

El rol administrador recibe los permisos operativos excepto los de gestión de roles y permisos. El superadministrador recibe todos los permisos definidos. Las acciones exactas pueden consultarse en `RolePermissionSeeder`.

## Modelo de datos

### Diagrama entidad-relación

```mermaid
erDiagram
    CATEGORIES ||--o{ PRODUCTS : clasifica
    CLIENTS ||--o{ SALES : realiza
    USERS ||--o{ SALES : registra
    PAYMENT_METHODS ||--o{ SALES : utiliza
    SALES ||--|{ SALE_DETAILS : contiene
    PRODUCTS ||--o{ SALE_DETAILS : vendido

    PROVIDERS ||--o{ PURCHASES : abastece
    PAYMENT_METHODS ||--o{ PURCHASES : utiliza
    PURCHASES ||--|{ PURCHASE_DETAILS : solicita
    PRODUCTS ||--o{ PURCHASE_DETAILS : solicitado

    PURCHASES ||--o{ PURCHASE_RECEIPTS : recibe
    USERS ||--o{ PURCHASE_RECEIPTS : registra
    PURCHASE_RECEIPTS ||--|{ PURCHASE_RECEIPT_DETAILS : contiene
    PURCHASE_DETAILS ||--o{ PURCHASE_RECEIPT_DETAILS : recibido

    ```
    ## Diccionario de datos

Resumen de las entidades principales. Las migraciones de `database/migrations` contienen los tipos, restricciones, valores predeterminados y llaves foráneas.

| Tabla | Propósito | Campos principales |
| --- | --- | --- |
| `users` | Usuarios que ingresan al sistema | `id`, `name`, `email`, `password`, timestamps |
| `roles`, `permissions` y tablas relacionadas | Administración de roles y permisos | Tablas administradas por Spatie Laravel Permission |
| `categories` | Clasificación de productos | `id`, `name`, `description`, `active`, `deleted_at`, timestamps |
| `products` | Catálogo e inventario | `id`, `name`, `description`, `price`, `cost_price`, `markup_percentage`, `iva_percentage`, `stock`, `category_id`, `active`, `deleted_at`, timestamps |
| `clients` | Clientes asociados a ventas | `id`, `name`, `email`, `phone`, `address`, `active`, timestamps |
| `providers` | Proveedores asociados a compras | `id`, `name`, `email`, `phone`, `address`, `active`, timestamps |
| `payment_methods` | Medios de pago disponibles | `id`, `name`, `active`, timestamps |
| `sales` | Encabezado de una venta | `id`, `invoice_number`, `client_id`, `user_id`, `payment_method_id`, `sale_date`, `subtotal`, `tax`, `total`, `status`, `notes`, `active`, `deleted_at`, timestamps |
| `sale_details` | Productos y cantidades de una venta | `id`, `sale_id`, `product_id`, `quantity`, `price`, `subtotal`, `iva_percentage`, `iva_amount`, timestamps |
| `purchases` | Encabezado de una orden de compra | `id`, `order_number`, `provider_id`, `payment_method_id`, `purchase_date`, `expected_delivery_date`, `payment_term_days`, `due_date`, `total`, `status`, `payment_status`, `observations`, `received_at`, `active`, timestamps |
| `purchase_details` | Líneas de productos solicitados | `id`, `purchase_id`, `product_id`, `quantity`, `received_quantity`, `price`, `subtotal`, `iva_percentage`, `iva_included`, `base_subtotal`, `iva_amount`, timestamps |
| `purchase_receipts` | Recepciones parciales o completas de una orden | `id`, `purchase_id`, `user_id`, `received_at`, `observations`, timestamps |
| `purchase_receipt_details` | Cantidades recibidas por cada línea | `id`, `purchase_receipt_id`, `purchase_detail_id`, `quantity`, timestamps |

## Requisitos académicos del corte

| Criterio | Estado |
| --- | --- |
| Roles y permisos aplicados a rutas y acciones | ✅ Implementado |
| Validación de acceso no autorizado con respuesta 403 | ✅ Comprobado |
| Búsqueda, filtros y paginación en listados | ✅ Implementado en los módulos correspondientes |
| Eliminación lógica de productos y categorías | ✅ Implementado |
| Registro de ventas con encabezado y detalles | ✅ Implementado |
| Control de inventario en ventas y anulación | ✅ Implementado |
| Cálculo y desglose de IVA por producto | ✅ Implementado en ventas y compras |
| Dashboard con indicadores y productos más vendidos | ✅ Implementado |
| Órdenes de compra sin movimiento anticipado de inventario | ✅ Implementado |
| Recepción parcial y actualización del inventario recibido | ✅ Implementado |
| Validación de cantidades recibidas contra unidades pendientes | ✅ Implementado y comprobado |
| Generación de PDF para venta y orden de compra | ✅ Implementado como documento interno |
| Diagrama entidad-relación | ✅ Incluido en este README |
| Diccionario de datos | ✅ Incluido en este README |
| Reglas de negocio | ✅ Documentadas con el módulo y su implementación |
| Matriz de seguridad | ✅ Documentada |
| Registros de prueba | ✅ Verificados: 20 registros en cada entidad principal |
| Evidencias numeradas de clases 8 y 9 | ✅ Incluidas |
| Evidencias numeradas de clase 10 | 🟡 Pendiente de guardar las capturas reales |

## Conteo de datos de prueba

Conteos verificados en la base de datos local:

| Entidad | Registros |
| --- | ---: |
| Productos | 20 |
| Categorías | 20 |
| Clientes | 20 |
| Proveedores | 20 |
| Ventas | 20 |
| Compras | 20 |

> Estos conteos corresponden a la base de datos local utilizada para las evidencias. Los registros se crearon en esa base de datos y no se asume que se generen automáticamente al instalar el proyecto desde cero.

## Evidencias

Las capturas se organizan en `docs/evidencias/`. Los nombres de las carpetas respetan mayúsculas y minúsculas para que los enlaces funcionen también en GitHub.

### Clase 8: roles, permisos y eliminación lógica

- [Roles](docs/evidencias/clase8/01-roles.png)
- [Usuario y rol](docs/evidencias/clase8/02-usuario-y-rol.png)
- [Permisos en las rutas](docs/evidencias/clase8/03-permisos-rutas.png)
- [Producto eliminado correctamente](docs/evidencias/clase8/04-producto-eliminado-exito.png)
- [Registro de eliminación lógica](docs/evidencias/clase8/05-soft-delete-deleted-at.png)
- [Acceso denegado 403](docs/evidencias/clase8/06-acceso-403.png)
- [Búsqueda y filtros de productos](docs/evidencias/clase8/07-productos-busqueda-filtros.png)
- [Menú del almacenista](docs/evidencias/clase8/08-menu-almacenista.png)

### Clase 9: ventas e indicadores

- [Listado de clientes](docs/evidencias/Clase9/01-clientes-index.png)
- [Formulario de venta y totales](docs/evidencias/Clase9/02-nueva-venta-totales.png)
- [Venta y stock en MySQL](docs/evidencias/Clase9/03-mysql-venta-stock.png)
- [Validación de stock insuficiente](docs/evidencias/Clase9/04-error-stock-insuficiente.png)
- [Dashboard con indicadores](docs/evidencias/Clase9/05-dashboard-kpis-top5.png)
- [Anulación de venta y devolución de stock](docs/evidencias/Clase9/06-ventas-anulada-stock-devuelto.png)

### Clase 10: órdenes y recepción de compras

- [Stock antes de crear la orden](docs/evidencias/Clase10/01-stock-antes-orden.png)
- [Orden creada sin aumentar el stock](docs/evidencias/Clase10/02-orden-sin-stock.png)
- [Stock mientras la orden sigue sin recibir](docs/evidencias/Clase10/03-stock-sin-recibir.png)
- [Recepción parcial y aumento del stock recibido](docs/evidencias/Clase10/04-recepcion-parcial-stock.png)
- [Validación al intentar recibir unidades de más](docs/evidencias/Clase10/05-rechazo-sobrerrecepcion.png)
- [Orden de compra generada en PDF](docs/evidencias/Clase10/06-orden-compra-pdf.png)

## Instalación local

### Requisitos previos

- Git
- PHP 8.3 o superior
- Composer
- Docker Desktop y Docker Compose
- Node.js y npm, o ejecución de npm mediante Sail

### Descargar e instalar

```bash
git clone https://github.com/monialeja1011/Software-de-Gestion-Empresarial-2026.git
cd Software-de-Gestion-Empresarial-2026

cp .env.example .env
composer install

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed

./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

La aplicación queda disponible en:

```text
http://localhost
```

### Iniciar y detener el entorno

```bash
# Iniciar los contenedores
./vendor/bin/sail up -d

# Consultar su estado
./vendor/bin/sail ps

# Detenerlos
./vendor/bin/sail down
```

### Comandos útiles

```bash
# Consultar el estado de las migraciones
./vendor/bin/sail artisan migrate:status

# Limpiar las cachés de Laravel
./vendor/bin/sail artisan optimize:clear

# Consultar rutas de ventas
./vendor/bin/sail artisan route:list --path=sales

# Consultar rutas de compras
./vendor/bin/sail artisan route:list --path=purchases

# Ejecutar las pruebas automatizadas
./vendor/bin/sail artisan test
```

## Dificultades y soluciones

| Dificultad | Solución aplicada |
| --- | --- |
| MySQL rechazó conexiones mientras el contenedor todavía iniciaba. | Esperar a que el servicio MySQL aparezca saludable antes de ejecutar migraciones y consultas. |
| Una venta anulada con eliminación lógica no se encontraba al abrir su detalle. | Permitir la consulta de ventas eliminadas lógicamente para conservar el acceso al historial. |
| Crear una orden de compra podía confundirse con recibir la mercancía. | Separar la creación de la orden de la recepción; solo la recepción modifica el inventario. |
| Se debía permitir recibir pedidos en varias entregas. | Registrar cada recepción por separado y acumular las unidades recibidas. |
| Se debía impedir la recepción de unidades adicionales no solicitadas. | Comparar cada cantidad recibida con las unidades pendientes y rechazar el exceso. |
| El IVA podía sumarse dos veces cuando el precio de venta ya lo incluía. | Desglosar la base y el impuesto sin volver a sumarlo al precio público. |
| Un usuario podía intentar acceder directamente a una ruta restringida. | Proteger las rutas en el servidor mediante middleware de permisos y comprobar la respuesta 403. |

## Plan de trabajo para el tercer corte

- Revisar con el profesor el alcance tributario esperado y la posible integración con la DIAN.
- Probar el recálculo del precio público después de recibir una compra con un costo diferente.
- Completar y guardar las evidencias reales de la clase 10.
- Ejecutar y documentar las pruebas automatizadas disponibles.
- Revisar la adaptación de las pantallas para celular, tableta y computador.
- Ampliar los reportes de ventas, compras e inventario según los requerimientos del siguiente corte.

## Alcance tributario

El sistema configura la tarifa de IVA por producto. En las ventas, el precio público incluye el impuesto cuando aplica y el comprobante presenta su desglose sin sumarlo nuevamente al total.

Los PDF generados son comprobantes internos de venta y órdenes de compra. No sustituyen una factura electrónica autorizada y el sistema no está integrado con la DIAN.