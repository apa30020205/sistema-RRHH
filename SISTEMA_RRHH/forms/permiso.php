<?php
/**
 * FORMULARIO: SOLICITUD DE PERMISO
 * 
 * Formulario completo integrado con backend PHP
 * Basado en: SOLICITUD DE PERMISO 2025.html
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
    $mensaje = '¡Solicitud de permiso guardada exitosamente!';
    // Limpiar $_POST para que no se muestren valores anteriores
    $_POST = array();
}

// Obtener información del funcionario para pre-llenar el formulario
$conn = conectarDB();
$funcionario = obtenerFuncionario($conn, $funcionario_id);

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener motivo seleccionado (radio button - solo uno)
    $motivo_texto = limpiarDatos($_POST['motivo'] ?? '');
    $motivo_otros = limpiarDatos($_POST['motivo_otros'] ?? '');
    
    // Fechas y horas
    $desde_hora = limpiarDatos($_POST['desde_hora'] ?? '');
    $desde_fecha = limpiarDatos($_POST['desde_fecha'] ?? '');
    $hasta_hora = limpiarDatos($_POST['hasta_hora'] ?? '');
    $hasta_fecha = limpiarDatos($_POST['hasta_fecha'] ?? '');
    
    // Convertir fechas completas a día, mes, año para la base de datos
    $desde_dia = 0;
    $desde_mes = 0;
    $desde_anio = 0;
    if (!empty($desde_fecha)) {
        $fecha_desde = DateTime::createFromFormat('Y-m-d', $desde_fecha);
        if ($fecha_desde) {
            $desde_dia = intval($fecha_desde->format('d'));
            $desde_mes = intval($fecha_desde->format('m'));
            $desde_anio = intval($fecha_desde->format('Y'));
        }
    }
    
    $hasta_dia = 0;
    $hasta_mes = 0;
    $hasta_anio = 0;
    if (!empty($hasta_fecha)) {
        $fecha_hasta = DateTime::createFromFormat('Y-m-d', $hasta_fecha);
        if ($fecha_hasta) {
            $hasta_dia = intval($fecha_hasta->format('d'));
            $hasta_mes = intval($fecha_hasta->format('m'));
            $hasta_anio = intval($fecha_hasta->format('Y'));
        }
    }
    
    // Otros campos
    $fecha_solicitud = limpiarDatos($_POST['fecha_solicitud'] ?? '');
    $fecha_firma_jefe = limpiarDatos($_POST['fecha_firma_jefe'] ?? '');
    $total = limpiarDatos($_POST['total'] ?? '');
    $utilizado = limpiarDatos($_POST['utilizado'] ?? '');
    $saldo = limpiarDatos($_POST['saldo'] ?? '');
    $observaciones = limpiarDatos($_POST['observaciones'] ?? '');
    $registrado_por = limpiarDatos($_POST['registrado_por'] ?? '');
    $fecha_registro = limpiarDatos($_POST['fecha_registro'] ?? '');
    $enterado = limpiarDatos($_POST['enterado'] ?? '');
    
    // Validaciones
    if (empty($motivo_texto)) {
        $error = 'Debe seleccionar un motivo para el permiso';
    } elseif (empty($desde_fecha)) {
        $error = 'Debe especificar la fecha de inicio del permiso';
    } elseif (empty($hasta_fecha)) {
        $error = 'Debe especificar la fecha de fin del permiso';
    } else {
        // Guardar la solicitud de permiso
        $stmt = $conn->prepare("INSERT INTO solicitudes_permiso 
            (funcionario_id, motivo, motivo_otros, desde_hora, desde_dia, desde_mes, desde_anio,
             hasta_hora, hasta_dia, hasta_mes, hasta_anio, fecha_solicitud, fecha_firma_jefe,
             total, utilizado, saldo, observaciones, registrado_por, fecha_registro, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");
        
        $desde_hora = !empty($desde_hora) ? $desde_hora : null;
        $hasta_hora = !empty($hasta_hora) ? $hasta_hora : null;
        $fecha_solicitud = !empty($fecha_solicitud) ? $fecha_solicitud : date('Y-m-d');
        $fecha_firma_jefe = !empty($fecha_firma_jefe) ? $fecha_firma_jefe : null;
        $fecha_registro = !empty($fecha_registro) ? $fecha_registro : null;
        
        $stmt->bind_param("isssiiisiiisssssssss", 
            $funcionario_id, 
            $motivo_texto, 
            $motivo_otros,
            $desde_hora,
            $desde_dia,
            $desde_mes,
            $desde_anio,
            $hasta_hora,
            $hasta_dia,
            $hasta_mes,
            $hasta_anio,
            $fecha_solicitud,
            $fecha_firma_jefe,
            $total,
            $utilizado,
            $saldo,
            $observaciones,
            $registrado_por,
            $fecha_registro
        );
        
        if ($stmt->execute()) {
            $mensaje = '¡Solicitud de permiso guardada exitosamente!';
            // Redirigir para limpiar el formulario completamente
            header('Location: permiso.php?guardado=1');
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
    <title>Solicitud de Permiso - Sistema RRHH</title>
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
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        }
        .checkbox-container {
            transition: all 0.2s ease;
        }
        .checkbox-container:hover {
            background-color: #f0fdf4;
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

    <div class="max-w-4xl mx-auto px-4">
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
                    <p class="text-lg mt-2">SOLICITUD DE PERMISO</p>
                </div>
                <div class="bg-white text-green-800 px-4 py-2 rounded-lg text-center">
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
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                    <div>
                        <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                        <input type="text" id="cedula" name="cedula" 
                               value="<?php echo htmlspecialchars($funcionario['cedula'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                    <!-- Nivel 2 -->
                    <div>
                        <label for="cargo" class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                        <input type="text" id="cargo" name="cargo" 
                               value="<?php echo htmlspecialchars($funcionario['cargo'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                    <div>
                        <label for="posicion" class="block text-sm font-medium text-gray-700 mb-1">N° Posición</label>
                        <input type="text" id="posicion" name="posicion" 
                               value="<?php echo htmlspecialchars($funcionario['numero_posicion'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                    <!-- Nivel 3 (Sede más grande) -->
                    <div class="md:col-span-2">
                        <label for="sede" class="block text-sm font-medium text-gray-700 mb-1">Sede: Dirección/Departamento</label>
                        <input type="text" id="sede" name="sede" 
                               value="<?php echo htmlspecialchars($funcionario['sede'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                    <!-- Nivel 4 -->
                    <div>
                        <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Oficina Regional: Provincia/Comarca</label>
                        <input type="text" id="region" name="region" 
                               value="<?php echo htmlspecialchars($funcionario['oficina_regional'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                    <div>
                        <label for="jefe" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Jefe Inmediato</label>
                        <input type="text" id="jefe" name="jefe" 
                               value="<?php echo htmlspecialchars($funcionario['nombre_jefe_inmediato'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                </div>
            </div>

            <!-- Permission Reasons Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Motivo del Permiso</h2>
                <p class="text-sm text-gray-600 mb-4">Solicito permiso para ausentarme de mi trabajo por motivos de:</p>
                
                <div class="space-y-3">
                    <div class="checkbox-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="enfermedad" name="motivo" value="enfermedad" 
                               class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'enfermedad') ? 'checked' : ''; ?>>
                        <label for="enfermedad" class="ml-3 block text-sm font-medium text-gray-700">Enfermedad</label>
                    </div>
                    
                    <div class="checkbox-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="duelo" name="motivo" value="duelo" 
                               class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'duelo') ? 'checked' : ''; ?>>
                        <label for="duelo" class="ml-3 block text-sm font-medium text-gray-700">Duelo</label>
                    </div>
                    
                    <div class="checkbox-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="matrimonio" name="motivo" value="matrimonio" 
                               class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'matrimonio') ? 'checked' : ''; ?>>
                        <label for="matrimonio" class="ml-3 block text-sm font-medium text-gray-700">Matrimonio</label>
                    </div>
                    
                    <div class="checkbox-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="nacimiento" name="motivo" value="nacimiento" 
                               class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'nacimiento') ? 'checked' : ''; ?>>
                        <label for="nacimiento" class="ml-3 block text-sm font-medium text-gray-700">Nacimiento de hijos</label>
                    </div>
                    
                    <div class="checkbox-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="enfermedad-familiares" name="motivo" value="enfermedad-familiares" 
                               class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'enfermedad-familiares') ? 'checked' : ''; ?>>
                        <label for="enfermedad-familiares" class="ml-3 block text-sm font-medium text-gray-700">Enfermedad de parientes cercanos</label>
                    </div>
                    
                    <div class="checkbox-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="eventos-academicos" name="motivo" value="eventos-academicos" 
                               class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'eventos-academicos') ? 'checked' : ''; ?>>
                        <label for="eventos-academicos" class="ml-3 block text-sm font-medium text-gray-700">Eventos académicos puntuales</label>
                    </div>
                    
                    <div class="checkbox-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="otros" name="motivo" value="otros" 
                               class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'otros') ? 'checked' : ''; ?>>
                        <label for="otros" class="ml-3 block text-sm font-medium text-gray-700">Otros asuntos personales</label>
                    </div>
                    
                    <div class="mt-2 ml-8">
                        <label for="especificar" class="block text-sm font-medium text-gray-700 mb-1">Especifique:</label>
                        <input type="text" id="especificar" name="motivo_otros" 
                               value="<?php echo htmlspecialchars($_POST['motivo_otros'] ?? ''); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                </div>
            </div>

            <!-- Date Range Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Período de Permiso</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-green-800 mb-4 text-center">Desde</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="desde-horas" class="block text-sm font-medium text-gray-700 mb-1">Horas</label>
                                <input type="time" id="desde-horas" name="desde_hora" 
                                       value="<?php echo htmlspecialchars($_POST['desde_hora'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            </div>
                            
                            <div>
                                <label for="desde-fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                <input type="date" id="desde-fecha" name="desde_fecha" required
                                       value="<?php echo !empty($_POST['desde_fecha']) ? htmlspecialchars($_POST['desde_fecha']) : ''; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-green-800 mb-4 text-center">Hasta</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="hasta-horas" class="block text-sm font-medium text-gray-700 mb-1">Horas</label>
                                <input type="time" id="hasta-horas" name="hasta_hora" 
                                       value="<?php echo htmlspecialchars($_POST['hasta_hora'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            </div>
                            
                            <div>
                                <label for="hasta-fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                <input type="date" id="hasta-fecha" name="hasta_fecha" required
                                       value="<?php echo !empty($_POST['hasta_fecha']) ? htmlspecialchars($_POST['hasta_fecha']) : ''; ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Firmas</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-4">Nombre del Funcionario</h3>
                        <div class="mb-3">
                            <input type="text" name="nombre_funcionario_firma"
                                   class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                                   placeholder="Nombre del Funcionario">
                        </div>
                        <span class="text-xs text-gray-500 block mb-4">Nombre del Funcionario</span>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_solicitud" 
                                   value="<?php echo !empty($_POST['fecha_solicitud']) ? htmlspecialchars($_POST['fecha_solicitud']) : ''; ?>"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-4">Nombre del Jefe Inmediato</h3>
                        <div class="mb-3">
                            <input type="text" name="nombre_jefe_firma"
                                   class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                                   placeholder="Nombre del Jefe Inmediato">
                        </div>
                        <span class="text-xs text-gray-500 block mb-4">Nombre del Jefe Inmediato</span>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_firma_jefe" 
                                   value="<?php echo htmlspecialchars($_POST['fecha_firma_jefe'] ?? ''); ?>"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                </div>
            </div>

            <!-- HR Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Para uso de la OIRH</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label for="total" class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                            <input type="text" id="total" name="total" 
                                   value="<?php echo htmlspecialchars($_POST['total'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                        </div>
                        
                        <div>
                            <label for="utilizado" class="block text-sm font-medium text-gray-700 mb-1">Utilizado a la fecha</label>
                            <input type="text" id="utilizado" name="utilizado" 
                                   value="<?php echo htmlspecialchars($_POST['utilizado'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                        </div>
                        
                        <div>
                            <label for="saldo" class="block text-sm font-medium text-gray-700 mb-1">Saldo</label>
                            <input type="text" id="saldo" name="saldo" readonly
                                   value="<?php echo htmlspecialchars($_POST['saldo'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                        </div>
                    </div>
                    
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="4" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition" 
                                  placeholder="Observaciones..."><?php echo htmlspecialchars($_POST['observaciones'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="registrado" class="block text-sm font-medium text-gray-700 mb-1">Registrado</label>
                        <input type="text" id="registrado" name="registrado_por" 
                                   value="<?php echo htmlspecialchars($_POST['registrado_por'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                                   placeholder="Nombre">
                    </div>
                    
                    <div>
                        <label for="fecha-registro" class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" id="fecha-registro" name="fecha_registro" 
                               value="<?php echo !empty($_POST['fecha_registro']) ? htmlspecialchars($_POST['fecha_registro']) : ''; ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                </div>
                <div class="mt-6">
                    <h3 class="font-medium text-gray-700 mb-2">Jefe Institucional de Recursos Humanos</h3>
                    <div class="flex items-center">
                        <input type="text" name="jefe_oirh" 
                               value="<?php echo htmlspecialchars($_POST['jefe_oirh'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition" 
                               placeholder="Jefe Institucional de Recursos Humanos">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="../dashboard.php" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition flex items-center justify-center">
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
        // Script para manejar la interacción de los radio buttons
        document.addEventListener('DOMContentLoaded', function() {
            const otrosRadio = document.getElementById('otros');
            const especificarInput = document.getElementById('especificar');
            const motivosRadios = document.querySelectorAll('input[name="motivo"]');
            
            // Habilitar/deshabilitar campo de especificación
            function actualizarCampoEspecificar() {
                if (otrosRadio && especificarInput) {
                    if (otrosRadio.checked) {
                        especificarInput.disabled = false;
                        especificarInput.focus();
                    } else {
                        especificarInput.disabled = true;
                        if (!otrosRadio.checked) {
                            especificarInput.value = '';
                        }
                    }
                }
            }
            
            // Agregar listener a todos los radio buttons
            if (motivosRadios) {
                motivosRadios.forEach(function(radio) {
                    radio.addEventListener('change', actualizarCampoEspecificar);
                });
            }
            
            // Inicialmente verificar el estado
            actualizarCampoEspecificar();
            
            // Calcular automáticamente el saldo
            const totalInput = document.getElementById('total');
            const utilizadoInput = document.getElementById('utilizado');
            const saldoInput = document.getElementById('saldo');
            
            function calcularSaldo() {
                if (totalInput && utilizadoInput && saldoInput) {
                    const total = parseFloat(totalInput.value) || 0;
                    const utilizado = parseFloat(utilizadoInput.value) || 0;
                    saldoInput.value = (total - utilizado).toFixed(2);
                }
            }
            
            if (totalInput) totalInput.addEventListener('input', calcularSaldo);
            if (utilizadoInput) utilizadoInput.addEventListener('input', calcularSaldo);
        });
    </script>
</body>
</html>

