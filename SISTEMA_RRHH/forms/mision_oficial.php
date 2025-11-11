<?php
/**
 * FORMULARIO: MISIÓN OFICIAL
 * 
 * Formulario completo integrado con backend PHP
 * Basado en: FORMULARIO DE MISIÓN OFICIAL.html
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
    $mensaje = '¡Misión oficial registrada exitosamente!';
    // Limpiar $_POST para que no se muestren valores anteriores
    $_POST = array();
}

// Obtener información del funcionario para pre-llenar el formulario
$conn = conectarDB();
$funcionario = obtenerFuncionario($conn, $funcionario_id);

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha_mision = limpiarDatos($_POST['fecha_mision'] ?? '');
    $desde_hora = limpiarDatos($_POST['desde_hora'] ?? '');
    $hasta_hora = limpiarDatos($_POST['hasta_hora'] ?? '');
    $motivo = limpiarDatos($_POST['motivo'] ?? '');
    $fecha_solicitud = limpiarDatos($_POST['fecha_solicitud'] ?? '');
    $revisado_por = limpiarDatos($_POST['revisado_por'] ?? '');
    $fecha_revision = limpiarDatos($_POST['fecha_revision'] ?? '');
    $observaciones = limpiarDatos($_POST['observaciones'] ?? '');
    
    // Validaciones
    if (empty($fecha_mision)) {
        $error = 'Debe seleccionar la fecha de la misión';
    } elseif (empty($desde_hora) || empty($hasta_hora)) {
        $error = 'Debe especificar las horas de inicio y fin';
    } elseif ($desde_hora >= $hasta_hora) {
        $error = 'La hora de fin debe ser posterior a la hora de inicio';
    } elseif (empty($motivo)) {
        $error = 'Debe proporcionar el motivo de la misión';
    } else {
        // Guardar la misión oficial
        $stmt = $conn->prepare("INSERT INTO misiones_oficiales 
            (funcionario_id, fecha_mision, desde_hora, hasta_hora, motivo, fecha_solicitud, 
             revisado_por, fecha_revision, observaciones, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");
        
        $fecha_solicitud = !empty($fecha_solicitud) ? $fecha_solicitud : date('Y-m-d');
        $fecha_revision = !empty($fecha_revision) ? $fecha_revision : null;
        
        $stmt->bind_param("issssssss", 
            $funcionario_id, 
            $fecha_mision, 
            $desde_hora, 
            $hasta_hora, 
            $motivo,
            $fecha_solicitud,
            $revisado_por,
            $fecha_revision,
            $observaciones
        );
        
        if ($stmt->execute()) {
            $mensaje = '¡Misión oficial registrada exitosamente!';
            // Redirigir para limpiar el formulario completamente
            header('Location: mision_oficial.php?guardado=1');
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
    <title>Misión Oficial - Sistema RRHH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7fa;
        }
        .form-container {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .header-section {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        /* Mejorar selector de hora - OCULTAR el indicador nativo del navegador */
        input[type="time"] {
            -webkit-appearance: none;
            -moz-appearance: textfield;
            appearance: none;
            min-height: 45px;
            cursor: pointer;
        }
        /* OCULTAR el indicador nativo del navegador - solo usamos el ícono de Font Awesome */
        input[type="time"]::-webkit-calendar-picker-indicator {
            display: none !important;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
        }
        input[type="time"]::-webkit-inner-spin-button,
        input[type="time"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type="time"]:hover {
            border-color: #ef4444;
        }
        input[type="time"]:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        /* Asegurar que el label con el ícono sea visible y no bloquee los clics */
        label[for^="desde-hora"],
        label[for^="hasta-hora"] {
            pointer-events: none !important;
            z-index: 10 !important;
        }
        label[for^="desde-hora"] i,
        label[for^="hasta-hora"] i {
            font-size: 18px !important;
            color: #6b7280 !important;
            display: block !important;
            visibility: visible !important;
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
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($mensaje): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <i class="fas fa-check-circle me-2"></i><?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="header-section text-white rounded-t-xl p-6 mb-1">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold">OFICINA INSTITUCIONAL DE RECURSOS HUMANOS</h1>
                    <p class="text-lg mt-2">MISIÓN OFICIAL</p>
                </div>
                <div class="bg-white text-red-800 px-4 py-2 rounded-lg text-center">
                    <p class="text-sm font-semibold">FORMULARIO</p>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <div class="form-container bg-white rounded-b-xl p-8">
            <form method="POST" action="" id="formMision">
                <!-- Información del Funcionario (Pre-llena automáticamente) -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Información del Funcionario</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo del funcionario</label>
                            <input type="text" value="<?php echo htmlspecialchars($funcionario['nombre_completo']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                            <input type="text" value="<?php echo htmlspecialchars($funcionario['cedula']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                            <input type="text" value="<?php echo htmlspecialchars($funcionario['cargo']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">N° Posición</label>
                            <input type="text" value="<?php echo htmlspecialchars($funcionario['numero_posicion']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sede: Dirección/Departamento</label>
                            <input type="text" value="<?php echo htmlspecialchars($funcionario['sede']); ?>" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-green-50" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Oficina Regional: Provincia/Comarca</label>
                            <input type="text" value="<?php echo htmlspecialchars($funcionario['oficina_regional']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Jefe Inmediato</label>
                            <input type="text" value="<?php echo htmlspecialchars($funcionario['nombre_jefe_inmediato']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                        </div>
                    </div>
                </div>

                <!-- Detalles de la Misión Oficial -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Detalles de la Misión Oficial</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
                        <!-- Fecha - Ocupa 5 columnas -->
                        <div class="md:col-span-5">
                            <label for="fecha-mision" class="block text-sm font-medium text-gray-700 mb-1 whitespace-nowrap">Fecha en que realizará la misión oficial</label>
                            <input type="date" id="fecha-mision" name="fecha_mision" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                   value="<?php echo $_POST['fecha_mision'] ?? ''; ?>">
                        </div>
                        
                        <!-- Desde Hora - Ocupa 3 columnas -->
                        <div class="md:col-span-3">
                            <label for="desde-hora" class="block text-sm font-medium text-gray-700 mb-1">Desde (Hora)</label>
                            <div class="relative">
                                <input type="time" id="desde-hora" name="desde_hora" required
                                       class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                       style="font-size: 16px; padding: 10px 45px 10px 10px; cursor: pointer;"
                                       value="<?php echo $_POST['desde_hora'] ?? ''; ?>">
                                <label for="desde-hora" class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer pointer-events-none z-10">
                                    <i class="fas fa-clock text-gray-500" style="font-size: 18px;"></i>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Hasta Hora - Ocupa 3 columnas -->
                        <div class="md:col-span-3">
                            <label for="hasta-hora" class="block text-sm font-medium text-gray-700 mb-1">Hasta (Hora)</label>
                            <div class="relative">
                                <input type="time" id="hasta-hora" name="hasta_hora" required
                                       class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                       style="font-size: 16px; padding: 10px 45px 10px 10px; cursor: pointer;"
                                       value="<?php echo $_POST['hasta_hora'] ?? ''; ?>">
                                <label for="hasta-hora" class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer pointer-events-none z-10">
                                    <i class="fas fa-clock text-gray-500" style="font-size: 18px;"></i>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Espacio restante - 1 columna -->
                        <div class="md:col-span-1"></div>
                    </div>
                    
                    <div>
                        <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">Motivo</label>
                        <textarea id="motivo" name="motivo" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                  placeholder="Describa el motivo de la misión oficial..."><?php echo htmlspecialchars($_POST['motivo'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Autorizaciones -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Autorizaciones</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-medium text-gray-700 mb-4">Nombre del Funcionario</h3>
                            <div class="mb-4">
                                <input type="text" name="nombre_funcionario_firma"
                                       class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                                       placeholder="Nombre del funcionario">
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Fecha:</span>
                                <input type="date" name="fecha_solicitud" 
                                       class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                       value="<?php echo !empty($_POST['fecha_solicitud']) ? htmlspecialchars($_POST['fecha_solicitud']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-medium text-gray-700 mb-4">Nombre del Jefe Inmediato / Director</h3>
                            <div class="mb-4">
                                <input type="text" name="nombre_jefe_firma"
                                       class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                                       placeholder="Nombre del jefe inmediato / director">
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Fecha:</span>
                                <input type="date" name="fecha_revision" 
                                       class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                       value="<?php echo $_POST['fecha_revision'] ?? ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HR Section -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Para uso de la Oficina Institucional de Recursos Humanos</h2>
                    
                    <div class="mb-6">
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                  placeholder="Observaciones..."><?php echo htmlspecialchars($_POST['observaciones'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-medium text-gray-700 mb-2">Registrado por</h3>
                            <div class="mb-4">
                                <input type="text" name="revisado_por" 
                                       class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition" 
                                       placeholder="Nombre"
                                       value="<?php echo htmlspecialchars($_POST['revisado_por'] ?? ''); ?>">
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Fecha:</span>
                                <input type="date" name="fecha_revision" 
                                       class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-500 focus:border-red-500"
                                       value="<?php echo $_POST['fecha_revision'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h3 class="font-medium text-gray-700 mb-2">Revisado por</h3>
                            <div class="mb-4">
                                <input type="text" name="revisado_por_oirh"
                                       class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition" 
                                       placeholder="Nombre">
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">Fecha:</span>
                                <input type="date" 
                                       class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-500 focus:border-red-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="../dashboard.php" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i> Guardar Formulario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Script para funcionalidades adicionales
        document.addEventListener('DOMContentLoaded', function() {
            // Cálculo automático de duración de la misión
            const fechaMision = document.getElementById('fecha-mision');
            const desdeHora = document.getElementById('desde-hora');
            const hastaHora = document.getElementById('hasta-hora');
            
            // Establecer fecha mínima como hoy
            const today = new Date().toISOString().split('T')[0];
            fechaMision.min = today;
            
            // Mejorar campos de hora - forzar que muestren el selector
            function mejorarCamposHora() {
                [desdeHora, hastaHora].forEach(function(input) {
                    if (input) {
                        // Agregar evento click para mostrar el selector
                        input.addEventListener('click', function() {
                            // Intentar usar showPicker si está disponible (navegadores modernos)
                            if (this.showPicker) {
                                try {
                                    this.showPicker();
                                } catch (e) {
                                    // Si showPicker falla, el navegador mostrará el selector automáticamente
                                }
                            }
                        });
                        
                        // Agregar evento focus para mostrar el selector
                        input.addEventListener('focus', function() {
                            if (this.showPicker) {
                                try {
                                    this.showPicker();
                                } catch (e) {
                                    // Ignorar errores
                                }
                            }
                        });
                        
                        // Agregar placeholder visual
                        if (!input.value) {
                            input.style.color = '#9ca3af';
                        }
                        
                        input.addEventListener('change', function() {
                            this.style.color = '#000';
                        });
                    }
                });
            }
            
            // Mejorar campos de hora
            mejorarCamposHora();
            
            // Validar que la hora de fin sea posterior a la de inicio
            function validarHoras() {
                if (desdeHora.value && hastaHora.value) {
                    if (desdeHora.value >= hastaHora.value) {
                        hastaHora.setCustomValidity('La hora de fin debe ser posterior a la hora de inicio');
                    } else {
                        hastaHora.setCustomValidity('');
                    }
                }
            }
            
            desdeHora.addEventListener('change', validarHoras);
            hastaHora.addEventListener('change', validarHoras);
        });
    </script>
</body>
</html>

