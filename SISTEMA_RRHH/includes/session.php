<?php
/**
 * GESTIÓN DE SESIONES
 * 
 * Este archivo maneja el inicio y cierre de sesión de usuarios.
 * Usa sesiones PHP nativas para mantener al usuario logueado.
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    // Configurar tiempo de vida de la sesión (2 horas)
    ini_set('session.cookie_lifetime', 7200); // 2 horas en segundos
    ini_set('session.gc_maxlifetime', 7200);
    session_start();
}

/**
 * Verificar si el usuario está logueado
 * @return bool true si está logueado, false si no
 */
function estaLogueado() {
    // Verificar que existan las variables de sesión
    if (!isset($_SESSION['funcionario_id']) || !isset($_SESSION['cedula'])) {
        return false;
    }
    
    // Verificar que la sesión no haya expirado (2 horas)
    if (isset($_SESSION['login_time'])) {
        $tiempo_transcurrido = time() - $_SESSION['login_time'];
        // Si han pasado más de 2 horas (7200 segundos), cerrar sesión
        if ($tiempo_transcurrido > 7200) {
            cerrarSesion();
            return false;
        }
    }
    
    return true;
}

/**
 * Obtener el ID del funcionario logueado
 * @return int|null ID del funcionario o null si no está logueado
 */
function getFuncionarioId() {
    return $_SESSION['funcionario_id'] ?? null;
}

/**
 * Obtener la cédula del funcionario logueado
 * @return string|null Cédula o null si no está logueado
 */
function getCedula() {
    return $_SESSION['cedula'] ?? null;
}

/**
 * Iniciar sesión
 * @param int $funcionario_id ID del funcionario
 * @param string $cedula Cédula del funcionario
 */
function iniciarSesion($funcionario_id, $cedula) {
    $_SESSION['funcionario_id'] = $funcionario_id;
    $_SESSION['cedula'] = $cedula;
    $_SESSION['login_time'] = time();
}

/**
 * Cerrar sesión
 */
function cerrarSesion() {
    // Limpiar todas las variables de sesión
    $_SESSION = array();
    
    // Destruir la cookie de sesión si existe
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
        setcookie(session_name(), '', time() - 3600);
    }
    
    // Destruir la sesión
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Requerir que el usuario esté logueado
 * Si no está logueado, redirige al login
 */
function requerirLogin() {
    if (!estaLogueado()) {
        header('Location: ../index.php');
        exit();
    }
}
?>











