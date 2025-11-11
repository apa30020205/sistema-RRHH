<?php
/**
 * FORMULARIO: AUTORIZACIÓN PARA LABORAR EN JORNADA EXTRAORDINARIA
 */

require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/funciones.php';

// Requerir login
requerirLogin();

$mensaje = '';
$error = '';
$funcionario_id = getFuncionarioId();

$conn = conectarDB();
$funcionario = obtenerFuncionario($conn, $funcionario_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $justificacion = limpiarDatos($_POST['justificacion'] ?? '');
    $horarios = $_POST['horarios'] ?? [];

    if (empty($justificacion)) {
        $error = 'Debe proporcionar una justificación de la jornada extraordinaria';
    } elseif (empty($horarios) || !is_array($horarios)) {
        $error = 'Debe agregar al menos un horario de jornada extraordinaria';
    } else {
        $stmt = $conn->prepare("INSERT INTO jornadas_extraordinarias
            (funcionario_id, justificacion, fecha_solicitud, autorizado_por, superior_inmediato, director_area, jefe_rrhh)
            VALUES (?, ?, CURDATE(), ?, ?, ?, ?)");

        $autorizado_por = limpiarDatos($_POST['autorizado_por'] ?? '');
        $superior = limpiarDatos($_POST['superior'] ?? '');
        $director = limpiarDatos($_POST['director'] ?? '');
        $jefe_rrhh = limpiarDatos($_POST['jefe_rrhh'] ?? '');

        $stmt->bind_param('isssss', $funcionario_id, $justificacion, $autorizado_por, $superior, $director, $jefe_rrhh);

        if ($stmt->execute()) {
            $jornada_id = $stmt->insert_id;

            $stmt_horario = $conn->prepare("INSERT INTO jornadas_extraordinarias_horarios
                (jornada_id, fecha, desde_hora, hasta_hora, total_horas)
                VALUES (?, ?, ?, ?, ?)");

            foreach ($horarios as $index => $horario) {
                $fecha = limpiarDatos($horario['fecha'] ?? '');
                $desde = limpiarDatos($horario['desde'] ?? '');
                $hasta = limpiarDatos($horario['hasta'] ?? '');
                $total = floatval($horario['total'] ?? 0);

                if (!empty($fecha) && !empty($desde) && !empty($hasta)) {
                    $stmt_horario->bind_param('isssd', $jornada_id, $fecha, $desde, $hasta, $total);
                    $stmt_horario->execute();
                }
            }

            $mensaje = '¡Formulario guardado exitosamente!';
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
    <title>Jornada Extraordinaria - Sistema RRHH</title>
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
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .table-header {
            background-color: #eff6ff;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen pb-8">
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
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <i class="fas fa-check-circle mr-2"></i><?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <div class="header-section text-white rounded-t-xl p-6 mb-1">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold">OFICINA INSTITUCIONAL DE RECURSOS HUMANOS</h1>
                    <p class="text-lg mt-2">AUTORIZACIÓN PARA LABORAR EN JORNADA EXTRAORDINARIA</p>
                </div>
                <div class="bg-white text-blue-800 px-4 py-2 rounded-lg text-center">
                    <p class="text-sm font-semibold">FORMULARIO</p>
                    <p class="text-lg font-bold">2025</p>
                </div>
            </div>
        </div>

        <form method="POST" action="" class="form-container bg-white rounded-b-xl p-8">
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Información del Funcionario</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo del funcionario</label>
                        <input type="text" value="<?php echo htmlspecialchars($funcionario['nombre_completo'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cédula</label>
                        <input type="text" value="<?php echo htmlspecialchars($funcionario['cedula'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                        <input type="text" value="<?php echo htmlspecialchars($funcionario['cargo'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">N° Posición</label>
                        <input type="text" value="<?php echo htmlspecialchars($funcionario['numero_posicion'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sede: Dirección/Departamento</label>
                        <input type="text" value="<?php echo htmlspecialchars($funcionario['sede'] ?? ''); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-green-50" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Oficina Regional: Provincia/Comarca</label>
                        <input type="text" value="<?php echo htmlspecialchars($funcionario['oficina_regional'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Jefe Inmediato</label>
                        <input type="text" value="<?php echo htmlspecialchars($funcionario['nombre_jefe_inmediato'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-green-50" disabled>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Justificación de Jornada Extraordinaria</h2>
                <p class="text-sm text-gray-600 mb-2">El tiempo extraordinario que se autoriza es para realizar las siguientes tareas:</p>
                <textarea name="justificacion" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                          placeholder="Describa las tareas a realizar..."></textarea>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Horario Extraordinario</h2>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="table-header">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Desde (Hora)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Hasta (Hora)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Total de Horas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider w-20">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="horarios-body">
                            <tr>
                                <td class="px-4 py-3">
                                    <input type="date" name="horarios[0][fecha]" required
                                           class="w-full px-2 py-1 border border-gray-300 rounded">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="time" name="horarios[0][desde]" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="time" name="horarios[0][hasta]" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="horarios[0][total]" step="0.01" readonly
                                           class="w-24 px-2 py-1 border border-gray-300 rounded bg-gray-100 text-center"
                                           placeholder="0.00">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" class="text-red-500 hover:text-red-700 remove-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
                    <button type="button" id="add-row" class="flex items-center text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-plus-circle mr-2"></i> Agregar otra fecha
                    </button>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Autorizaciones</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Nombre del funcionario</h3>
                        <input type="text" name="autorizado_por"
                               class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition mb-4"
                               placeholder="Nombre del funcionario">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_autorizacion"
                                   class="px-3 py-1 border border-gray-300 rounded">
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Nombre del Jefe Inmediato</h3>
                        <input type="text" name="superior"
                               class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition mb-4"
                               placeholder="Nombre del Jefe Inmediato">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_superior"
                                   class="px-3 py-1 border border-gray-300 rounded">
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-1">Para uso de la Oficina Institucional de Recursos Humanos</h3>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">V° B° Director del Área:</h3>
                        <input type="text" name="director"
                               class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition mb-4"
                               placeholder="V° B° Director del Área">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_director"
                                   class="px-3 py-1 border border-gray-300 rounded">
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-700 mb-2">Jefe Institucional de Recursos Humanos</h3>
                        <input type="text" name="jefe_rrhh"
                               class="w-full px-4 py-3 border border-red-200 rounded-lg bg-red-50 focus:ring-2 focus:ring-red-400 focus:border-red-400 transition mb-4"
                               placeholder="Jefe Institucional de Recursos Humanos">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Fecha:</span>
                            <input type="date" name="fecha_jefe_rrhh"
                                   class="px-3 py-1 border border-gray-300 rounded">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="../dashboard.php" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center justify-center">
                    <i class="fas fa-times mr-2"></i> Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                    <i class="fas fa-save mr-2"></i> Guardar Formulario
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addButton = document.getElementById('add-row');
            const tbody = document.getElementById('horarios-body');

            function calcularHoras(row) {
                if (!row) return;
                const desdeInput = row.querySelector('td:nth-child(2) input[type="time"]');
                const hastaInput = row.querySelector('td:nth-child(3) input[type="time"]');
                const totalInput = row.querySelector('td:nth-child(4) input[type="number"]');

                if (desdeInput && hastaInput && totalInput) {
                    if (desdeInput.value && hastaInput.value) {
                        const desde = new Date('2000-01-01T' + desdeInput.value);
                        const hasta = new Date('2000-01-01T' + hastaInput.value);

                        if (hasta < desde) {
                            hasta.setDate(hasta.getDate() + 1);
                        }

                        const diffMs = hasta - desde;
                        const diffHrs = diffMs / (1000 * 60 * 60);
                        totalInput.value = diffHrs.toFixed(2);
                    } else {
                        totalInput.value = '';
                    }
                }
            }

            function configurarFila(row) {
                const timeInputs = row.querySelectorAll('input[type="time"]');
                timeInputs.forEach(function (input) {
                    input.addEventListener('change', function () {
                        calcularHoras(row);
                    });
                    input.addEventListener('input', function () {
                        calcularHoras(row);
                    });
                });
            }

            if (tbody) {
                tbody.querySelectorAll('tr').forEach(configurarFila);
                tbody.querySelectorAll('tr').forEach(calcularHoras);

                tbody.addEventListener('click', function (e) {
                    const removeButton = e.target.closest('.remove-row');
                    if (removeButton) {
                        const row = removeButton.closest('tr');
                        if (tbody.children.length > 1) {
                            row.remove();
                        } else {
                            alert('Debe haber al menos una fila de horario');
                        }
                    }
                });
            }

            if (addButton && tbody) {
                addButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    const index = tbody.children.length;
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td class="px-4 py-3">
                            <input type="date" name="horarios[${index}][fecha]" required
                                   class="w-full px-2 py-1 border border-gray-300 rounded">
                        </td>
                        <td class="px-4 py-3">
                            <input type="time" name="horarios[${index}][desde]" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </td>
                        <td class="px-4 py-3">
                            <input type="time" name="horarios[${index}][hasta]" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="horarios[${index}][total]" step="0.01" readonly
                                   class="w-24 px-2 py-1 border border-gray-300 rounded bg-gray-100 text-center"
                                   placeholder="0.00">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" class="text-red-500 hover:text-red-700 remove-row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>`;
                    tbody.appendChild(newRow);
                    configurarFila(newRow);
                });
            }
        });
    </script>
</body>
</html>
