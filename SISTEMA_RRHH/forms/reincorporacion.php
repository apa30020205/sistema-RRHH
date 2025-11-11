<?php
/**
 * FORMULARIO: REINCORPORACIÓN
 * 
 * Formulario completo integrado con backend PHP
 * Basado en: FORMULARIO DE REINCORPORACIÓN.html
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
    $mensaje = '¡Notificación de reincorporación guardada exitosamente!';
    // Limpiar $_POST para que no se muestren valores anteriores
    $_POST = array();
}

// Obtener información del funcionario para pre-llenar el formulario
$conn = conectarDB();
$funcionario = obtenerFuncionario($conn, $funcionario_id);

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motivo_ausencia = limpiarDatos($_POST['motivo'] ?? '');
    $puesto = limpiarDatos($_POST['puesto'] ?? '');
    $posicion_numero = limpiarDatos($_POST['posicion_numero'] ?? '');
    $unidad_administrativa = limpiarDatos($_POST['unidad_administrativa'] ?? '');
    $fecha_reincorporacion = limpiarDatos($_POST['fecha_reincorporacion'] ?? '');
    $fecha_firma_funcionario = limpiarDatos($_POST['fecha_firma_funcionario'] ?? '');
    $fecha_firma_jefe = limpiarDatos($_POST['fecha_firma_jefe'] ?? '');
    $jefe_oirh = limpiarDatos($_POST['jefe_oirh'] ?? '');
    $fecha_jefe_oirh = limpiarDatos($_POST['fecha_jefe_oirh'] ?? '');
    
    // Validaciones
    if (empty($motivo_ausencia)) {
        $error = 'Debe seleccionar el motivo de su ausencia';
    } elseif (empty($puesto)) {
        $error = 'Debe especificar el puesto al que se reincorpora';
    } elseif (empty($posicion_numero)) {
        $error = 'Debe especificar el número de posición';
    } elseif (empty($unidad_administrativa)) {
        $error = 'Debe especificar la unidad administrativa';
    } elseif (empty($fecha_reincorporacion)) {
        $error = 'Debe especificar la fecha de reincorporación';
    } else {
        // Guardar la reincorporación
        $stmt = $conn->prepare("INSERT INTO reincorporaciones 
            (funcionario_id, motivo_ausencia, puesto, posicion_numero, unidad_administrativa, 
             fecha_reincorporacion, fecha_firma_funcionario, fecha_firma_jefe, jefe_oirh, fecha_jefe_oirh, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");
        
        $fecha_firma_funcionario = !empty($fecha_firma_funcionario) ? $fecha_firma_funcionario : null;
        $fecha_firma_jefe = !empty($fecha_firma_jefe) ? $fecha_firma_jefe : null;
        $fecha_jefe_oirh = !empty($fecha_jefe_oirh) ? $fecha_jefe_oirh : null;
        
        $stmt->bind_param("isssssssss", 
            $funcionario_id, 
            $motivo_ausencia, 
            $puesto, 
            $posicion_numero, 
            $unidad_administrativa,
            $fecha_reincorporacion,
            $fecha_firma_funcionario,
            $fecha_firma_jefe,
            $jefe_oirh,
            $fecha_jefe_oirh
        );
        
        if ($stmt->execute()) {
            $mensaje = '¡Notificación de reincorporación guardada exitosamente!';
            // Redirigir para limpiar el formulario completamente
            header('Location: reincorporacion.php?guardado=1');
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
    <title>Reincorporación - Sistema RRHH</title>
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
            background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
        }
        .radio-container {
            transition: all 0.2s ease;
        }
        .radio-container:hover {
            background-color: #faf5ff;
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
                    <p class="text-lg mt-2">NOTIFICACIÓN DE REINCORPORACIÓN</p>
                </div>
                <div class="bg-white text-purple-800 px-4 py-2 rounded-lg text-center">
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
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>
                    <div>
                        <label for="cedula" class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                        <input type="text" id="cedula" name="cedula" 
                               value="<?php echo htmlspecialchars($funcionario['cedula'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>
                    <!-- Nivel 2 -->
                    <div>
                        <label for="cargo" class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                        <input type="text" id="cargo" name="cargo" 
                               value="<?php echo htmlspecialchars($funcionario['cargo'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>
                    <div>
                        <label for="posicion" class="block text-sm font-medium text-gray-700 mb-1">N° Posición</label>
                        <input type="text" id="posicion" name="posicion" 
                               value="<?php echo htmlspecialchars($funcionario['numero_posicion'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>
                    <!-- Nivel 3 (Sede más grande) -->
                    <div class="md:col-span-2">
                        <label for="sede" class="block text-sm font-medium text-gray-700 mb-1">Sede: Dirección/Departamento</label>
                        <input type="text" id="sede" name="sede" 
                               value="<?php echo htmlspecialchars($funcionario['sede'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>
                    <!-- Nivel 4 -->
                    <div>
                        <label for="region" class="block text-sm font-medium text-gray-700 mb-1">Oficina Regional: Provincia/Comarca</label>
                        <input type="text" id="region" name="region" 
                               value="<?php echo htmlspecialchars($funcionario['oficina_regional'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>
                    <div>
                        <label for="jefe" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Jefe Inmediato</label>
                        <input type="text" id="jefe" name="jefe" 
                               value="<?php echo htmlspecialchars($funcionario['nombre_jefe_inmediato'] ?? ''); ?>" 
                               readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                    </div>
                </div>
            </div>
            
            <!-- Motivo de ausencia -->
            <div class="mb-8">
                <p class="text-sm font-medium text-gray-700 mb-3">Estuve ausente por motivo de: <span class="text-red-500">*</span></p>
                <div class="space-y-2">
                    <div class="radio-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="licencia-sueldo" name="motivo" value="licencia con sueldo" 
                               class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'licencia con sueldo') ? 'checked' : ''; ?>>
                        <label for="licencia-sueldo" class="ml-3 block text-sm font-medium text-gray-700">Licencia con sueldo</label>
                    </div>
                    <div class="radio-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="licencia-sin-sueldo" name="motivo" value="licencia sin sueldo" 
                               class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'licencia sin sueldo') ? 'checked' : ''; ?>>
                        <label for="licencia-sin-sueldo" class="ml-3 block text-sm font-medium text-gray-700">Licencia sin sueldo</label>
                    </div>
                    <div class="radio-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="licencia-especial" name="motivo" value="licencia especial" 
                               class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'licencia especial') ? 'checked' : ''; ?>>
                        <label for="licencia-especial" class="ml-3 block text-sm font-medium text-gray-700">Licencia especial</label>
                    </div>
                    <div class="radio-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="vacaciones" name="motivo" value="vacaciones" 
                               class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'vacaciones') ? 'checked' : ''; ?>>
                        <label for="vacaciones" class="ml-3 block text-sm font-medium text-gray-700">Vacaciones</label>
                    </div>
                    <div class="radio-container flex items-center p-3 rounded-lg border border-gray-200">
                        <input type="radio" id="funciones-otra-institucion" name="motivo" value="prestando funciones en otra institución" 
                               class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300"
                               <?php echo (isset($_POST['motivo']) && $_POST['motivo'] === 'prestando funciones en otra institución') ? 'checked' : ''; ?>>
                        <label for="funciones-otra-institucion" class="ml-3 block text-sm font-medium text-gray-700">Prestando funciones en otra Institución</label>
                    </div>
                </div>
            </div>

            <!-- Reincorporation Details Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Detalles de Reincorporación</h2>
                
                <div class="space-y-6">
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <p class="text-sm text-gray-700 mb-4 text-center">Me estoy reincorporando formalmente al puesto de:</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="puesto" class="block text-sm font-medium text-gray-700 mb-1">Puesto</label>
                                <input type="text" id="puesto" name="puesto" required
                                       value="<?php echo htmlspecialchars($_POST['puesto'] ?? $funcionario['cargo'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            </div>
                            
                            <div>
                                <label for="posicion_numero" class="block text-sm font-medium text-gray-700 mb-1">Posición N°</label>
                                <input type="text" id="posicion_numero" name="posicion_numero" required
                                       value="<?php echo htmlspecialchars($_POST['posicion_numero'] ?? $funcionario['numero_posicion'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="unidad_administrativa" class="block text-sm font-medium text-gray-700 mb-1">Unidad Administrativa</label>
                            <input type="text" id="unidad_administrativa" name="unidad_administrativa" required
                                   value="<?php echo htmlspecialchars($_POST['unidad_administrativa'] ?? $funcionario['sede'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        </div>
                        
                        <div class="mt-4">
                            <label for="fecha-reincorporacion" class="block text-sm font-medium text-gray-700 mb-1">A partir del</label>
                            <input type="date" id="fecha-reincorporacion" name="fecha_reincorporacion" required
                                   value="<?php echo htmlspecialchars($_POST['fecha_reincorporacion'] ?? ''); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Autorizaciones</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Nombre del Funcionario</h3>
                        <div class="mb-3">
                            <input type="text" name="nombre_funcionario_firma"
                                   class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                                   placeholder="Nombre del Funcionario">
                        </div>
                        <span class="text-xs text-gray-500 block mb-4">Nombre del Funcionario</span>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_firma_funcionario" 
                                   value="<?php echo !empty($_POST['fecha_firma_funcionario']) ? htmlspecialchars($_POST['fecha_firma_funcionario']) : ''; ?>"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Nombre del Jefe Inmediato</h3>
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
                    
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Nombre del Jefe OIRH</h3>
                        <div class="mb-3">
                            <input type="text" id="jefe_oirh" name="jefe_oirh" 
                                   value="<?php echo htmlspecialchars($_POST['jefe_oirh'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"
                                   placeholder="Nombre del Jefe OIRH">
                        </div>
                        <span class="text-xs text-gray-500 block mb-4">Nombre del Jefe OIRH</span>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_jefe_oirh" 
                                   value="<?php echo htmlspecialchars($_POST['fecha_jefe_oirh'] ?? ''); ?>"
                                   class="px-3 py-1 border border-gray-300 rounded focus:ring-1 focus:ring-red-400 focus:border-red-400">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="../dashboard.php" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i> Guardar Notificación
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
            // Establecer fecha mínima como hoy para la fecha de reincorporación
            const fechaReincorporacion = document.getElementById('fecha-reincorporacion');
            const today = new Date().toISOString().split('T')[0];
            if (fechaReincorporacion) {
                fechaReincorporacion.min = today;
            }
            
            // Efecto visual para los radio buttons
            const motivos = document.querySelectorAll('input[name="motivo"]');
            motivos.forEach(motivo => {
                motivo.addEventListener('change', function() {
                    // Remover clase activa de todos
                    document.querySelectorAll('.radio-container').forEach(container => {
                        container.classList.remove('bg-purple-50', 'border-purple-300');
                    });
                    
                    // Agregar clase activa al seleccionado
                    if (this.checked) {
                        this.closest('.radio-container').classList.add('bg-purple-50', 'border-purple-300');
                    }
                });
                
                // Aplicar estilo si ya está seleccionado al cargar
                if (motivo.checked) {
                    motivo.closest('.radio-container').classList.add('bg-purple-50', 'border-purple-300');
                }
            });
        });
    </script>
</body>
</html>

