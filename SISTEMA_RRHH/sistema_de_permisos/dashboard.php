<?php
/**
 * DASHBOARD - MENÚ PRINCIPAL
 * 
 * Pantalla principal después del login donde el funcionario
 * puede seleccionar qué formulario desea llenar
 */

require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/funciones.php';

// Requerir que el usuario esté logueado
requerirLogin();

// Obtener información del funcionario
$conn = conectarDB();
$funcionario = obtenerFuncionario($conn, getFuncionarioId());
cerrarDB($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema RRHH</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
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
        .user-info {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            height: 100%;
            border-left: 4px solid;
        }
        .form-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .form-card.jornada { border-left-color: #3b82f6; }
        .form-card.mision { border-left-color: #ef4444; }
        .form-card.reincorporacion { border-left-color: #8b5cf6; }
        .form-card.tiempo { border-left-color: #f97316; }
        .form-card.permiso { border-left-color: #10b981; }
        .form-card.vacaciones { border-left-color: #ec4899; }
        .form-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .form-card.jornada .form-icon { color: #3b82f6; }
        .form-card.mision .form-icon { color: #ef4444; }
        .form-card.reincorporacion .form-icon { color: #8b5cf6; }
        .form-card.tiempo .form-icon { color: #f97316; }
        .form-card.permiso .form-icon { color: #10b981; }
        .form-card.vacaciones .form-icon { color: #ec4899; }
        .btn-form {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            width: 100%;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                <i class="fas fa-briefcase me-2"></i>Sistema de Gestión RRHH
            </span>
            <div class="d-flex">
                <a href="mis_formularios.php" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-list me-1"></i>Mis Formularios
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
        <!-- Información del Usuario -->
        <div class="user-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">
                        <i class="fas fa-user-circle me-2 text-primary"></i>
                        <?php echo htmlspecialchars($funcionario['nombre_completo']); ?>
                    </h4>
                    <p class="text-muted mb-0">
                        <i class="fas fa-id-card me-1"></i>Cédula: <?php echo htmlspecialchars($funcionario['cedula']); ?> | 
                        <i class="fas fa-briefcase me-1"></i><?php echo htmlspecialchars($funcionario['cargo']); ?>
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-building me-1"></i><?php echo htmlspecialchars($funcionario['sede']); ?> | 
                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($funcionario['oficina_regional']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Título de Sección -->
        <h2 class="mb-4">
            <i class="fas fa-clipboard-list me-2"></i>Formularios Disponibles
        </h2>

        <!-- Grid de Formularios -->
        <div class="row">
            <!-- Formulario 1: Jornada Extraordinaria -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="form-card jornada">
                    <div class="text-center">
                        <i class="fas fa-clock form-icon"></i>
                        <h5>Jornada Extraordinaria</h5>
                        <p class="text-muted small">Autorización para laborar en jornada extraordinaria</p>
                        <a href="forms/jornada_extraordinaria.php" class="btn btn-primary btn-form">
                            <i class="fas fa-edit me-1"></i>Llenar Formulario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulario 2: Misión Oficial -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="form-card mision">
                    <div class="text-center">
                        <i class="fas fa-plane form-icon"></i>
                        <h5>Misión Oficial</h5>
                        <p class="text-muted small">Solicitud de misión oficial</p>
                        <a href="forms/mision_oficial.php" class="btn btn-danger btn-form">
                            <i class="fas fa-edit me-1"></i>Llenar Formulario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulario 3: Reincorporación -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="form-card reincorporacion">
                    <div class="text-center">
                        <i class="fas fa-undo form-icon"></i>
                        <h5>Reincorporación</h5>
                        <p class="text-muted small">Notificación de reincorporación</p>
                        <a href="forms/reincorporacion.php" class="btn btn-form" style="background: #8b5cf6; color: white;">
                            <i class="fas fa-edit me-1"></i>Llenar Formulario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulario 4: Tiempo Compensatorio -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="form-card tiempo">
                    <div class="text-center">
                        <i class="fas fa-hourglass-half form-icon"></i>
                        <h5>Tiempo Compensatorio</h5>
                        <p class="text-muted small">Solicitud de uso de tiempo compensatorio</p>
                        <a href="forms/tiempo_compensatorio.php" class="btn btn-form" style="background: #f97316; color: white;">
                            <i class="fas fa-edit me-1"></i>Llenar Formulario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulario 5: Permiso -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="form-card permiso">
                    <div class="text-center">
                        <i class="fas fa-calendar-check form-icon"></i>
                        <h5>Solicitud de Permiso</h5>
                        <p class="text-muted small">Solicitud de permiso personal</p>
                        <a href="forms/permiso.php" class="btn btn-success btn-form">
                            <i class="fas fa-edit me-1"></i>Llenar Formulario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulario 6: Vacaciones -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="form-card vacaciones">
                    <div class="text-center">
                        <i class="fas fa-umbrella-beach form-icon"></i>
                        <h5>Solicitud de Vacaciones</h5>
                        <p class="text-muted small">Solicitud de vacaciones</p>
                        <a href="forms/vacaciones.php" class="btn btn-form" style="background: #ec4899; color: white;">
                            <i class="fas fa-edit me-1"></i>Llenar Formulario
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Herramientas -->
        <h2 class="mb-4 mt-5">
            <i class="fas fa-tools me-2"></i>Herramientas
        </h2>

        <div class="row">
            <!-- Lector de Excel -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="form-card" style="border-left-color: #10b981;">
                    <div class="text-center">
                        <i class="fas fa-file-excel form-icon" style="color: #10b981;"></i>
                        <h5>Lector de Excel</h5>
                        <p class="text-muted small">Lee archivos Excel usando el microservicio Python</p>
                        <a href="../lectura_de_excel_php/index.php" class="btn btn-success btn-form">
                            <i class="fas fa-file-upload me-1"></i>Abrir Lector
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para manejar expiración de sesión -->
    <script>
        // Verificar cada 5 minutos si la sesión sigue activa
        setInterval(function() {
            fetch('check_session.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.activa) {
                        // Sesión expirada, redirigir al login
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => {
                    console.log('Error verificando sesión:', error);
                });
        }, 300000); // 5 minutos
    </script>
</body>
</html>


