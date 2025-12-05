<?php
/**
 * PÁGINA DE INICIO DE SESIÓN
 * 
 * Permite que los funcionarios inicien sesión con su cédula y contraseña
 */

require_once 'config/database.php';
require_once 'includes/session.php';
require_once 'includes/funciones.php';

$error = '';

// Si el usuario ya está logueado, redirigir al dashboard
// PERO primero verificar que la sesión no haya expirado
if (estaLogueado()) {
    // Verificar tiempo de sesión (2 horas)
    if (isset($_SESSION['login_time'])) {
        $tiempo_transcurrido = time() - $_SESSION['login_time'];
        if ($tiempo_transcurrido > 7200) {
            // Sesión expirada, cerrar sesión
            cerrarSesion();
        } else {
            // Sesión válida, redirigir al dashboard
            header('Location: dashboard.php');
            exit();
        }
    } else {
        // No hay tiempo de login, redirigir al dashboard
        header('Location: dashboard.php');
        exit();
    }
}

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = limpiarDatos($_POST['cedula'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($cedula) || empty($password)) {
        $error = 'Por favor ingrese su cédula y contraseña';
    } else {
        // Conectar a la base de datos
        $conn = conectarDB();
        
        // Buscar el funcionario por cédula
        $stmt = $conn->prepare("SELECT id, cedula, nombre_completo, password FROM funcionarios WHERE cedula = ? AND activo = 1");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $funcionario = $result->fetch_assoc();
            
            // Verificar la contraseña
            if (password_verify($password, $funcionario['password'])) {
                // Iniciar sesión
                iniciarSesion($funcionario['id'], $funcionario['cedula']);
                
                // Redirigir al dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Cédula o contraseña incorrectos';
            }
        } else {
            $error = 'Cédula o contraseña incorrectos';
        }
        
        cerrarDB($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Sistema RRHH</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-body {
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        .input-icon input {
            padding-left: 45px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-users-cog fa-3x mb-3"></i>
            <h2>Sistema de Gestión</h2>
            <p class="mb-0">Recursos Humanos</p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula</label>
                    <div class="input-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" class="form-control" id="cedula" name="cedula" 
                               placeholder="Ingrese su cédula" required autofocus>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Ingrese su contraseña" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <p class="mb-0">¿No tienes cuenta? 
                    <a href="registro.php" class="text-decoration-none">Regístrate aquí</a>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>











