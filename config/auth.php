<?php
function requireAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['idusuario'])) {
        header('HTTP/1.1 401 Unauthorized');
        echo json_encode(['error' => 'No autenticado']);
        exit;
    }
}
