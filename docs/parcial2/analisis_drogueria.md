# Análisis de la Droguería Vida Sana

## 1. Flujo integrado

1. El administrador consulta las existencias y detecta un producto por debajo del mínimo definido.
2. Un usuario autorizado crea una orden de compra para un proveedor. La orden registra quién la creó.
3. La orden pasa de borrador a enviada. Crear o enviar una orden no aumenta el inventario.
4. Cuando llega la mercancía, el auxiliar de bodega registra los productos recibidos, sus cantidades, números de lote y fechas de vencimiento.
5. El regente revisa la mercancía recibida. Acepta los lotes que cumplen las condiciones y rechaza los dañados o que no correspondan. Solo los lotes aceptados aumentan el inventario.
6. El sistema conserva quién creó la compra y quién registró la recepción. El servicio de recepción debe impedir que sean la misma persona.
7. Al vender, el sistema excluye los lotes vencidos y ordena los vigentes por fecha de vencimiento ascendente (FEFO: First Expired, First Out).
8. Para dispensar un medicamento de control especial, el regente autoriza la operación. Se deben registrar tanto el cajero que realiza la venta como el regente que autoriza.
9. Al confirmar la venta, se registran sus detalles y los lotes utilizados, se descuentan las cantidades de esos lotes y el stock total del producto, y se genera el documento de venta.
10. El administrador consulta indicadores de ventas, existencias bajas y lotes próximos a vencer.

## 2. Reglas de negocio

| Regla | Módulo | Dónde implementarla |
|---|---|---|
| No se venden lotes cuya fecha de vencimiento ya pasó. | Inventario y ventas | `DispensingService`, filtrando por vencimiento; la vista no debe ofrecerlos. |
| Se descuenta primero el lote vigente con el vencimiento más próximo (FEFO). | Ventas | `DispensingService`, ordenando por `expiration_date` y bloqueando los lotes durante la transacción. |
| La cantidad solicitada debe ser mayor que cero y no superar el stock vigente. | Ventas | `DispensingService`; validación antes de descontar. |
| Si el stock vigente es insuficiente, ningún lote ni el stock total deben cambiar. | Ventas | `DB::transaction` en `DispensingService`; lanzar `ValidationException` si no alcanza. |
| Cada lote debe pertenecer a un producto y su número no se repite para ese producto. | Inventario | Llave foránea e índice único en la base de datos. |
| Quien crea una orden no puede registrar su recepción. | Compras y recepción | Servicio de recepción: comparar el usuario creador con el receptor y rechazar si coinciden. |
| Solo los lotes aceptados tras la revisión aumentan el inventario. | Recepción e inventario | Servicio de recepción y revisión del regente. |
| La dispensación de medicamentos de control especial requiere autorización del regente. | Ventas | Permiso `autorizar-control-especial` y validación en una política o servicio de venta. |

La migración, los modelos, el seeder y `DispensingService` implementan parte de estas reglas. Las reglas de recepción y la autorización en el flujo de venta deben conectarse a sus respectivos servicios y controladores para que se apliquen en la aplicación.

## 3. Estados e impacto en inventario

### Orden de compra

- `borrador`: se puede editar; no afecta el inventario.
- `enviada`: se transmitió al proveedor; no afecta el inventario.
- `parcialmente_recibida`: llegaron algunas unidades; solo las aceptadas aumentan el inventario.
- `recibida`: se aceptaron todas las cantidades de la orden.
- `cancelada`: no se reciben más unidades; se conserva el historial.

### Venta

- `borrador`: se prepara la venta; no descuenta inventario.
- `confirmada`: descuenta las cantidades de sus lotes y el stock total dentro de una transacción.
- `anulada`: conserva el documento y la trazabilidad. Si ya estaba confirmada, la reversión debe registrar un movimiento compensatorio que reponga el inventario.

## 4. Indicadores (KPIs)

- Unidades en lotes que vencen durante los próximos 30 días.
- Productos cuyo stock total está por debajo del mínimo.
- Unidades vendidas por producto en el periodo seleccionado.
- Valor del inventario vigente.
- Porcentaje de órdenes recibidas parcial o totalmente.

## 5. Modelo de datos

### Relaciones principales

- `products` 1:N `lots`
- `providers` 1:N `purchases`
- `purchases` 1:N `purchase_details`
- `products` 1:N `purchase_details`
- `clients` 1:N `sales`
- `sales` 1:N `sale_details`
- `products` 1:N `sale_details`
- `lots` 1:N `sale_details`; `sale_details.lot_id` puede ser nulo en registros históricos.
- `users` N:M `roles`, mediante `model_has_roles`.
- `roles` N:M `permissions`, mediante `role_has_permissions`.
- Spatie también permite asignar permisos directamente a usuarios mediante `model_has_permissions`.

### ¿En qué tabla se guarda de qué lote salió cada unidad vendida?

