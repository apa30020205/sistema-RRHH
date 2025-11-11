<?php
/**
 * CONFIGURACIÓN DE BASE DE DATOS
 * 
 * Este archivo contiene la conexión a la base de datos MySQL.
 * Laragon usa estas configuraciones por defecto.
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');      // Dirección del servidor MySQL (localhost en Laragon)
define('DB_USER', 'root');            // Usuario de MySQL (por defecto 'root' en Laragon)
define('DB_PASS', '');                // Contraseña (por defecto vacía en Laragon)
define('DB_NAME', 'recursos_humanos'); // Nombre de la base de datos
define('DB_PORT', 3306);               // Puerto de MySQL (3306 es el predeterminado, 3307 puede ser alternativo)

/**
 * Función para conectar a la base de datos
 * @return mysqli|false Retorna la conexión o false si hay error
 */
function conectarDB() {
    // Crear conexión (sin seleccionar base de datos primero para verificar que MySQL esté corriendo)
    // Intentar con el puerto especificado, si no funciona, probar sin puerto
    try {
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT);
        
        // Si falla, intentar sin especificar puerto
        if ($conn->connect_error) {
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);
        }
        
        // Si aún falla, intentar con 127.0.0.1
        if ($conn->connect_error) {
            $conn = @new mysqli('127.0.0.1', DB_USER, DB_PASS);
        }
    } catch (Exception $e) {
        // Si todo falla, mostrar mensaje de error
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    }
    
    // Verificar conexión a MySQL
    if ($conn->connect_error) {
        die("
        <div style='padding: 20px; background: #fee; border: 2px solid #f00; border-radius: 10px; max-width: 600px; margin: 50px auto; font-family: Arial;'>
            <h2 style='color: #c00;'>❌ Error: MySQL no está corriendo</h2>
            <p><strong>El problema:</strong> No se puede conectar a MySQL.</p>
            <p><strong>Solución:</strong></p>
            <ol>
                <li>Abre <strong>Laragon</strong></li>
                <li>Verifica que <strong>MySQL</strong> esté <span style='color: green; font-weight: bold;'>ON</span> (debe aparecer en verde)</li>
                <li>Si está en rojo, haz clic en <strong>MySQL</strong> para activarlo</li>
                <li>Espera unos segundos y recarga esta página</li>
            </ol>
            <p style='margin-top: 20px;'><strong>Mensaje técnico:</strong> " . $conn->connect_error . "</p>
        </div>");
    }
    
    // Seleccionar la base de datos
    $conn->select_db(DB_NAME);
    
    // Si la base de datos no existe, crear un mensaje más claro
    if ($conn->error && $conn->errno == 1049) {
        die("
        <div style='padding: 20px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 10px; max-width: 600px; margin: 50px auto; font-family: Arial;'>
            <h2 style='color: #856404;'>⚠️ Base de datos no encontrada</h2>
            <p><strong>La base de datos 'recursos_humanos' no existe.</strong></p>
            <p><strong>Solución:</strong></p>
            <ol>
                <li>Abre phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>
                <li>Ve a la pestaña <strong>SQL</strong></li>
                <li>Abre el archivo: <code>database/schema.sql</code></li>
                <li>Copia todo el contenido y pégalo en phpMyAdmin</li>
                <li>Haz clic en <strong>Ejecutar</strong></li>
            </ol>
        </div>");
    }
    
    // Establecer charset UTF-8 para caracteres especiales (ñ, acentos, etc.)
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

/**
 * Función para cerrar la conexión
 * @param mysqli $conn Conexión a cerrar
 */
function cerrarDB($conn) {
    if ($conn) {
        $conn->close();
    }
}
?>

