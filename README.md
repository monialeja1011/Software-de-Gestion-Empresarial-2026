## Análisis del negocio

### Situación que atiende el sistema

Una empresa comercial necesita mantener conectadas sus operaciones de abastecimiento, inventario y venta. Cuando las órdenes de compra, las recepciones y las ventas se registran por separado, resulta difícil saber cuántas unidades están disponibles, qué pedidos aún no llegan y qué productos se venden con mayor frecuencia.

El SGE centraliza esta información en una aplicación. Cada operación queda relacionada con sus responsables, productos, cantidades, fechas y valores. Esto permite consultar el historial y mantener coherencia entre las compras, las recepciones y las ventas.

### Actores del sistema

| Actor | Responsabilidad en la operación |
| --- | --- |
| Administrador | Gestionar las operaciones habilitadas para administración, consultar módulos y mantener usuarios según sus permisos. |
| Superadministrador | Administrar el sistema, incluidos roles y permisos. |
| Vendedor | Consultar productos y clientes autorizados, registrar ventas y consultar sus operaciones según los permisos asignados. |
| Almacenista | Consultar productos, preparar órdenes de compra y registrar la llegada total o parcial de la mercancía. |
| Cliente | Persona o entidad a quien se registra una venta. Sus datos quedan asociados a la operación. |
| Proveedor | Persona o entidad que abastece productos y queda asociada a las órdenes de compra. |

El acceso efectivo depende de los permisos del usuario y del rol activo seleccionado para la sesión. La interfaz puede ocultar acciones no autorizadas, pero la autorización también se comprueba en el servidor.

### Alcance funcional

El alcance implementado comprende el catálogo de productos y categorías, el registro de clientes y proveedores, las ventas, las órdenes de compra, las recepciones, el control de inventario, los medios de pago, la gestión de usuarios y el dashboard.

El sistema genera PDF para consultar o descargar comprobantes internos de ventas y órdenes de compra. Estos documentos no constituyen facturas electrónicas autorizadas y la aplicación no tiene integración con la DIAN.

## Procesos principales

### Abastecimiento y recepción

1. El usuario autorizado selecciona un proveedor y registra la fecha de la orden, las condiciones de pago, los productos solicitados, las cantidades y los costos cotizados.
2. El sistema guarda la orden con un consecutivo `OC-000001`, su detalle y el estado correspondiente.
3. La creación de la orden no cambia las existencias. Las unidades solicitadas todavía no se consideran disponibles.
4. Cuando llega el pedido, el almacenista abre la orden y registra las cantidades recibidas para cada producto.
5. El sistema compara cada cantidad con las unidades pendientes. Si se intenta registrar un exceso, rechaza la recepción.
6. La recepción puede ser parcial. Las unidades pendientes permanecen en la orden para una entrega posterior.
7. El stock aumenta solo por las unidades registradas como recibidas. La fecha, el usuario y las observaciones de cada recepción quedan asociados al historial.
8. Cuando se completa la orden, el sistema actualiza su estado de recepción.

### Venta y actualización del inventario

1. El usuario autorizado selecciona el cliente, el medio de pago y los productos que va a vender.
2. La aplicación carga el precio público y la tarifa de IVA configurados para cada producto.
3. Antes de registrar la operación, el sistema comprueba que las cantidades solicitadas sean válidas y que haya stock suficiente.
4. La venta y sus detalles se guardan dentro de una transacción de base de datos. Si ocurre un error, la operación no debe quedar parcialmente registrada.
5. Al confirmarse la venta, se descuentan del inventario las unidades vendidas.
6. La venta recibe un consecutivo con formato `CV-00001` y puede consultarse en pantalla o descargarse como PDF interno.
7. Si se anula una venta, el sistema conserva el registro histórico y devuelve al inventario las unidades vendidas.

### Cálculo de costo, precio público e IVA

Cada producto almacena un costo, un porcentaje de margen, un precio público y una tarifa de IVA. Al recibirse mercancía, el sistema actualiza la información de costo y calcula el precio público de acuerdo con la configuración del producto.

En ventas, el precio público se trata como un precio que ya incluye el IVA cuando el producto tiene una tarifa aplicable. El comprobante muestra el desglose de la base y el impuesto, sin volver a sumar el IVA al total pagado. Cuando la tarifa configurada es cero, el producto se presenta sin IVA aplicable.

En compras se conservan el costo cotizado y los datos del IVA de la línea, para poder consultar cómo se calculó el total de la orden.

## Seguridad y autorización

Las rutas protegidas emplean autenticación y permisos de Spatie Laravel Permission. Cada rol agrupa permisos operativos; el superadministrador dispone de permisos de administración del sistema, mientras que los roles de vendedor y almacenista tienen acceso limitado a las operaciones de su función.

Cuando una cuenta tiene varios roles, puede seleccionar el rol activo para la sesión. La aplicación utiliza esa selección al autorizar las acciones. Además, las rutas comprueban el permiso en el servidor: ocultar un botón en la interfaz no sustituye esta validación.

