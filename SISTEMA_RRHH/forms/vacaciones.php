<?php
/**
 * FORMULARIO: SOLICITUD DE VACACIONES
 * 
 * Formulario completo integrado con backend PHP
 * Basado en: SOLICITUD VACACIONES 2025.html
 */

require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/funciones.php';

// Requerir que el usuario esté logueado
requerirLogin();

$mensaje = '';
$error = '';
$funcionario_id = getFuncionarioId();

// Si se guardó exitosamente, mostrar mensaje
if (isset($_GET['guardado']) && $_GET['guardado'] == '1') {
    $mensaje = '¡Solicitud de vacaciones guardada exitosamente!';
    // Limpiar $_POST para que no se muestren valores anteriores
    $_POST = array();
}

// Obtener información del funcionario para pre-llenar el formulario
$conn = conectarDB();
$funcionario = obtenerFuncionario($conn, $funcionario_id);

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dias_declaracion = intval($_POST['dias_declaracion'] ?? 0);
    $fecha_efectiva = limpiarDatos($_POST['fecha_efectiva'] ?? '');
    $fecha_retorno = limpiarDatos($_POST['fecha_retorno'] ?? '');
    $observaciones = limpiarDatos($_POST['observaciones'] ?? '');
    $revisado_por = limpiarDatos($_POST['revisado_por'] ?? '');
    $fecha_revision = limpiarDatos($_POST['fecha_revision'] ?? '');
    $autorizado_oirh = limpiarDatos($_POST['autorizado_oirh'] ?? '');
    $fecha_autorizacion_oirh = limpiarDatos($_POST['fecha_autorizacion_oirh'] ?? '');
    $fecha_firma_funcionario = limpiarDatos($_POST['fecha_firma_funcionario'] ?? '');
    $fecha_firma_jefe = limpiarDatos($_POST['fecha_firma_jefe'] ?? '');
    $vacaciones_detalle = $_POST['vacaciones'] ?? [];
    
    // Validaciones
    if ($dias_declaracion == 0) {
        $error = 'Debe especificar la cantidad de días de vacaciones';
    } elseif (empty($fecha_efectiva)) {
        $error = 'Debe especificar la fecha en que se harán efectivas las vacaciones';
    } elseif (empty($fecha_retorno)) {
        $error = 'Debe especificar la fecha de retorno a sus labores';
    } elseif (empty($vacaciones_detalle) || !is_array($vacaciones_detalle)) {
        $error = 'Debe agregar al menos un período de vacaciones';
    } else {
        // Guardar la solicitud de vacaciones
        $stmt = $conn->prepare("INSERT INTO solicitudes_vacaciones 
            (funcionario_id, dias_declaracion, fecha_efectiva, fecha_retorno, observaciones,
             revisado_por, fecha_revision, autorizado_oirh, fecha_autorizacion_oirh,
             fecha_firma_funcionario, fecha_firma_jefe, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");
        
        $fecha_revision = !empty($fecha_revision) ? $fecha_revision : null;
        $fecha_autorizacion_oirh = !empty($fecha_autorizacion_oirh) ? $fecha_autorizacion_oirh : null;
        $fecha_firma_funcionario = !empty($fecha_firma_funcionario) ? $fecha_firma_funcionario : null;
        $fecha_firma_jefe = !empty($fecha_firma_jefe) ? $fecha_firma_jefe : null;
        
        $stmt->bind_param("iisssssssss", 
            $funcionario_id, 
            $dias_declaracion, 
            $fecha_efectiva,
            $fecha_retorno,
            $observaciones,
            $revisado_por,
            $fecha_revision,
            $autorizado_oirh,
            $fecha_autorizacion_oirh,
            $fecha_firma_funcionario,
            $fecha_firma_jefe
        );
        
        if ($stmt->execute()) {
            $solicitud_id = $stmt->insert_id;
            
            // Guardar cada detalle de vacaciones
            $stmt_detalle = $conn->prepare("INSERT INTO vacaciones_detalle 
                (solicitud_id, resolucion, fecha, dias) 
                VALUES (?, ?, ?, ?)");
            
            foreach ($vacaciones_detalle as $detalle) {
                $resolucion = intval($detalle['resolucion'] ?? 0);
                $fecha = limpiarDatos($detalle['fecha'] ?? '');
                $dias = intval($detalle['dias'] ?? 0);
                
                if ($resolucion > 0 && !empty($fecha) && $dias > 0) {
                    $stmt_detalle->bind_param("iisi", $solicitud_id, $resolucion, $fecha, $dias);
                    $stmt_detalle->execute();
                }
            }
            
            $mensaje = '¡Solicitud de vacaciones guardada exitosamente!';
            // Redirigir para limpiar el formulario completamente
            header('Location: vacaciones.php?guardado=1');
            exit();
        } else {
            $error = 'Error al guardar: ' . $conn->error;
        }
    }
}

cerrarDB($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Vacaciones - Sistema RRHH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
        }
        .form-container {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .header-section {
            background: linear-gradient(135deg, #be185d 0%, #ec4899 100%);
        }
        .table-header {
            background-color: #e0f2fe;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen pb-8">
    <!-- Navbar -->
    <nav class="navbar-custom text-white p-4 mb-6">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Sistema de Recursos Humanos</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm"><?php echo htmlspecialchars($funcionario['nombre_completo'] ?? ''); ?></span>
                <a href="../dashboard.php" class="bg-white text-purple-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                    <i class="fas fa-home mr-2"></i>Menu principal
                </a>
                <a href="../logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto px-4">
        <!-- Mensajes -->
        <?php if ($mensaje): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="header-section text-white rounded-t-xl p-6 mb-1">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold">OFICINA INSTITUCIONAL DE RECURSOS HUMANOS</h1>
                    <p class="text-lg mt-2">SOLICITUD DE VACACIONES</p>
                </div>
                <div class="bg-white text-pink-700 px-4 py-2 rounded-lg text-center">
                    <p class="text-sm font-semibold">FORMULARIO</p>
                    <p class="text-lg font-bold">2025</p>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form method="POST" action="" class="form-container bg-white rounded-b-xl p-8">
            <!-- Información del Funcionario (Encabezado Unificado) -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Información del Funcionario</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nivel 1 -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo del funcionario</label>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?php echo htmlspecialchars($funcionario['nombre_completo'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition">
                    </div>
                    <div>
                        <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                        <input type="text" id="cedula" name="cedula" 
                               value="<?php echo htmlspecialchars($funcionario['cedula'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition">
                    </div>
                    <!-- Nivel 2 -->
                    <div>
                        <label for="cargo" class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                        <input type="text" id="cargo" name="cargo" 
                               value="<?php echo htmlspecialchars($funcionario['cargo'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition">
                    </div>
                    <div>
                        <label for="posicion" class="block text-sm font-medium text-gray-700 mb-1">N° Posición</label>
                        <input type="text" id="posicion" name="posicion" 
                               value="<?php echo htmlspecialchars($funcionario['numero_posicion'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition">
                    </div>
                    <!-- Nivel 3 (Sede más grande) -->
                    <div class="md:col-span-2">
                        <label for="sede" class="block text-sm font-medium text-gray-700 mb-1">Sede: Dirección/Departamento</label>
                        <input type="text" id="sede" name="sede" 
                               value="<?php echo htmlspecialchars($funcionario['sede'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition">
                    </div>
                    <!-- Nivel 4 -->
                    <div>
                        <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Oficina Regional: Provincia/Comarca</label>
                        <input type="text" id="region" name="region" 
                               value="<?php echo htmlspecialchars($funcionario['oficina_regional'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition">
                    </div>
                    <div>
                        <label for="jefe" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Jefe Inmediato</label>
                        <input type="text" id="jefe" name="jefe" 
                               value="<?php echo htmlspecialchars($funcionario['nombre_jefe_inmediato'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition">
                    </div>
                </div>
            </div>

            <!-- Declaración -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Declaración</h2>
                <div class="bg-pink-50 p-6 rounded-lg border border-pink-100">
                    <div class="text-sm text-gray-700 mb-4">
                        <span>Por este medio informo a usted que haré uso de </span>
                        <input type="number" id="dias-declaracion" name="dias_declaracion" min="1" max="999" step="1" required
                               value="<?php echo htmlspecialchars($_POST['dias_declaracion'] ?? ''); ?>"
                               class="mx-1 w-20 px-2 py-1 border border-gray-300 rounded bg-white focus:ring-1 focus:ring-pink-500 focus:border-pink-500 align-middle text-center">
                        <span> días de vacaciones a las que tengo derecho, según el Artículo 95 del Texto Único de 2008, que contiene la Ley N°9 del 20 de junio de 1994.</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="fecha-inicio-vac" class="block text-sm font-medium text-gray-700 mb-1">Las mismas se harán efectivas</label>
                            <input type="date" id="fecha-inicio-vac" name="fecha_efectiva" required
                                   value="<?php echo !empty($_POST['fecha_efectiva']) ? htmlspecialchars($_POST['fecha_efectiva']) : ''; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition">
                        </div>
                        <div>
                            <label for="fecha-retorno" class="block text-sm font-medium text-gray-700 mb-1">Retornando a mis labores en día</label>
                            <input type="date" id="fecha-retorno" name="fecha_retorno" required
                                   value="<?php echo !empty($_POST['fecha_retorno']) ? htmlspecialchars($_POST['fecha_retorno']) : ''; ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vacaciones Solicitadas -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Vacaciones correspondientes a:</h2>
                <p class="text-sm text-gray-600 mb-3">Puede agregar varias líneas de vacaciones. Cada línea corresponde a un periodo solicitado.</p>

                <div class="overflow-x-auto rounded-lg border border-gray-200 bg-sky-50">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="table-header">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Resolución</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Días</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider w-20">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="vacaciones-body">
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-700">Resolución</span>
                                        <input type="number" name="vacaciones[0][resolucion]" min="1" max="99" step="1" required
                                               value="<?php echo htmlspecialchars($_POST['vacaciones'][0]['resolucion'] ?? ''); ?>"
                                               class="px-2 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-pink-500 focus:border-pink-500 w-16 text-center" 
                                               placeholder="0">
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input type="date" name="vacaciones[0][fecha]" required
                                           value="<?php echo !empty($_POST['vacaciones'][0]['fecha']) ? htmlspecialchars($_POST['vacaciones'][0]['fecha']) : ''; ?>"
                                           class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-pink-500 focus:border-pink-500">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <input type="number" name="vacaciones[0][dias]" min="1" max="999" step="1" required
                                               value="<?php echo htmlspecialchars($_POST['vacaciones'][0]['dias'] ?? ''); ?>"
                                               class="px-2 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-pink-500 focus:border-pink-500 w-20 text-center" 
                                               placeholder="0">
                                        <span class="text-sm text-gray-700">días</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <button type="button" class="text-red-500 hover:text-red-700 remove-vac-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="button" id="add-vac-row" class="flex items-center text-pink-600 hover:text-pink-800 font-medium">
                        <i class="fas fa-plus-circle mr-2"></i> Agregar vacaciones
                    </button>
                </div>
            </div>

            <!-- Observaciones -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Observaciones</h2>
                <textarea id="observaciones" name="observaciones" rows="4" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition" 
                          placeholder="Observaciones..."><?php echo htmlspecialchars($_POST['observaciones'] ?? ''); ?></textarea>
            </div>

            <!-- Autorizaciones -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Autorizaciones</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-4">Nombre del funcionario</h3>
                        <div class="mb-4">
                            <input type="text" name="nombre_funcionario_firma"
                                   class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus-border-red-400 transition"
                                   placeholder="Nombre del funcionario">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_firma_funcionario" 
                                   value="<?php echo !empty($_POST['fecha_firma_funcionario']) ? htmlspecialchars($_POST['fecha_firma_funcionario']) : ''; ?>"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-4">Nombre del Jefe Inmediato</h3>
                        <div class="mb-4">
                            <input type="text" name="nombre_jefe_firma"
                                   class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus-border-red-400 transition"
                                   placeholder="Nombre del Jefe Inmediato">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_firma_jefe" 
                                   value="<?php echo !empty($_POST['fecha_firma_jefe']) ? htmlspecialchars($_POST['fecha_firma_jefe']) : ''; ?>"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Para uso de la OIRH (firma y nota) -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Para uso de la Oficina Institucional de Recursos Humanos</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Revisado por:</h3>
                        <input type="text" name="director_area_oirh"
                               class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 text-gray-600 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition mb-4"
                               placeholder="Revisado por">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_director_area_oirh"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Jefe Institucional de Recursos Humanos</h3>
                        <input type="text" name="jefe_rrhh_oirh"
                               class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition mb-4"
                               placeholder="Jefe Institucional de Recursos Humanos">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_jefe_rrhh_oirh"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <p class="text-sm text-gray-700">
                        <strong>NOTA:</strong> Las solicitudes deben ser enviadas a Recursos Humanos con fin si de ante situación a la fecha de hacer efectivo el derecho.
                    </p>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="../dashboard.php" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-pink-600 text-white font-medium rounded-lg hover:bg-pink-700 transition flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i> Guardar Solicitud
                </button>
            </div>
        </form>
        
        <!-- Footer Note -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Formulario Oficial - Oficina Institucional de Recursos Humanos © 2025</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.getElementById('vacaciones-body');
            const addBtn = document.getElementById('add-vac-row');
            let rowCount = 0;

            if (addBtn) {
                addBtn.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    rowCount++;
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700">Resolución</span>
                                <input type="number" name="vacaciones[${rowCount}][resolucion]" min="1" max="99" step="1" required
                                       class="px-2 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-pink-500 focus:border-pink-500 w-16 text-center" 
                                       placeholder="0">
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="date" name="vacaciones[${rowCount}][fecha]" required
                                   class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-pink-500 focus:border-pink-500">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <input type="number" name="vacaciones[${rowCount}][dias]" min="1" max="999" step="1" required
                                       class="px-2 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-pink-500 focus:border-pink-500 w-20 text-center" 
                                       placeholder="0">
                                <span class="text-sm text-gray-700">días</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <button type="button" class="text-red-500 hover:text-red-700 remove-vac-row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    body.appendChild(row);
                    return false;
                };
            }

            if (body) {
                body.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-vac-row')) {
                        const row = e.target.closest('tr');
                        if (body.children.length > 1) {
                            row.remove();
                        } else {
                            alert('Debe haber al menos una línea de vacaciones');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>

