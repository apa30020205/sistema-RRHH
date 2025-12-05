<?php
/**
 * Script para verificar y actualizar la contraseña del admin
 * Ejecutar desde: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/verificar_password_admin.php
 * 
 * Script independiente - no requiere archivos de configuración externos
 */

// Configuración de base de datos (ajusta si es necesario)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Laragon por defecto no tiene contraseña
define('DB_NAME', 'rrhh');
define('DB_CHARSET', 'utf8mb4');

// Función para obtener conexión
function getDBConnection() {
    static $conn = null;
    
    if ($conn === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $conn = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch(PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
    
    return $conn;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Contraseña Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .info {
            background: #e7f3ff;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            padding: 15px;
            border-left: 4px solid #28a745;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            padding: 15px;
            border-left: 4px solid #dc3545;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Verificar y Actualizar Contraseña Admin</h1>
        
        <?php
        try {
            $db = getDBConnection();
            
            // Paso 1: Verificar usuario admin
            echo '<div class="info">';
            echo '<h2>Paso 1: Verificar Usuario Admin</h2>';
            $stmt = $db->prepare("
                SELECT id_usuario, username, LEFT(password_hash, 50) as hash_preview, 
                       activo, rol, nombre_completo
                FROM usuarios 
                WHERE username = 'admin'
            ");
            $stmt->execute();
            $admin = $stmt->fetch();
            
            if ($admin) {
                echo '<div class="success">';
                echo '<strong>✓ Usuario admin encontrado:</strong><br>';
                echo '<table>';
                echo '<tr><th>Campo</th><th>Valor</th></tr>';
                echo '<tr><td>ID</td><td>' . htmlspecialchars($admin['id_usuario']) . '</td></tr>';
                echo '<tr><td>Username</td><td>' . htmlspecialchars($admin['username']) . '</td></tr>';
                echo '<tr><td>Hash (primeros 50 chars)</td><td><code>' . htmlspecialchars($admin['hash_preview']) . '...</code></td></tr>';
                echo '<tr><td>Activo</td><td>' . ($admin['activo'] ? '✓ Sí' : '✗ No') . '</td></tr>';
                echo '<tr><td>Rol</td><td>' . htmlspecialchars($admin['rol']) . '</td></tr>';
                echo '<tr><td>Nombre Completo</td><td>' . htmlspecialchars($admin['nombre_completo'] ?? 'N/A') . '</td></tr>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<div class="error">';
                echo '<strong>✗ Usuario admin NO encontrado</strong>';
                echo '</div>';
                exit;
            }
            echo '</div>';
            
            // Paso 2: Obtener hash completo
            echo '<div class="info">';
            echo '<h2>Paso 2: Hash Completo de la Contraseña</h2>';
            $stmt2 = $db->prepare("SELECT password_hash FROM usuarios WHERE id_usuario = ?");
            $stmt2->execute([$admin['id_usuario']]);
            $hashData = $stmt2->fetch();
            $hashActual = $hashData['password_hash'];
            
            echo '<div class="warning">';
            echo '<strong>Hash completo actual:</strong><br>';
            echo '<code style="word-break: break-all;">' . htmlspecialchars($hashActual) . '</code>';
            echo '</div>';
            echo '</div>';
            
            // Paso 3: Probar contraseña
            echo '<div class="info">';
            echo '<h2>Paso 3: Probar Contraseña "admin123"</h2>';
            $passwordTest = 'admin123';
            $verificacion = password_verify($passwordTest, $hashActual);
            
            if ($verificacion) {
                echo '<div class="success">';
                echo '<strong>✓ La contraseña "admin123" es CORRECTA</strong><br>';
                echo 'El hash en la base de datos coincide con la contraseña.';
                echo '</div>';
            } else {
                echo '<div class="error">';
                echo '<strong>✗ La contraseña "admin123" NO coincide</strong><br>';
                echo 'Necesitamos actualizar el hash en la base de datos.';
                echo '</div>';
                
                // Paso 4: Actualizar contraseña
                echo '<div class="info">';
                echo '<h2>Paso 4: Actualizar Contraseña</h2>';
                
                // Generar nuevo hash
                $nuevoHash = password_hash($passwordTest, PASSWORD_BCRYPT);
                
                $stmtUpdate = $db->prepare("
                    UPDATE usuarios 
                    SET password_hash = ?, 
                        activo = 1,
                        fecha_actualizacion = NOW()
                    WHERE id_usuario = ?
                ");
                $resultado = $stmtUpdate->execute([$nuevoHash, $admin['id_usuario']]);
                
                if ($resultado) {
                    echo '<div class="success">';
                    echo '<strong>✓ Contraseña actualizada exitosamente</strong><br>';
                    echo 'Filas afectadas: ' . $stmtUpdate->rowCount() . '<br>';
                    echo '<br>';
                    echo '<strong>Nuevo hash generado:</strong><br>';
                    echo '<code style="word-break: break-all;">' . htmlspecialchars($nuevoHash) . '</code>';
                    echo '</div>';
                    
                    // Verificar nuevamente
                    $verificacionFinal = password_verify($passwordTest, $nuevoHash);
                    if ($verificacionFinal) {
                        echo '<div class="success">';
                        echo '<strong>✓ Verificación final: La contraseña funciona correctamente</strong>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="error">';
                    echo '<strong>✗ Error al actualizar la contraseña</strong>';
                    echo '</div>';
                }
                echo '</div>';
            }
            
            // Paso 5: Instrucciones
            echo '<div class="info">';
            echo '<h2>Paso 5: Instrucciones para Login</h2>';
            echo '<p><strong>Credenciales:</strong></p>';
            echo '<ul>';
            echo '<li>Usuario: <code>admin</code></li>';
            echo '<li>Contraseña: <code>admin123</code></li>';
            echo '</ul>';
            echo '<p><strong>URL de Login:</strong></p>';
            echo '<p><a href="/RECURSOS%20HUMANOS/SISTEMA_RRHH/roles_rrhh/pages/login.php" class="btn">Ir al Login</a></p>';
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
            echo '<br><br>';
            echo '<strong>Detalles técnicos:</strong><br>';
            echo '<code>' . htmlspecialchars($e->getTraceAsString()) . '</code>';
            echo '</div>';
        }
        ?>
        
        <div class="warning">
            <strong>⚠️ Importante:</strong>
            <ul>
                <li>Este script es solo para desarrollo/testing</li>
                <li>Elimina este archivo en producción</li>
                <li>Cambia la contraseña por defecto después del primer login</li>
            </ul>
        </div>
    </div>
</body>
</html>
