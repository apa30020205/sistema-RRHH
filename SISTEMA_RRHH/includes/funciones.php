<?php
/**
 * FUNCIONES AUXILIARES
 * 
 * Funciones útiles para todo el sistema
 */

/**
 * Limpiar y validar datos de entrada
 * Previene inyección SQL y XSS
 */
function limpiarDatos($data) {
    $data = trim($data);              // Eliminar espacios al inicio y final
    $data = stripslashes($data);       // Eliminar barras invertidas
    $data = htmlspecialchars($data);  // Convertir caracteres especiales a HTML
    return $data;
}

/**
 * Mostrar mensaje de éxito
 */
function mostrarExito($mensaje) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($mensaje) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

/**
 * Mostrar mensaje de error
 */
function mostrarError($mensaje) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($mensaje) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

/**
 * Obtener información del funcionario desde la sesión
 */
function obtenerFuncionario($conn, $funcionario_id) {
    $stmt = $conn->prepare("SELECT * FROM funcionarios WHERE id = ? AND activo = 1");
    $stmt->bind_param("i", $funcionario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>












