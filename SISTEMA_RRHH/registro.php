<?php
/**
 * PÁGINA DE REGISTRO DE FUNCIONARIOS
 * 
 * Permite que un funcionario se registre en el sistema
 * creando su cuenta con usuario y contraseña.
 */

require_once 'config/database.php';
require_once 'includes/funciones.php';

$mensaje = '';
$error = '';

// Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener y limpiar datos
    $cedula = limpiarDatos($_POST['cedula'] ?? '');
    $nombre_completo = limpiarDatos($_POST['nombre_completo'] ?? '');
    $cargo = limpiarDatos($_POST['cargo'] ?? '');
    $numero_posicion = limpiarDatos($_POST['numero_posicion'] ?? '');
    $sede = limpiarDatos($_POST['sede'] ?? '');
    $oficina_regional = limpiarDatos($_POST['oficina_regional'] ?? '');
    $nombre_jefe_inmediato = limpiarDatos($_POST['nombre_jefe_inmediato'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validaciones
    if (empty($cedula) || empty($nombre_completo) || empty($cargo) || 
        empty($numero_posicion) || empty($sede) || empty($oficina_regional) || 
        empty($nombre_jefe_inmediato) || empty($password)) {
        $error = 'Todos los campos son obligatorios';
    } elseif (strlen($password) < 4 || strlen($password) > 12) {
        $error = 'La contraseña debe tener entre 4 y 12 caracteres';
    } elseif ($password !== $password_confirm) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Conectar a la base de datos
        $conn = conectarDB();
        
        // Verificar si la cédula ya existe
        $stmt = $conn->prepare("SELECT id FROM funcionarios WHERE cedula = ?");
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Esta cédula ya está registrada en el sistema';
        } else {
            // Encriptar contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar nuevo funcionario
            $stmt = $conn->prepare("INSERT INTO funcionarios 
                (cedula, nombre_completo, cargo, numero_posicion, sede, oficina_regional, nombre_jefe_inmediato, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $cedula, $nombre_completo, $cargo, $numero_posicion, 
                            $sede, $oficina_regional, $nombre_jefe_inmediato, $password_hash);
            
            if ($stmt->execute()) {
                $mensaje = 'Registro exitoso! Ahora puedes iniciar sesión.';
                // Limpiar campos después del registro exitoso
                $cedula = $nombre_completo = $cargo = $numero_posicion = $sede = $oficina_regional = $nombre_jefe_inmediato = '';
            } else {
                $error = 'Error al registrar: ' . $conn->error;
            }
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
    <title>Registro de Funcionario - Sistema RRHH</title>
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
        .registro-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        .registro-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .registro-body {
            padding: 40px;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-registro {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn-registro:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="registro-card">
        <div class="registro-header">
            <h2><i class="fas fa-user-plus me-2"></i>Registro de Funcionario</h2>
            <p class="mb-0">Sistema de Gestión de Recursos Humanos</p>
        </div>
        
        <div class="registro-body">
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="formRegistro">
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label for="nombre_completo" class="form-label">
                            <i class="fas fa-user me-1"></i> Nombre completo del funcionario
                        </label>
                        <input type="text" class="form-control" id="nombre_completo" name="nombre_completo" 
                               value="<?php echo htmlspecialchars($nombre_completo ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="cedula" class="form-label">
                            <i class="fas fa-id-card me-1"></i> Cédula
                        </label>
                        <input type="text" class="form-control" id="cedula" name="cedula" 
                               value="<?php echo htmlspecialchars($cedula ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label for="cargo" class="form-label">
                            <i class="fas fa-briefcase me-1"></i> Cargo
                        </label>
                        <input type="text" class="form-control" id="cargo" name="cargo" 
                               value="<?php echo htmlspecialchars($cargo ?? ''); ?>" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="numero_posicion" class="form-label">
                            <i class="fas fa-hashtag me-1"></i> N° Posición
                        </label>
                        <input type="text" class="form-control" id="numero_posicion" name="numero_posicion" 
                               value="<?php echo htmlspecialchars($numero_posicion ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="sede" class="form-label">
                        <i class="fas fa-building me-1"></i> Sede: Dirección/Departamento
                    </label>
                    <select class="form-select" id="sede" name="sede" required>
                        <option value="">Seleccione una sede...</option>
                        <option value="Dirección de Administración: Departamento de Recursos Humanos">Dirección de Administración: Departamento de Recursos Humanos</option>
                        <option value="Dirección de Administración: Departamento de Compras y Contratación">Dirección de Administración: Departamento de Compras y Contratación</option>
                        <option value="Dirección de Administración: Departamento de Servicios Generales">Dirección de Administración: Departamento de Servicios Generales</option>
                        <option value="Dirección de Finanzas: Departamento de Presupuesto">Dirección de Finanzas: Departamento de Presupuesto</option>
                        <option value="Dirección de Finanzas: Departamento de Contabilidad">Dirección de Finanzas: Departamento de Contabilidad</option>
                        <option value="Dirección de Finanzas: Departamento de Tesorería">Dirección de Finanzas: Departamento de Tesorería</option>
                        <option value="Dirección de Servicios Financieros: Departamento de Capital Semilla">Dirección de Servicios Financieros: Departamento de Capital Semilla</option>
                        <option value="Dirección de Servicios Financieros: Departamento de Microcrédito">Dirección de Servicios Financieros: Departamento de Microcrédito</option>
                        <option value="Dirección de Desarrollo Empresarial: Departamento de Fomento Empresarial">Dirección de Desarrollo Empresarial: Departamento de Fomento Empresarial</option>
                        <option value="Dirección de Desarrollo Empresarial: Departamento de Capacitación y Asistencia Técnica">Dirección de Desarrollo Empresarial: Departamento de Capacitación y Asistencia Técnica</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="oficina_regional" class="form-label">
                        <i class="fas fa-map-marker-alt me-1"></i> Oficina Regional: Provincia/Comarca
                    </label>
                    <select class="form-select" id="oficina_regional" name="oficina_regional" required>
                        <option value="">Seleccione una regional...</option>
                        <option value="Regional de San Miguelito">Regional de San Miguelito</option>
                        <option value="Regional de Panamá Este">Regional de Panamá Este</option>
                        <option value="Regional de Panamá Oeste">Regional de Panamá Oeste</option>
                        <option value="Regional de Bocas del Toro">Regional de Bocas del Toro</option>
                        <option value="Regional de Coclé">Regional de Coclé</option>
                        <option value="Regional de Colón">Regional de Colón</option>
                        <option value="Regional de Barú-Chiriquí">Regional de Barú-Chiriquí</option>
                        <option value="Regional de David-Chiriquí">Regional de David-Chiriquí</option>
                        <option value="Regional de Darién">Regional de Darién</option>
                        <option value="Regional de Herrera">Regional de Herrera</option>
                        <option value="Regional de Los Santos">Regional de Los Santos</option>
                        <option value="Regional de Veraguas">Regional de Veraguas</option>
                        <option value="Cinta Costera (Ciudad de Panamá)">Cinta Costera (Ciudad de Panamá)</option>
                        <option value="Centro de Arte y Cultura (Colón)">Centro de Arte y Cultura (Colón)</option>
                        <option value="Guna Yala">Guna Yala</option>
                        <option value="Emberá-Wounaan">Emberá-Wounaan</option>
                        <option value="Ngäbe-Buglé">Ngäbe-Buglé</option>
                        <option value="Naso Tjër Di">Naso Tjër Di</option>
                        <option value="Madugandí">Madugandí</option>
                        <option value="Wargandí">Wargandí</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="nombre_jefe_inmediato" class="form-label">
                        <i class="fas fa-user-tie me-1"></i> Nombre del Jefe Inmediato
                    </label>
                    <input type="text" class="form-control" id="nombre_jefe_inmediato" name="nombre_jefe_inmediato" 
                           value="<?php echo htmlspecialchars($nombre_jefe_inmediato ?? ''); ?>" required>
                </div>
                
                <hr class="my-4">
                
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i> Contraseña
                        </label>
                        <input type="password" class="form-control" id="password" name="password" 
                               minlength="4" maxlength="12" required>
                        <div class="password-requirements">
                            Mínimo 4 caracteres, máximo 12 caracteres
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="password_confirm" class="form-label">
                            <i class="fas fa-lock me-1"></i> Confirmar Contraseña
                        </label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                               minlength="4" maxlength="12" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-registro">
                        <i class="fas fa-user-plus me-2"></i>Registrarse
                    </button>
                </div>
                
                <div class="text-center mt-4">
                    <p class="mb-0">¿Ya tienes cuenta? 
                        <a href="index.php" class="text-decoration-none">Inicia sesión aquí</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validar que las contraseñas coincidan
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
        });
    </script>
</body>
</html>












