<?php
/**
 * FORMULARIO: TIEMPO COMPENSATORIO
 * 
 * Formulario completo integrado con backend PHP
 * Basado en: FORMULARIO DEL USO DE TIEMPO COMPENSATORIO.html
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
    $mensaje = '¡Solicitud de tiempo compensatorio enviada exitosamente!';
    // Limpiar $_POST para que no se muestren valores anteriores
    $_POST = array();
}

// Obtener información del funcionario para pre-llenar el formulario
$conn = conectarDB();
$funcionario = obtenerFuncionario($conn, $funcionario_id);

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $horas = intval($_POST['horas'] ?? 0);
    $dias = intval($_POST['dias'] ?? 0);
    $fecha_uso = limpiarDatos($_POST['fecha_uso'] ?? '');
    $fecha_solicitud = limpiarDatos($_POST['fecha_solicitud'] ?? '');
    $fecha_aprobacion_jefe = limpiarDatos($_POST['fecha_aprobacion_jefe'] ?? '');
    $saldo = limpiarDatos($_POST['saldo'] ?? '');
    $tiempo_tomado = limpiarDatos($_POST['tiempo_tomado'] ?? '');
    $pendiente_por_tomar = limpiarDatos($_POST['pendiente_por_tomar'] ?? '');
    $observaciones = limpiarDatos($_POST['observaciones'] ?? '');
    $registrado_por = limpiarDatos($_POST['registrado_por'] ?? '');
    $fecha_registro = limpiarDatos($_POST['fecha_registro'] ?? '');
    $jefe_oirh = limpiarDatos($_POST['jefe_oirh'] ?? '');
    
    // Validaciones
    if (empty($fecha_uso)) {
        $error = 'Debe especificar la fecha en que hará uso del tiempo compensatorio';
    } elseif ($horas == 0 && $dias == 0) {
        $error = 'Debe especificar al menos horas o días de tiempo compensatorio';
    } else {
        // Guardar el tiempo compensatorio
        $stmt = $conn->prepare("INSERT INTO tiempo_compensatorio 
            (funcionario_id, horas, dias, fecha_uso, fecha_solicitud, fecha_aprobacion_jefe, 
             saldo, tiempo_tomado, pendiente_por_tomar, observaciones, registrado_por, fecha_registro, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");
        
        $fecha_solicitud = !empty($fecha_solicitud) ? $fecha_solicitud : date('Y-m-d');
        $fecha_aprobacion_jefe = !empty($fecha_aprobacion_jefe) ? $fecha_aprobacion_jefe : null;
        $fecha_registro = !empty($fecha_registro) ? $fecha_registro : null;
        
        $stmt->bind_param("iiisssssssss", 
            $funcionario_id, 
            $horas, 
            $dias, 
            $fecha_uso,
            $fecha_solicitud,
            $fecha_aprobacion_jefe,
            $saldo,
            $tiempo_tomado,
            $pendiente_por_tomar,
            $observaciones,
            $registrado_por,
            $fecha_registro
        );
        
        if ($stmt->execute()) {
            $mensaje = '¡Solicitud de tiempo compensatorio enviada exitosamente!';
            // Redirigir para limpiar el formulario completamente
            header('Location: tiempo_compensatorio.php?guardado=1');
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
    <title>Tiempo Compensatorio - Sistema RRHH</title>
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
            background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);
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
                    <p class="text-lg mt-2">SOLICITUD DE USO DE TIEMPO COMPENSATORIO</p>
                </div>
                <div class="bg-white text-orange-800 px-4 py-2 rounded-lg text-center">
                    <p class="text-sm font-semibold">FORMULARIO</p>
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
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>
                    <div>
                        <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                        <input type="text" id="cedula" name="cedula" 
                               value="<?php echo htmlspecialchars($funcionario['cedula'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>
                    <!-- Nivel 2 -->
                    <div>
                        <label for="cargo" class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                        <input type="text" id="cargo" name="cargo" 
                               value="<?php echo htmlspecialchars($funcionario['cargo'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>
                    <div>
                        <label for="posicion" class="block text-sm font-medium text-gray-700 mb-1">N° Posición</label>
                        <input type="text" id="posicion" name="posicion" 
                               value="<?php echo htmlspecialchars($funcionario['numero_posicion'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>
                    <!-- Nivel 3 (Sede más grande) -->
                    <div class="md:col-span-2">
                        <label for="sede" class="block text-sm font-medium text-gray-700 mb-1">Sede: Dirección/Departamento</label>
                        <input type="text" id="sede" name="sede" 
                               value="<?php echo htmlspecialchars($funcionario['sede'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>
                    <!-- Nivel 4 -->
                    <div>
                        <label for="regional" class="block text-sm font-medium text-gray-700 mb-1">Oficina Regional: Provincia/Comarca</label>
                        <input type="text" id="regional" name="regional" 
                               value="<?php echo htmlspecialchars($funcionario['oficina_regional'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>
                    <div>
                        <label for="jefe" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Jefe Inmediato</label>
                        <input type="text" id="jefe" name="jefe" 
                               value="<?php echo htmlspecialchars($funcionario['nombre_jefe_inmediato'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>
                </div>
            </div>

            <!-- Time Request Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Tiempo Solicitado</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-orange-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-orange-800 mb-4 text-center">Tiempo Compensatorio</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="horas" class="block text-sm font-medium text-gray-700 mb-1">Horas</label>
                                <input type="number" id="horas" name="horas" min="0" 
                                       value="<?php echo htmlspecialchars($_POST['horas'] ?? '0'); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition" 
                                       placeholder="0">
                            </div>
                            
                            <div>
                                <label for="dias" class="block text-sm font-medium text-gray-700 mb-1">Días</label>
                                <input type="number" id="dias" name="dias" min="0" max="30" step="1" 
                                       value="<?php echo htmlspecialchars($_POST['dias'] ?? '0'); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition" 
                                       placeholder="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-orange-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-orange-800 mb-4 text-center">Fecha de Uso</h3>
                        
                        <div>
                            <label for="fecha-uso" class="block text-sm font-medium text-gray-700 mb-1">Fecha en que hace uso del tiempo <span class="text-red-500">*</span></label>
                            <input type="date" id="fecha-uso" name="fecha_uso" required
                                   value="<?php echo htmlspecialchars($_POST['fecha_uso'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Autorizaciones</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Nombre del funcionario</h3>
                        <div class="mb-4">
                            <input type="text" name="nombre_funcionario_firma"
                                   class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                                   placeholder="Nombre del funcionario">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_solicitud" 
                                   value="<?php echo !empty($_POST['fecha_solicitud']) ? htmlspecialchars($_POST['fecha_solicitud']) : ''; ?>"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Nombre del Jefe Inmediato</h3>
                        <div class="mb-4">
                            <input type="text" name="nombre_jefe_firma"
                                   class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                                   placeholder="Nombre del Jefe Inmediato">
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_aprobacion_jefe" 
                                   value="<?php echo htmlspecialchars($_POST['fecha_aprobacion_jefe'] ?? ''); ?>"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                </div>
                
            </div>

            <!-- HR Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Para uso de la Oficina Institucional de Recursos Humanos</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label for="saldo" class="block text-sm font-medium text-gray-700 mb-1">Saldo</label>
                            <input type="text" id="saldo" name="saldo" 
                                   value="<?php echo htmlspecialchars($_POST['saldo'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        </div>
                        
                        <div>
                            <label for="tiempo-tomado" class="block text-sm font-medium text-gray-700 mb-1">Tiempo tomado (esta solicitud)</label>
                            <input type="text" id="tiempo-tomado" name="tiempo_tomado" readonly
                                   value="<?php echo htmlspecialchars($_POST['tiempo_tomado'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        </div>
                        
                        <div>
                            <label for="pendiente" class="block text-sm font-medium text-gray-700 mb-1">Pendiente por tomar</label>
                            <input type="text" id="pendiente" name="pendiente_por_tomar" readonly
                                   value="<?php echo htmlspecialchars($_POST['pendiente_por_tomar'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                        </div>
                    </div>
                    
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="4" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition" 
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
                               value="<?php echo htmlspecialchars($_POST['fecha_registro'] ?? ''); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="font-medium text-gray-700 mb-2">Jefe Institucional de Recursos Humanos</h3>
                    <div class="flex items-center">
                        <input type="text" name="jefe_oirh"
                               value="<?php echo htmlspecialchars($_POST['jefe_oirh'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                               placeholder="Jefe institucional de Recursos Humanos">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="../dashboard.php" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-orange-600 text-white font-medium rounded-lg hover:bg-orange-700 transition flex items-center justify-center">
                    <i class="fas fa-paper-plane mr-2"></i> Enviar Solicitud
                </button>
            </div>
        </form>
        
        <!-- Footer Note -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Formulario Oficial - Oficina Institucional de Recursos Humanos</p>
        </div>
    </div>

    <script>
        // Script para funcionalidades adicionales
        document.addEventListener('DOMContentLoaded', function() {
            const horasInput = document.getElementById('horas');
            const diasInput = document.getElementById('dias');
            const tiempoTomadoInput = document.getElementById('tiempo-tomado');
            const saldoInput = document.getElementById('saldo');
            const pendienteInput = document.getElementById('pendiente');
            
            // Calcular automáticamente el tiempo tomado
            function calcularTiempoTomado() {
                const horas = parseFloat(horasInput.value) || 0;
                const dias = parseFloat(diasInput.value) || 0;
                
                // Convertir días a horas (asumiendo 8 horas por día)
                const totalHoras = horas + (dias * 8);
                
                if (totalHoras > 0) {
                    tiempoTomadoInput.value = `${totalHoras} horas`;
                    
                    // Si hay saldo, calcular pendiente
                    if (saldoInput.value) {
                        const saldoMatch = saldoInput.value.match(/(\d+)/);
                        if (saldoMatch) {
                            const saldo = parseFloat(saldoMatch[1]) || 0;
                            const pendiente = saldo - totalHoras;
                            pendienteInput.value = `${pendiente >= 0 ? pendiente : 0} horas`;
                        }
                    } else {
                        pendienteInput.value = '';
                    }
                } else {
                    tiempoTomadoInput.value = '';
                    pendienteInput.value = '';
                }
            }
            
            horasInput.addEventListener('input', calcularTiempoTomado);
            diasInput.addEventListener('input', calcularTiempoTomado);
            saldoInput.addEventListener('input', calcularTiempoTomado);
            
            // Establecer fecha mínima como hoy para la fecha de uso
            const fechaUso = document.getElementById('fecha-uso');
            const today = new Date().toISOString().split('T')[0];
            if (fechaUso) {
                fechaUso.min = today;
            }
            
            // Calcular al cargar la página si hay valores
            calcularTiempoTomado();
        });
    </script>
</body>
</html>

