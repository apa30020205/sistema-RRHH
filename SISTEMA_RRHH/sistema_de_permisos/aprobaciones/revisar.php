<?php
/**
 * INTERFAZ DE REVISIÓN Y APROBACIÓN
 * 
 * Permite a jefes, revisores y jefe RRHH aprobar/rechazar solicitudes
 */

require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/funciones.php';
require_once '../includes/aprobaciones.php';

$mensaje = '';
$error = '';
$solicitud = null;
$formulario_datos = null;

// Obtener token de la URL
$token = $_GET['token'] ?? '';
$token = trim($token); // Limpiar espacios

if (empty($token)) {
    $error = 'Token de aprobación no válido';
} else {
    $conn = conectarDB();
    
    // Primero buscar sin verificar expiración para diagnóstico
    $stmt = $conn->prepare("SELECT * FROM aprobaciones WHERE token_aprobacion = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $solicitud = $result->fetch_assoc();
    
    if (!$solicitud) {
        $error = 'Token inválido o expirado. Por favor, contacte al sistema de recursos humanos.';
        // Log para diagnóstico
        error_log("Token no encontrado: " . substr($token, 0, 20) . "...");
    } else {
        // Verificar expiración
        if ($solicitud['fecha_expiracion_token'] && strtotime($solicitud['fecha_expiracion_token']) <= time()) {
            $error = 'Token inválido o expirado. Por favor, contacte al sistema de recursos humanos.';
            error_log("Token expirado: ID " . $solicitud['id']);
        } else {
            // Obtener datos del formulario
            $formulario_datos = obtenerDatosFormulario($conn, $solicitud['tipo_formulario'], $solicitud['formulario_id']);
            
            // Si ya fue procesado
            if (!empty($solicitud['accion'])) {
                $mensaje = 'Esta solicitud ya fue procesada.';
            }
        }
    }
    
    // Procesar aprobación/rechazo
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($solicitud) && empty($solicitud['accion'])) {
        $accion = $_POST['accion'] ?? '';
        $aprobador_nombre = limpiarDatos($_POST['aprobador_nombre'] ?? '');
        $observaciones = limpiarDatos($_POST['observaciones'] ?? '');
        
        if (empty($accion) || empty($aprobador_nombre)) {
            $error = 'Debe especificar su nombre y seleccionar una acción';
        } else {
            $resultado = procesarAprobacion($conn, $token, $accion, null, $aprobador_nombre, $observaciones);
            
            if ($resultado['success']) {
                $mensaje = $resultado['mensaje'];
                // Recargar datos
                $stmt = $conn->prepare("SELECT * FROM aprobaciones WHERE token_aprobacion = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $result = $stmt->get_result();
                $solicitud = $result->fetch_assoc();
            } else {
                $error = $resultado['mensaje'];
            }
        }
    }
    
    cerrarDB($conn);
}

/**
 * Obtener datos del formulario según su tipo
 */
function obtenerDatosFormulario($conn, $tipo, $formulario_id) {
    $tabla = obtenerTablaFormulario($tipo);
    $stmt = $conn->prepare("SELECT f.*, func.nombre_completo, func.cedula, func.cargo 
                           FROM {$tabla} f 
                           INNER JOIN funcionarios func ON f.funcionario_id = func.id 
                           WHERE f.id = ?");
    $stmt->bind_param("i", $formulario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Renderizar vista del formulario según su tipo
 */
function renderizarFormulario($tipo, $datos) {
    switch ($tipo) {
        case 'permiso':
            return renderizarPermiso($datos);
        case 'vacaciones':
            return renderizarVacaciones($datos);
        case 'mision_oficial':
            return renderizarMisionOficial($datos);
        case 'jornada_extraordinaria':
            return renderizarJornadaExtraordinaria($datos);
        case 'tiempo_compensatorio':
            return renderizarTiempoCompensatorio($datos);
        case 'reincorporacion':
            return renderizarReincorporacion($datos);
        default:
            return '<p>Tipo de formulario no reconocido</p>';
    }
}

// Funciones de renderizado (simplificadas, se pueden expandir)
function renderizarPermiso($datos) {
    return "
    <div class='bg-white p-6 rounded-lg shadow'>
        <h3 class='text-xl font-bold mb-4'>Solicitud de Permiso</h3>
        <div class='grid grid-cols-2 gap-4'>
            <div><strong>Funcionario:</strong> {$datos['nombre_completo']}</div>
            <div><strong>Cédula:</strong> {$datos['cedula']}</div>
            <div><strong>Motivo:</strong> {$datos['motivo']}</div>
            <div><strong>Desde:</strong> {$datos['desde_dia']}/{$datos['desde_mes']}/{$datos['desde_anio']}</div>
            <div><strong>Hasta:</strong> {$datos['hasta_dia']}/{$datos['hasta_mes']}/{$datos['hasta_anio']}</div>
        </div>
    </div>";
}

function renderizarVacaciones($datos) {
    return "
    <div class='bg-white p-6 rounded-lg shadow'>
        <h3 class='text-xl font-bold mb-4'>Solicitud de Vacaciones</h3>
        <div class='grid grid-cols-2 gap-4'>
            <div><strong>Funcionario:</strong> {$datos['nombre_completo']}</div>
            <div><strong>Días:</strong> {$datos['dias_declaracion']}</div>
            <div><strong>Fecha Efectiva:</strong> {$datos['fecha_efectiva']}</div>
            <div><strong>Fecha Retorno:</strong> {$datos['fecha_retorno']}</div>
        </div>
    </div>";
}

function renderizarMisionOficial($datos) {
    return "
    <div class='bg-white p-6 rounded-lg shadow'>
        <h3 class='text-xl font-bold mb-4'>Misión Oficial</h3>
        <div class='grid grid-cols-2 gap-4'>
            <div><strong>Funcionario:</strong> {$datos['nombre_completo']}</div>
            <div><strong>Fecha:</strong> {$datos['fecha_mision']}</div>
            <div><strong>Desde:</strong> {$datos['desde_hora']}</div>
            <div><strong>Hasta:</strong> {$datos['hasta_hora']}</div>
            <div class='col-span-2'><strong>Motivo:</strong> {$datos['motivo']}</div>
        </div>
    </div>";
}

function renderizarJornadaExtraordinaria($datos) {
    return "
    <div class='bg-white p-6 rounded-lg shadow'>
        <h3 class='text-xl font-bold mb-4'>Jornada Extraordinaria</h3>
        <div class='grid grid-cols-2 gap-4'>
            <div><strong>Funcionario:</strong> {$datos['nombre_completo']}</div>
            <div><strong>Justificación:</strong> {$datos['justificacion']}</div>
        </div>
    </div>";
}

function renderizarTiempoCompensatorio($datos) {
    return "
    <div class='bg-white p-6 rounded-lg shadow'>
        <h3 class='text-xl font-bold mb-4'>Tiempo Compensatorio</h3>
        <div class='grid grid-cols-2 gap-4'>
            <div><strong>Funcionario:</strong> {$datos['nombre_completo']}</div>
            <div><strong>Horas:</strong> {$datos['horas']}</div>
            <div><strong>Días:</strong> {$datos['dias']}</div>
            <div><strong>Fecha Uso:</strong> {$datos['fecha_uso']}</div>
        </div>
    </div>";
}

function renderizarReincorporacion($datos) {
    return "
    <div class='bg-white p-6 rounded-lg shadow'>
        <h3 class='text-xl font-bold mb-4'>Reincorporación</h3>
        <div class='grid grid-cols-2 gap-4'>
            <div><strong>Funcionario:</strong> {$datos['nombre_completo']}</div>
            <div><strong>Motivo Ausencia:</strong> {$datos['motivo_ausencia']}</div>
            <div><strong>Fecha Reincorporación:</strong> {$datos['fecha_reincorporacion']}</div>
        </div>
    </div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Solicitud - Sistema RRHH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-2xl font-bold mb-6">Revisar Solicitud</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($mensaje): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($solicitud && $formulario_datos): ?>
                <?php if (empty($solicitud['accion'])): ?>
                    <!-- Mostrar formulario -->
                    <?php echo renderizarFormulario($solicitud['tipo_formulario'], $formulario_datos); ?>
                    
                    <!-- Formulario de aprobación -->
                    <form method="POST" class="mt-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Su Nombre Completo *</label>
                            <input type="text" name="aprobador_nombre" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones (opcional)</label>
                            <textarea name="observaciones" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"></textarea>
                        </div>
                        
                        <div class="flex gap-4">
                            <button type="submit" name="accion" value="aprobado"
                                    class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
                                <i class="fas fa-check mr-2"></i>Aprobar
                            </button>
                            <button type="submit" name="accion" value="rechazado"
                                    class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-medium">
                                <i class="fas fa-times mr-2"></i>Rechazar
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-2">Estado de la Solicitud</h3>
                        <p><strong>Acción:</strong> <?php echo ucfirst($solicitud['accion']); ?></p>
                        <p><strong>Aprobado por:</strong> <?php echo htmlspecialchars($solicitud['aprobado_por_nombre']); ?></p>
                        <p><strong>Fecha:</strong> <?php echo $solicitud['fecha_aprobacion']; ?></p>
                        <?php if (!empty($solicitud['observaciones'])): ?>
                            <p><strong>Observaciones:</strong> <?php echo htmlspecialchars($solicitud['observaciones']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