La matriz de este README resume los permisos por módulo. La configuración completa debe contrastarse con `database/seeders/RolePermissionSeeder.php`, donde se declaran los nombres de permisos y su asignación a cada rol.

## Validaciones y resultados observados

| Escenario | Resultado esperado | Evidencia relacionada |
| --- | --- | --- |
| Consultar una ruta sin el permiso requerido | El servidor rechaza el acceso con respuesta 403. | `docs/evidencias/clase8/06-acceso-403.png` |
| Buscar y filtrar productos | El listado muestra los resultados correspondientes a los criterios ingresados. | `docs/evidencias/clase8/07-productos-busqueda-filtros.png` |
| Registrar una venta con stock suficiente | Se registra el encabezado y el detalle; el stock disminuye por las unidades vendidas. | `docs/evidencias/Clase9/02-nueva-venta-totales.png` y `docs/evidencias/Clase9/03-mysql-venta-stock.png` |
| Registrar una venta que supera el stock | La operación se rechaza y no debe descontar inventario. | `docs/evidencias/Clase9/04-error-stock-insuficiente.png` |
| Anular una venta | La venta conserva su historial y las unidades se devuelven al inventario. | `docs/evidencias/Clase9/06-ventas-anulada-stock-devuelto.png` |
| Crear una orden de compra | La orden y sus líneas quedan guardadas; el stock no aumenta por la creación de la orden. | `docs/evidencias/Clase10/02-orden-sin-stock.png` y `docs/evidencias/Clase10/03-stock-sin-recibir.png` |
| Recibir parcialmente una orden | El stock aumenta por la cantidad recibida y las unidades restantes continúan pendientes. | `docs/evidencias/Clase10/04-recepcion-parcial-stock.png` |
| Intentar recibir más unidades que las pendientes | El sistema valida y rechaza la sobre recepción. | `docs/evidencias/Clase10/05-rechazo-sobrerrecepcion.png` |
| Descargar una orden de compra | Se genera el PDF interno asociado a la orden. | `docs/evidencias/Clase10/06-orden-compra-pdf.png` |

Las capturas ilustran los escenarios documentados. Las reglas críticas de inventario también se implementan en el servidor para que no dependan únicamente de las validaciones visuales del formulario.

## Interpretación de los indicadores

| Indicador | Qué representa | Decisión que puede apoyar |
| --- | --- | --- |
| Ventas del mes | Suma de las ventas activas dentro del periodo mensual consultado. | Revisar el nivel de ventas del periodo y compararlo con otros periodos. |
| Compras del mes | Suma de las compras activas registradas dentro del periodo mensual consultado. | Revisar el volumen de abastecimiento y los compromisos de compra. |
| Clientes activos | Número de clientes habilitados. | Conocer el tamaño actual de la cartera disponible para operar. |
| Productos activos | Número de productos habilitados. | Consultar la amplitud del catálogo disponible. |
| Stock bajo | Productos cuya existencia está en o por debajo del umbral configurado en el dashboard. | Identificar productos que conviene revisar para una posible reposición. |
| Productos más vendidos | Productos ordenados según las unidades vendidas. | Reconocer los artículos con mayor movimiento y apoyar decisiones de abastecimiento. |
| Últimas ventas | Operaciones registradas recientemente. | Revisar de forma rápida la actividad comercial reciente. |

Los indicadores son resúmenes de consulta. La decisión final sobre compras, precios o reposición debe considerar además el detalle de cada producto y sus movimientos.

## Arquitectura y organización del código

La aplicación sigue la organización habitual de Laravel:

| Componente | Responsabilidad en este proyecto |
| --- | --- |
| `app/Models` | Representar las entidades y relaciones de la base de datos, como productos, ventas, compras y recepciones. |
| `app/Http/Controllers` | Atender las rutas, coordinar las solicitudes y seleccionar las vistas o redirecciones. |
| `app/Http/Requests` | Validar los datos recibidos en formularios cuando existe una clase Form Request para la operación. |
| `app/Services` | Encapsular operaciones de negocio que requieren coordinación y transacciones, como el registro de ventas. |
| `database/migrations` | Definir la estructura y evolución del esquema de la base de datos. |
| `database/seeders` | Crear datos iniciales y configurar roles, permisos y cuentas previstas para desarrollo. |
| `resources/views` | Contener las plantillas Blade de los módulos y documentos PDF. |
| `routes` | Declarar las rutas web y sus protecciones de autenticación y permisos. |
| `docs/evidencias` | Organizar las capturas de comprobación por clase y tema. |

Las operaciones que modifican simultáneamente una transacción comercial y el inventario deben ejecutarse de forma coordinada para evitar guardar solo una parte de la operación.

## Datos de prueba y reproducibilidad

Los conteos indicados en la sección de datos de prueba corresponden a la base local usada para revisar la aplicación y obtener las evidencias. No deben interpretarse como una garantía de que una instalación limpia cree automáticamente la misma cantidad de registros.

La inicialización de roles, permisos y datos debe verificarse con los seeders antes de instalar el proyecto en otra base de datos. No se deben ejecutar seeders de demostración sobre una base con información existente sin revisar primero qué datos crean o modifican.