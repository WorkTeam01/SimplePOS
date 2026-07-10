# CLAUDE.md

Este archivo proporciona orientación a Claude Code (claude.ai/code) cuando trabaja con el código de este repositorio.

## Descripción del Proyecto

**SimplePOS** es un Sistema de Punto de Venta (POS) y Gestión de Inventario open source construido con PHP y MySQL. Soporta operaciones multi-sucursal, control de acceso basado en roles y generación de reportes PDF. La interfaz está en español.

## Ejecución de la Aplicación

Este proyecto corre sobre XAMPP/LAMPP. No requiere compilación — los archivos PHP se sirven directamente.

- **Iniciar servidor:** `sudo /opt/lampp/lampp start`
- **Acceder a la app:** `http://localhost/SimplePOS/`
- **Entry point:** `index.php` redirige a `vistas/login.html`
- **Configuración PHP:** `config/global.php` (carga variables de `.env`), `config/Conexion.php` (conexión + helpers), `config/auth.php` (helper `requireAuth()` de control de sesión)
- **Variables de entorno:** Copiar `.env.example` a `.env` y ajustar credenciales. `config/global.php` las carga automáticamente con fallback a valores por defecto (host `localhost`, BD `simplepos`, usuario `root`, sin contraseña).

Este proyecto no tiene pruebas automatizadas, configuraciones de linter ni gestores de paquetes.

## Arquitectura

La aplicación sigue un patrón MVC personalizado de 3 capas:

| Capa          | Directorio | Rol                                                   |
| ------------- | ---------- | ----------------------------------------------------- |
| Modelos       | `modelos/` | Lógica de negocio + consultas a BD (12 clases PHP)    |
| Controladores | `ajax/`    | Manejadores de peticiones AJAX, un archivo por modelo |
| Vistas        | `vistas/`  | Plantillas HTML/PHP; el JS vive en `vistas/scripts/`  |

**Flujo de una petición:**

1. Las vistas hacen llamadas AJAX POST a `ajax/<modulo>.php?op=<accion>`
2. El manejador AJAX instancia el modelo correspondiente y llama a un método
3. El modelo ejecuta una consulta usando las funciones helper de `Conexion.php` y retorna JSON

**Reportes** (`reportes/`) usan la librería FPDF 1.81 (`fpdf181/`) para generar PDFs.

## Convenciones de Base de Datos

- **Eliminación lógica:** Todas las entidades tienen una columna `estado` (1 = activo, 0 = inactivo). Nunca eliminar registros físicamente; usar los métodos `activar()`/`desactivar()` del modelo.
- **Claves primarias:** `id<nombretabla>` (ej. `idproducto`, `idventa`)
- **Marcas de tiempo:** `fechacreacion` usando `CURRENT_TIMESTAMP` o `NOW()`
- **Contraseñas:** SHA2(contraseña, 256) — nunca almacenar en texto plano

## Convenciones de los Modelos

Cada clase modelo sigue esta interfaz estándar:

```php
insertar()    // INSERT
editar()      // UPDATE
mostrar()     // SELECT fila única
listar()      // SELECT todos los registros activos
desactivar()  // SET estado = 0
activar()     // SET estado = 1
```

Funciones helper de base de datos (definidas en `config/Conexion.php`):

```php
ejecutarConsulta($sql)              // Retorna un conjunto de resultados
ejecutarConsultaSimpleFila($sql)    // Retorna una sola fila como array
ejecutarConsulta_retornarID($sql)   // Retorna el último ID insertado (false si la query falló)
limpiarCadena($str)                 // Solo SQL-escape (mysqli_real_escape_string) — usar en toda entrada del usuario antes de la consulta
```

Toda salida de datos de BD hacia HTML/JSON debe pasar por `htmlspecialchars()` en el punto de salida (en `ajax/*.php` y vistas) — `limpiarCadena()` ya no hace HTML-encoding, así que el escape de salida es responsabilidad de cada handler.

## Autenticación

Todo endpoint en `ajax/*.php` (excepto `usuario.php` case `verificar`, que es el propio login) debe llamar a `requireAuth()` (definida en `config/auth.php`) al inicio del archivo. Corta la ejecución con 401 si no hay `$_SESSION['idusuario']`.

## Convenciones del Frontend

- **Framework:** Bootstrap 3.3.7 + plantilla AdminLTE
- **Patrón AJAX:** `$.ajax({ url: '../ajax/modulo.php?op=accion', type: 'POST', data: ... })`
- **Tablas:** DataTables con extensiones responsive
- **Modales:** Diálogos modal de Bootstrap para formularios de creación/edición
- **Subida de archivos:** Solo se aceptan jpg/jpeg/png; se almacenan en `files/productos/` o `files/usuarios/` con nombres basados en timestamp

## Lógica de Negocio Clave

- **Gestión de stock:** El stock aumenta con compras (`compra`) y disminuye con ventas (`venta`). Ambos módulos revierten el stock al anular.
- **Ventas:** Soportan múltiples métodos de pago por transacción. Los detalles de pago se almacenan por separado en `pagos_venta`. Las ventas no pueden editarse después de creadas — solo anularse (ANULADO).
- **Traspasos:** El módulo `traspaso` mueve stock entre sucursales (`sucursal`).
- **Personas:** Clientes y proveedores comparten la tabla `persona`, distinguidos por `tipopersona`.
- **Escritorio:** `vistas/escritorio.php` ejecuta consultas analíticas a través de `modelos/Consultas.php` para los mejores clientes, productos más/menos vendidos y comparaciones por período.

## Rutas de Subida de Archivos

- Imágenes de productos: `files/productos/`
- Imágenes de usuarios: `files/usuarios/`
- Logo para reportes: `reportes/logo.png`
