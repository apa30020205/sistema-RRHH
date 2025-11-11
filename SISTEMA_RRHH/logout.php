<?php
/**
 * CERRAR SESIÓN
 * 
 * Destruye la sesión del usuario y lo redirige al login
 */

require_once 'includes/session.php';

cerrarSesion();

// Redirigir al login
header('Location: index.php');
exit();
?>












