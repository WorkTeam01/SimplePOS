<?php

/**
 * Inicia la sesión con cookies endurecidas (httponly + secure si HTTPS).
 * Debe llamarse antes de cualquier salida y de leer/escribir $_SESSION.
 */
function iniciarSesionSegura()
{
    if (session_status() === PHP_SESSION_NONE) {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $https,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/**
 * Regenera el ID de sesión (mitiga fijación de sesión). Llamar tras autenticar.
 */
function regenerarSesion()
{
    if (session_status() === PHP_SESSION_NONE) {
        iniciarSesionSegura();
    }
    session_regenerate_id(true);
}

function requiereAutenticacion()
{
    iniciarSesionSegura();
    if (!isset($_SESSION['idusuario'])) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }
}

// Alias para el nombre usado por los endpoints existentes.
function requireAuth()
{
    requiereAutenticacion();
}

/**
 * Devuelve (y crea si falta) el token CSRF de la sesión actual.
 */
function csrfToken()
{
    iniciarSesionSegura();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valida el token CSRF enviado (POST o cabecera X-CSRF-TOKEN).
 * Termina con 403 si no coincide.
 */
function verificarCsrf()
{
    iniciarSesionSegura();
    $esperado = $_SESSION['csrf_token'] ?? '';
    $enviado = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if ($esperado === '' || $enviado === '' || !hash_equals($esperado, $enviado)) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['error' => 'Token CSRF inválido']);
        exit;
    }
}

/**
 * Campo oculto HTML con el token CSRF (para formularios clásicos).
 */
function csrfCampo()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES) . '">';
}

/**
 * Exige un permiso de sesión (clave: escritorio, almacen, compras, ventas,
 * acceso, consultac, consultav, configuracion). Termina con 403 si no lo tiene.
 */
function requerirPermiso($clave)
{
    requiereAutenticacion();
    if (empty($_SESSION[$clave])) {
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(['error' => 'Sin permiso para esta operación']);
        exit;
    }
}

/**
 * Exige cualquiera de varios permisos de sesión (útil para handlers compartidos,
 * p. ej. persona.php que sirve a clientes [ventas] y proveedores [compras]).
 */
function requerirAlgunPermiso($claves)
{
    requiereAutenticacion();
    foreach ((array) $claves as $clave) {
        if (!empty($_SESSION[$clave])) return;
    }
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Sin permiso para esta operación']);
    exit;
}

/**
 * Hashea una contraseña con el algoritmo por defecto de PHP (bcrypt/argon2).
 */
function hashPassword($clave)
{
    return password_hash($clave, PASSWORD_DEFAULT);
}

/**
 * Verifica una contraseña contra el hash almacenado. Soporta el hash
 * legacy SHA256 (sin sal) para migración transparente.
 */
function verifyPassword($clave, $hashAlmacenado)
{
    if ($hashAlmacenado === '' || $hashAlmacenado === null) return false;

    // Hash moderno (bcrypt/argon2) — password_get_info lo identifica.
    if (password_get_info($hashAlmacenado)['algoName'] !== 'unknown') {
        return password_verify($clave, $hashAlmacenado);
    }

    // Legacy SHA256 sin sal: se acepta una sola vez y se fuerza re-hash.
    return hash_equals($hashAlmacenado, hash('sha256', $clave));
}

/**
 * Indica si un hash almacenado aún usa el esquema legacy (SHA256)
 * y debe re-hashearse con password_hash tras verificar.
 */
function passwordRequiereRehash($hashAlmacenado)
{
    return password_get_info($hashAlmacenado)['algoName'] === 'unknown';
}