En `sale_details`, mediante `lot_id`. Cada detalle asocia un producto y una cantidad con el lote utilizado. Si una venta toma el mismo producto de dos lotes, debe crear dos detalles, uno por lote. El campo es nullable para conservar ventas históricas que se registraron antes de agregar la relación; las nuevas ventas deben asignarlo.

### Diccionario de datos: `lots`

| Campo | Tipo | Descripción y restricciones |
|---|---|---|
| `id` | BIGINT UNSIGNED | Llave primaria. |
| `product_id` | BIGINT UNSIGNED | Llave foránea a `products.id`; obligatorio. |
| `lot_number` | VARCHAR(100) | Número del lote; obligatorio. |
| `expiration_date` | DATE | Fecha de vencimiento; obligatoria. |
| `quantity` | INT UNSIGNED | Cantidad disponible; valor predeterminado 0, mínimo cero. |
| `created_at`, `updated_at` | TIMESTAMP | Fechas de creación y actualización. |
| `(product_id, lot_number)` | UNIQUE | Evita repetir un número de lote para el mismo producto. |

### Diccionario de datos: `purchase_details`

| Campo | Tipo | Descripción y restricciones |
|---|---|---|
| `id` | BIGINT UNSIGNED | Llave primaria. |
| `purchase_id` | BIGINT UNSIGNED | Llave foránea a `purchases.id`; obligatorio. |
| `product_id` | BIGINT UNSIGNED | Llave foránea a `products.id`; obligatorio. |
| `quantity` | INT | Cantidad comprada. |
| `price` | DECIMAL(12,2) | Precio unitario registrado. |
| `subtotal` | DECIMAL(12,2) | Subtotal de la línea. |
| `iva_percentage` | DECIMAL(5,2) | Porcentaje de IVA; valor predeterminado 0. |
| `iva_included` | BOOLEAN | Indica si el precio incluye IVA; valor predeterminado falso. |
| `base_subtotal` | DECIMAL(12,2) | Subtotal base sin IVA; valor predeterminado 0. |
| `iva_amount` | DECIMAL(12,2) | Valor del IVA; valor predeterminado 0. |

### Diccionario de datos: `sale_details`

| Campo | Tipo | Descripción y restricciones |
|---|---|---|
| `id` | BIGINT UNSIGNED | Llave primaria. |
| `sale_id` | BIGINT UNSIGNED | Llave foránea a `sales.id`; obligatorio. |
| `product_id` | BIGINT UNSIGNED | Llave foránea a `products.id`; obligatorio. |
| `lot_id` | BIGINT UNSIGNED, nullable | Llave foránea a `lots.id`; nullable para ventas históricas. Las ventas nuevas deben registrar el lote. |
| `quantity` | INT | Cantidad vendida desde ese lote. |
| `price` | DECIMAL(12,2) | Precio unitario de venta. |
| `subtotal` | DECIMAL(12,2) | Subtotal de la línea. |
| `iva_percentage` | DECIMAL(5,2) | Porcentaje de IVA; valor predeterminado 0. |
| `iva_amount` | DECIMAL(12,2) | Valor del IVA; valor predeterminado 0. |

## 6. Matriz de seguridad

`V`: ver; `C`: crear; `E`: editar; `X`: eliminar. Las acciones especiales se describen en la celda correspondiente.

| Módulo/acción | Admin | Regente | Cajero | Auxiliar de bodega |
|---|---|---|---|---|
| Productos: ver | V | V | V | V |
| Gestionar productos | C/E/X | — | — | — |
| Lotes: ver | V | V | — | V |
| Compras: ver | V | V | — | V |
| Compras: crear/editar | C/E | — | — | — |
| Registrar recepción de lotes | Sí | — | — | Sí |
| Revisar y aceptar lotes | Sí | Sí | — | — |
| Ventas ordinarias: ver/crear | V/C | V | V/C | — |
| Dispensar control especial | Sí | Autoriza | C, requiere autorización | — |
| Autorizar control especial | Sí | Sí | — | — |
| Gestionar usuarios | Sí | — | — | — |
| Gestionar roles y permisos | No; corresponde a `superadmin` | — | — | — |
| Informes e indicadores | V | — | — | — |

El seeder asigna permisos a los roles del parcial. `admin` recibe los permisos existentes salvo `gestionar-roles` y `gestionar-permisos`, que se reservan al rol `superadmin`.

La segregación de funciones debe validarse en el servicio de recepción: se guarda quién creó la compra y quién la recibió, y se rechaza la operación si ambos usuarios son iguales. Ocultar una acción en la vista no es suficiente.

Si el regente cubre caja regularmente los sábados, se le puede asignar también el rol `cajero`. Para operar en caja debe seleccionar el contexto de cajero; para autorizar medicamentos controlados, el de regente. Si la aplicación no ofrece selección de rol activo, debe registrar claramente el contexto y quién autorizó cada dispensación.