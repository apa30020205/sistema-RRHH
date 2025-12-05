<?php
/**
 * MIS FORMULARIOS
 * 
 * Página para ver todos los formularios enviados por el funcionario
 */

require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/funciones.php';

// Requerir que el usuario esté logueado
requerirLogin();

$conn = conectarDB();
$funcionario_id = getFuncionarioId();
$funcionario = obtenerFuncionario($conn, $funcionario_id);

// Obtener todos los formularios de jornada extraordinaria del usuario
$stmt = $conn->prepare("
    SELECT je.*, 
           COUNT(jeh.id) as total_horarios
    FROM jornadas_extraordinarias je
    LEFT JOIN jornadas_extraordinarias_horarios jeh ON je.id = jeh.jornada_id
    WHERE je.funcionario_id = ?
    GROUP BY je.id
    ORDER BY je.fecha_creacion DESC
");
$stmt->bind_param("i", $funcionario_id);
$stmt->execute();
$jornadas = $stmt->get_result();

cerrarDB($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Formularios - Sistema RRHH</title>
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
        .card-header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 15px 20px;
        }
        .badge-estado {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-pendiente {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-aprobado {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-rechazado {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-list me-2"></i>Mis Formularios
            </span>
            <div class="d-flex">
                <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Dashboard
                </a>
                <span class="navbar-text text-white me-3">
                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($funcionario['nombre_completo']); ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Salir
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="fas fa-clock me-2 text-primary"></i>Jornadas Extraordinarias
        </h2>

        <?php if ($jornadas->num_rows === 0): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No has enviado ningún formulario de jornada extraordinaria aún</h5>
                    <a href="forms/jornada_extraordinaria.php" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i>Crear mi primer formulario
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php while ($jornada = $jornadas->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar me-2"></i>
                                    Formulario del <?php echo date('d/m/Y', strtotime($jornada['fecha_solicitud'])); ?>
                                </h5>
                                <small class="opacity-75">
                                    Enviado: <?php echo date('d/m/Y H:i', strtotime($jornada['fecha_creacion'])); ?>
                                </small>
                            </div>
                            <?php
                            $estado_class = 'pendiente';
                            if ($jornada['estado'] === 'aprobado') $estado_class = 'aprobado';
                            if ($jornada['estado'] === 'rechazado') $estado_class = 'rechazado';
                            ?>
                            <span class="badge-estado badge-<?php echo $estado_class; ?>">
                                <?php echo ucfirst($jornada['estado']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <strong><i class="fas fa-tasks me-2 text-primary"></i>Justificación:</strong>
                                <p class="mt-2"><?php echo nl2br(htmlspecialchars($jornada['justificacion'])); ?></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-calendar-alt me-2 text-primary"></i>Total de horarios:</strong>
                                <span class="badge bg-primary"><?php echo $jornada['total_horarios']; ?> fechas</span>
                            </div>
                            <?php if ($jornada['autorizado_por']): ?>
                            <div class="col-md-6 mb-3">
                                <strong><i class="fas fa-check-circle me-2 text-success"></i>Autorizado por:</strong>
                                <?php echo htmlspecialchars($jornada['autorizado_por']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="mt-3">
                            <a href="ver_jornada.php?id=<?php echo $jornada['id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>Ver detalles completos
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>










