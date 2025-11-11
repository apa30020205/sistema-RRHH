<?php
/**
 * VER JORNADA EXTRAORDINARIA
 * 
 * Página para ver los detalles completos de una jornada extraordinaria
 */

require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/funciones.php';

// Requerir que el usuario esté logueado
requerirLogin();

$conn = conectarDB();
$funcionario_id = getFuncionarioId();

// Obtener el ID de la jornada desde la URL
$jornada_id = intval($_GET['id'] ?? 0);

// Obtener la jornada (solo si pertenece al usuario logueado)
$stmt = $conn->prepare("
    SELECT je.*, f.nombre_completo, f.cedula, f.cargo, f.numero_posicion, 
           f.sede, f.oficina_regional, f.nombre_jefe_inmediato
    FROM jornadas_extraordinarias je
    INNER JOIN funcionarios f ON je.funcionario_id = f.id
    WHERE je.id = ? AND je.funcionario_id = ?
");
$stmt->bind_param("ii", $jornada_id, $funcionario_id);
$stmt->execute();
$result = $stmt->get_result();
$jornada = $result->fetch_assoc();

if (!$jornada) {
    header('Location: mis_formularios.php');
    exit;
}

// Obtener los horarios de esta jornada
$stmt_horarios = $conn->prepare("
    SELECT * FROM jornadas_extraordinarias_horarios 
    WHERE jornada_id = ? 
    ORDER BY fecha ASC, desde_hora ASC
");
$stmt_horarios->bind_param("i", $jornada_id);
$stmt_horarios->execute();
$horarios = $stmt_horarios->get_result();

cerrarDB($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles Jornada Extraordinaria - Sistema RRHH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background: #f5f7fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: none;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .table-custom {
            background: #eff6ff;
        }
        .bg-green-50 {
            background-color: #f0fdf4;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-clock me-2"></i>Detalles de Jornada Extraordinaria
            </span>
            <div class="d-flex">
                <a href="mis_formularios.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Volver
                </a>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-4">
        <!-- Información del Funcionario -->
        <div class="card">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información del Funcionario</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Nombre completo del funcionario</label>
                        <input type="text" value="<?php echo htmlspecialchars($jornada['nombre_completo']); ?>" 
                               class="form-control bg-green-50" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Cédula</label>
                        <input type="text" value="<?php echo htmlspecialchars($jornada['cedula']); ?>" 
                               class="form-control bg-green-50" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Cargo</label>
                        <input type="text" value="<?php echo htmlspecialchars($jornada['cargo']); ?>" 
                               class="form-control bg-green-50" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">N° Posición</label>
                        <input type="text" value="<?php echo htmlspecialchars($jornada['numero_posicion']); ?>" 
                               class="form-control bg-green-50" readonly>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small">Sede: Dirección/Departamento</label>
                        <input type="text" value="<?php echo htmlspecialchars($jornada['sede']); ?>" 
                               class="form-control bg-green-50" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Oficina Regional: Provincia/Comarca</label>
                        <input type="text" value="<?php echo htmlspecialchars($jornada['oficina_regional']); ?>" 
                               class="form-control bg-green-50" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Nombre del Jefe Inmediato</label>
                        <input type="text" value="<?php echo htmlspecialchars($jornada['nombre_jefe_inmediato']); ?>" 
                               class="form-control bg-green-50" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Justificación -->
        <div class="card">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Justificación</h5>
            </div>
            <div class="card-body">
                <p><?php echo nl2br(htmlspecialchars($jornada['justificacion'])); ?></p>
            </div>
        </div>

        <!-- Horarios -->
        <div class="card">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Horario Extraordinario</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-custom">
                            <tr>
                                <th>Fecha</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Total Horas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total_horas = 0;
                            while ($horario = $horarios->fetch_assoc()): 
                                $total_horas += floatval($horario['total_horas']);
                            ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($horario['fecha'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($horario['desde_hora'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($horario['hasta_hora'])); ?></td>
                                    <td><strong><?php echo number_format($horario['total_horas'], 2); ?> horas</strong></td>
                                </tr>
                            <?php endwhile; ?>
                            <tr class="table-info">
                                <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                                <td><strong><?php echo number_format($total_horas, 2); ?> horas</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Autorizaciones -->
        <?php if ($jornada['autorizado_por'] || $jornada['superior_inmediato'] || $jornada['director_area'] || $jornada['jefe_rrhh']): ?>
        <div class="card">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Autorizaciones</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if ($jornada['autorizado_por']): ?>
                    <div class="col-md-6 mb-3">
                        <strong>Autorizado por:</strong>
                        <p><?php echo htmlspecialchars($jornada['autorizado_por']); ?></p>
                        <?php if ($jornada['fecha_autorizacion']): ?>
                        <small class="text-muted">Fecha: <?php echo date('d/m/Y', strtotime($jornada['fecha_autorizacion'])); ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($jornada['superior_inmediato']): ?>
                    <div class="col-md-6 mb-3">
                        <strong>Superior Inmediato:</strong>
                        <p><?php echo htmlspecialchars($jornada['superior_inmediato']); ?></p>
                        <?php if ($jornada['fecha_superior']): ?>
                        <small class="text-muted">Fecha: <?php echo date('d/m/Y', strtotime($jornada['fecha_superior'])); ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($jornada['director_area']): ?>
                    <div class="col-md-6 mb-3">
                        <strong>V° B° Director del Área:</strong>
                        <p><?php echo htmlspecialchars($jornada['director_area']); ?></p>
                        <?php if ($jornada['fecha_director']): ?>
                        <small class="text-muted">Fecha: <?php echo date('d/m/Y', strtotime($jornada['fecha_director'])); ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($jornada['jefe_rrhh']): ?>
                    <div class="col-md-6 mb-3">
                        <strong>Enterado: Jefe Institucional de Recursos Humanos</strong>
                        <p><?php echo htmlspecialchars($jornada['jefe_rrhh']); ?></p>
                        <?php if ($jornada['fecha_jefe_rrhh']): ?>
                        <small class="text-muted">Fecha: <?php echo date('d/m/Y', strtotime($jornada['fecha_jefe_rrhh'])); ?></small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Botones -->
        <div class="text-center mt-4">
            <a href="mis_formularios.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Volver a Mis Formularios
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-1"></i>Imprimir
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>










