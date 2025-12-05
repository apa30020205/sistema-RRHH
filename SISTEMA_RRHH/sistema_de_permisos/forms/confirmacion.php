<?php
/**
 * PÁGINA DE CONFIRMACIÓN DE SOLICITUD
 * 
 * Muestra confirmación después de enviar una solicitud
 */

require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/funciones.php';

// Requerir login
requerirLogin();

// Obtener parámetros
$tipo = $_GET['tipo'] ?? 'vacaciones';
$solicitud_id = $_GET['id'] ?? 0;
$funcionario_id = getFuncionarioId();

$conn = conectarDB();
$funcionario = obtenerFuncionario($conn, $funcionario_id);

// Nombres de los tipos de formulario
$tipos_nombre = [
    'vacaciones' => 'Solicitud de Vacaciones',
    'permiso' => 'Solicitud de Permiso',
    'mision_oficial' => 'Misión Oficial',
    'jornada_extraordinaria' => 'Jornada Extraordinaria',
    'tiempo_compensatorio' => 'Tiempo Compensatorio',
    'reincorporacion' => 'Reincorporación'
];

$nombre_tipo = $tipos_nombre[$tipo] ?? 'Solicitud';

// Obtener fecha actual
$fecha_actual = date('d/m/Y H:i');

cerrarDB($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud Recibida - Sistema RRHH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-t-xl p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">Sistema de Recursos Humanos</h1>
                    <p class="text-blue-100 mt-1"><?php echo htmlspecialchars($nombre_tipo); ?></p>
                </div>
                <div class="bg-white text-blue-800 px-4 py-2 rounded-lg text-center">
                    <p class="text-sm font-semibold">CONFIRMACIÓN</p>
                </div>
            </div>
        </div>

        <!-- Card de Confirmación -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-6">
            <!-- Icono de éxito -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-4">
                    <i class="fas fa-check-circle text-5xl text-green-600"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Solicitud Recibida</h2>
                <p class="text-gray-600">Su solicitud ha sido enviada exitosamente</p>
            </div>

            <!-- Información de la Solicitud -->
            <div class="border-t border-b border-gray-200 py-6 mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Estado de la Solicitud</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                            <span class="text-gray-700 font-medium">Acción:</span>
                        </div>
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg font-semibold">
                            Pendiente de aprobación
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div class="flex items-center">
                            <i class="fas fa-user text-blue-600 mr-3"></i>
                            <span class="text-gray-700 font-medium">Aprobado por:</span>
                        </div>
                        <span class="text-gray-800 font-semibold">
                            <?php echo htmlspecialchars($funcionario['nombre_completo'] ?? 'N/A'); ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center">
                            <i class="fas fa-calendar text-blue-600 mr-3"></i>
                            <span class="text-gray-700 font-medium">Fecha:</span>
                        </div>
                        <span class="text-gray-800 font-semibold">
                            <?php echo htmlspecialchars($fecha_actual); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Mensaje informativo -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-6">
                <div class="flex items-start">
                    <i class="fas fa-envelope text-blue-600 mr-3 mt-1"></i>
                    <div>
                        <p class="text-blue-800 font-medium mb-1">Notificación enviada</p>
                        <p class="text-blue-700 text-sm">
                            Se ha enviado un email a su jefe inmediato para iniciar el proceso de aprobación. 
                            Recibirá notificaciones en cada etapa del proceso.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botón para volver al Dashboard -->
            <div class="text-center">
                <a href="../dashboard.php" 
                   class="inline-flex items-center px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-md hover:shadow-lg">
                    <i class="fas fa-home mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center text-gray-500 text-sm">
            <p>Sistema de Recursos Humanos © 2025</p>
        </div>
    </div>

    <!-- Redirección automática después de 10 segundos -->
    <script>
        setTimeout(function() {
            window.location.href = '../dashboard.php';
        }, 10000); // 10 segundos
    </script>
</body>
</html>


