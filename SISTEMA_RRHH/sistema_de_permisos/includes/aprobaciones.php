<?php
/**
 * SISTEMA DE APROBACIONES
 * 
 * Maneja el flujo de aprobación de 3 niveles para los formularios
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/email.php';
require_once __DIR__ . '/funciones.php';

/**
 * Iniciar flujo de aprobación cuando se crea una solicitud
 */
function iniciarFlujoAprobacion($conn, $tipo_formulario, $formulario_id, $funcionario_id) {
    // Obtener información del funcionario
    $funcionario = obtenerFuncionario($conn, $funcionario_id);
    if (!$funcionario) {
        return false;
    }
    
    // Generar token para el jefe inmediato
    $token = generarTokenAprobacion();
    $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    // Asegurar que el token sea una cadena (prevenir conversión a número)
    $token = (string)$token;
    
    // Log para diagnóstico
    error_log("Token generado (longitud: " . strlen($token) . "): " . substr($token, 0, 20) . "...");
    
    // Crear registro de aprobación nivel 1 (Jefe Inmediato)
    // Usar CAST explícito en SQL para asegurar que el token se guarde como string
    $stmt = $conn->prepare("INSERT INTO aprobaciones 
        (tipo_formulario, formulario_id, nivel_aprobacion, token_aprobacion, fecha_expiracion_token) 
        VALUES (?, ?, 1, CAST(? AS CHAR(255)), ?)");
    $stmt->bind_param("siss", $tipo_formulario, $formulario_id, $token, $fecha_expiracion);
    
    if (!$stmt->execute()) {
        error_log("Error al crear registro de aprobación: " . $conn->error);
        return false;
    }
    
    // Actualizar formulario con nivel actual
    actualizarNivelAprobacion($conn, $tipo_formulario, $formulario_id, 1);
    
    // Enviar email al jefe inmediato
    $email_jefe = $funcionario['email_jefe_inmediato'];
    if (!empty($email_jefe)) {
        $asunto = "Nueva Solicitud Pendiente de Aprobación - " . ucfirst($tipo_formulario);
        $mensaje = crearEmailSolicitudPendiente($tipo_formulario, $funcionario['nombre_completo'], $formulario_id, $token, 'jefe_inmediato', 1);
        enviarEmail($email_jefe, $asunto, $mensaje);
        
        // Marcar email como enviado
        $aprobacion_id = $stmt->insert_id;
        $stmt_update = $conn->prepare("UPDATE aprobaciones SET email_enviado = 1 WHERE id = ?");
        $stmt_update->bind_param("i", $aprobacion_id);
        $stmt_update->execute();
    }
    
    return true;
}

/**
 * Procesar aprobación/rechazo de un nivel
 */
function procesarAprobacion($conn, $token, $accion, $aprobador_id, $aprobador_nombre, $observaciones = '') {
    // Obtener registro de aprobación
    $stmt = $conn->prepare("SELECT * FROM aprobaciones WHERE token_aprobacion = ? AND fecha_expiracion_token > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $aprobacion = $result->fetch_assoc();
    
    if (!$aprobacion) {
        return ['success' => false, 'mensaje' => 'Token inválido o expirado'];
    }
    
    // Actualizar registro de aprobación
    $stmt_update = $conn->prepare("UPDATE aprobaciones 
        SET aprobado_por_id = ?, aprobado_por_nombre = ?, accion = ?, observaciones = ?, fecha_aprobacion = NOW() 
        WHERE id = ?");
    $stmt_update->bind_param("isssi", $aprobador_id, $aprobador_nombre, $accion, $observaciones, $aprobacion['id']);
    $stmt_update->execute();
    
    // Actualizar formulario
    $tipo = $aprobacion['tipo_formulario'];
    $formulario_id = $aprobacion['formulario_id'];
    $nivel = $aprobacion['nivel_aprobacion'];
    
    if ($accion == 'rechazado') {
        // Si se rechaza, finalizar flujo
        actualizarFormularioRechazado($conn, $tipo, $formulario_id, $nivel, $aprobador_nombre, $observaciones);
        notificarFuncionario($conn, $tipo, $formulario_id, 'rechazado', $aprobador_nombre, $observaciones);
        return ['success' => true, 'mensaje' => 'Solicitud rechazada'];
    } else {
        // Si se aprueba, avanzar al siguiente nivel
        if ($nivel == 1) {
            // Aprobado por jefe inmediato, pasar a revisor
            avanzarANivel2($conn, $tipo, $formulario_id, $aprobador_nombre);
        } elseif ($nivel == 2) {
            // Aprobado por revisor, pasar a jefe RRHH
            avanzarANivel3($conn, $tipo, $formulario_id, $aprobador_nombre);
        } elseif ($nivel == 3) {
            // Aprobado por jefe RRHH, finalizar
            finalizarAprobacion($conn, $tipo, $formulario_id, $aprobador_nombre);
            notificarFuncionario($conn, $tipo, $formulario_id, 'aprobado', $aprobador_nombre, $observaciones);
            return ['success' => true, 'mensaje' => 'Solicitud aprobada completamente'];
        }
    }
    
    return ['success' => true, 'mensaje' => 'Aprobación procesada'];
}

/**
 * Avanzar al nivel 2 (Revisor)
 */
function avanzarANivel2($conn, $tipo, $formulario_id, $jefe_nombre) {
    // Actualizar formulario
    actualizarAprobacionNivel($conn, $tipo, $formulario_id, 1, $jefe_nombre);
    actualizarNivelAprobacion($conn, $tipo, $formulario_id, 2);
    
    // Obtener funcionario para email del revisor
    $funcionario = obtenerFuncionarioPorFormulario($conn, $tipo, $formulario_id);
    if ($funcionario && !empty($funcionario['email_revisor'])) {
        $token = generarTokenAprobacion();
        $token = (string)$token; // Asegurar que sea string
        $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+7 days'));
        
        // Log para diagnóstico
        error_log("Token nivel 2 generado (longitud: " . strlen($token) . "): " . substr($token, 0, 20) . "...");
        
        // Crear registro nivel 2
        $stmt = $conn->prepare("INSERT INTO aprobaciones 
            (tipo_formulario, formulario_id, nivel_aprobacion, token_aprobacion, fecha_expiracion_token) 
            VALUES (?, ?, 2, CAST(? AS CHAR(255)), ?)");
        $stmt->bind_param("siss", $tipo, $formulario_id, $token, $fecha_expiracion);
        $stmt->execute();
        
        // Enviar email
        $asunto = "Solicitud Aprobada - Pendiente de Revisión - " . ucfirst($tipo);
        $mensaje = crearEmailSolicitudPendiente($tipo, $funcionario['nombre_completo'], $formulario_id, $token, 'revisor', 2);
        enviarEmail($funcionario['email_revisor'], $asunto, $mensaje);
    }
}

/**
 * Avanzar al nivel 3 (Jefe RRHH)
 */
function avanzarANivel3($conn, $tipo, $formulario_id, $revisor_nombre) {
    // Actualizar formulario
    actualizarAprobacionNivel($conn, $tipo, $formulario_id, 2, $revisor_nombre);
    actualizarNivelAprobacion($conn, $tipo, $formulario_id, 3);
    
    // Obtener funcionario para email del jefe RRHH
    $funcionario = obtenerFuncionarioPorFormulario($conn, $tipo, $formulario_id);
    if ($funcionario && !empty($funcionario['email_jefe_rrhh'])) {
        $token = generarTokenAprobacion();
        $token = (string)$token; // Asegurar que sea string
        $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+7 days'));
        
        // Log para diagnóstico
        error_log("Token nivel 3 generado (longitud: " . strlen($token) . "): " . substr($token, 0, 20) . "...");
        
        // Crear registro nivel 3
        $stmt = $conn->prepare("INSERT INTO aprobaciones 
            (tipo_formulario, formulario_id, nivel_aprobacion, token_aprobacion, fecha_expiracion_token) 
            VALUES (?, ?, 3, CAST(? AS CHAR(255)), ?)");
        $stmt->bind_param("siss", $tipo, $formulario_id, $token, $fecha_expiracion);
        $stmt->execute();
        
        // Enviar email
        $asunto = "Solicitud Pendiente de Aprobación Final - " . ucfirst($tipo);
        $mensaje = crearEmailSolicitudPendiente($tipo, $funcionario['nombre_completo'], $formulario_id, $token, 'jefe_rrhh', 3);
        enviarEmail($funcionario['email_jefe_rrhh'], $asunto, $mensaje);
    }
}

/**
 * Finalizar aprobación (nivel 3 completado)
 */
function finalizarAprobacion($conn, $tipo, $formulario_id, $jefe_rrhh_nombre) {
    actualizarAprobacionNivel($conn, $tipo, $formulario_id, 3, $jefe_rrhh_nombre);
    actualizarEstadoFormulario($conn, $tipo, $formulario_id, 'aprobado');
}

/**
 * Actualizar aprobación de un nivel específico
 */
function actualizarAprobacionNivel($conn, $tipo, $formulario_id, $nivel, $nombre_aprobador) {
    $tabla = obtenerTablaFormulario($tipo);
    $campo_nombre = "nombre_" . obtenerCampoNombrePorNivel($nivel);
    $campo_fecha = "fecha_aprobacion_" . obtenerCampoFechaPorNivel($nivel);
    $campo_aprobado = "aprobado_" . obtenerCampoAprobadoPorNivel($nivel);
    
    $stmt = $conn->prepare("UPDATE {$tabla} 
        SET {$campo_aprobado} = 1, {$campo_nombre} = ?, {$campo_fecha} = NOW() 
        WHERE id = ?");
    $stmt->bind_param("si", $nombre_aprobador, $formulario_id);
    $stmt->execute();
}

/**
 * Actualizar nivel de aprobación actual
 */
function actualizarNivelAprobacion($conn, $tipo, $formulario_id, $nivel) {
    $tabla = obtenerTablaFormulario($tipo);
    $stmt = $conn->prepare("UPDATE {$tabla} SET nivel_aprobacion_actual = ? WHERE id = ?");
    $stmt->bind_param("ii", $nivel, $formulario_id);
    $stmt->execute();
}

/**
 * Actualizar formulario como rechazado
 */
function actualizarFormularioRechazado($conn, $tipo, $formulario_id, $nivel, $nombre_rechazador, $motivo) {
    $tabla = obtenerTablaFormulario($tipo);
    $campo_nombre = "nombre_" . obtenerCampoNombrePorNivel($nivel);
    $campo_fecha = "fecha_aprobacion_" . obtenerCampoFechaPorNivel($nivel);
    
    $stmt = $conn->prepare("UPDATE {$tabla} 
        SET estado = 'rechazado', {$campo_nombre} = ?, {$campo_fecha} = NOW(), motivo_rechazo = ? 
        WHERE id = ?");
    $stmt->bind_param("ssi", $nombre_rechazador, $motivo, $formulario_id);
    $stmt->execute();
}

/**
 * Actualizar estado del formulario
 */
function actualizarEstadoFormulario($conn, $tipo, $formulario_id, $estado) {
    $tabla = obtenerTablaFormulario($tipo);
    $stmt = $conn->prepare("UPDATE {$tabla} SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $estado, $formulario_id);
    $stmt->execute();
}

/**
 * Notificar al funcionario del resultado
 */
function notificarFuncionario($conn, $tipo, $formulario_id, $accion, $aprobador_nombre, $observaciones) {
    $funcionario = obtenerFuncionarioPorFormulario($conn, $tipo, $formulario_id);
    if ($funcionario && !empty($funcionario['email'])) {
        $asunto = "Su Solicitud ha sido " . ucfirst($accion);
        $funcionario_nombre = $funcionario['nombre_completo'] ?? '';
        $mensaje = crearEmailNotificacionFuncionario($tipo, $accion, $aprobador_nombre, $observaciones, $funcionario_nombre);
        enviarEmail($funcionario['email'], $asunto, $mensaje);
    }
}

/**
 * Obtener funcionario por formulario
 */
function obtenerFuncionarioPorFormulario($conn, $tipo, $formulario_id) {
    $tabla = obtenerTablaFormulario($tipo);
    $stmt = $conn->prepare("SELECT funcionario_id FROM {$tabla} WHERE id = ?");
    $stmt->bind_param("i", $formulario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row) {
        return obtenerFuncionario($conn, $row['funcionario_id']);
    }
    return null;
}

/**
 * Obtener nombre de tabla por tipo de formulario
 */
function obtenerTablaFormulario($tipo) {
    $tablas = [
        'permiso' => 'solicitudes_permiso',
        'vacaciones' => 'solicitudes_vacaciones',
        'mision_oficial' => 'misiones_oficiales',
        'jornada_extraordinaria' => 'jornadas_extraordinarias',
        'tiempo_compensatorio' => 'tiempo_compensatorio',
        'reincorporacion' => 'reincorporaciones'
    ];
    return $tablas[$tipo] ?? '';
}

/**
 * Obtener nombre de campo por nivel
 */
function obtenerCampoNombrePorNivel($nivel) {
    $campos = [1 => 'jefe_inmediato', 2 => 'revisor', 3 => 'jefe_rrhh'];
    return $campos[$nivel] ?? '';
}

/**
 * Obtener campo de fecha por nivel
 */
function obtenerCampoFechaPorNivel($nivel) {
    $campos = [1 => 'jefe', 2 => 'revisor', 3 => 'jefe_rrhh'];
    return $campos[$nivel] ?? '';
}

/**
 * Obtener campo de aprobado por nivel
 */
function obtenerCampoAprobadoPorNivel($nivel) {
    $campos = [1 => 'jefe_inmediato', 2 => 'revisor', 3 => 'jefe_rrhh'];
    return $campos[$nivel] ?? '';
}


