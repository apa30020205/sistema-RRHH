<?php
/**
 * SCRIPT DE DIAGNÓSTICO DE CONEXIÓN
 * 
 * Este archivo te ayuda a diagnosticar problemas con MySQL
 */

echo "<h2>🔍 Diagnóstico de Conexión MySQL</h2>";
echo "<hr>";

// Probar diferentes configuraciones
$configs = [
    ['host' => 'localhost', 'puerto' => 3306, 'desc' => 'Configuración estándar (localhost:3306)'],
    ['host' => '127.0.0.1', 'puerto' => 3306, 'desc' => 'IP local (127.0.0.1:3306)'],
    ['host' => 'localhost', 'puerto' => 3307, 'desc' => 'Puerto alternativo (localhost:3307)'],
    ['host' => '127.0.0.1', 'puerto' => 3307, 'desc' => 'IP local puerto alternativo (127.0.0.1:3307)'],
];

foreach ($configs as $config) {
    echo "<h3>Probando: {$config['desc']}</h3>";
    
    $host = $config['host'];
    $puerto = $config['puerto'];
    
    // Intentar conectar sin especificar base de datos (usando @ para suprimir warnings)
    $conn = null;
    $error = '';
    
    try {
        $conn = @new mysqli($host, 'root', '', null, $puerto);
        if ($conn->connect_error) {
            $error = $conn->connect_error;
            $conn = null;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        $conn = null;
    }
    
    if ($conn && !$conn->connect_error) {
        echo "✅ <strong>¡ÉXITO!</strong> Conexión establecida<br>";
        echo "Puerto usado: <strong>$puerto</strong><br>";
        echo "Host usado: <strong>$host</strong><br>";
        
        // Probar crear la base de datos
        $sql = "CREATE DATABASE IF NOT EXISTS recursos_humanos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if ($conn->query($sql)) {
            echo "✅ Base de datos creada o ya existe<br>";
        }
        
        $conn->close();
        echo "<hr>";
        echo "<h3 style='color: green;'>🎉 ¡CONFIGURACIÓN FUNCIONAL ENCONTRADA!</h3>";
        echo "<p>Usa esta configuración en <code>config/database.php</code>:</p>";
        echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
        echo "define('DB_HOST', '$host');\n";
        echo "define('DB_USER', 'root');\n";
        echo "define('DB_PASS', '');\n";
        echo "define('DB_NAME', 'recursos_humanos');\n";
        echo "define('DB_PORT', $puerto);  // Agregar esta línea\n";
        echo "</pre>";
        $conexion_exitosa = true;
        break;
    } else {
        echo "❌ <strong>FALLÓ:</strong> " . ($error ?: 'No se pudo conectar') . "<br>";
    }
    
    echo "<br>";
}

// Si ninguna conexión funcionó
if (!isset($conexion_exitosa)) {
    echo "<hr>";
    echo "<div style='background: #fee; border: 3px solid #f00; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h2 style='color: #c00;'>❌ NINGUNA CONEXIÓN FUNCIONÓ</h2>";
    echo "<p><strong>Esto significa que MySQL NO está corriendo en tu computadora.</strong></p>";
    echo "<h3>🔧 SOLUCIÓN INMEDIATA:</h3>";
    echo "<ol style='line-height: 2;'>";
    echo "<li><strong>Abre Laragon</strong> (busca el ícono en la barra de tareas)</li>";
    echo "<li><strong>Busca el servicio MySQL</strong> en la lista de servicios</li>";
    echo "<li><strong>Si dice [OFF] o está en ROJO</strong>, haz clic en él para activarlo</li>";
    echo "<li><strong>Espera 15-20 segundos</strong> a que MySQL inicie completamente</li>";
    echo "<li><strong>Verifica que cambie a [ON] o VERDE</strong></li>";
    echo "<li><strong>Recarga esta página</strong> (F5) y vuelve a ejecutar el diagnóstico</li>";
    echo "</ol>";
    echo "<p style='margin-top: 15px;'><strong>📄 También puedes leer:</strong> <code>SOLUCION_MYSQL.md</code> para instrucciones detalladas.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>📋 Verificaciones Adicionales</h3>";

// Verificar si MySQL está corriendo como proceso
echo "<h4>1. Verificar proceso MySQL:</h4>";
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows
    exec('tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL', $output);
    if (!empty($output) && count($output) > 1) {
        echo "✅ <strong>MySQL está corriendo</strong> (proceso mysqld.exe encontrado)<br>";
    } else {
        echo "❌ <strong>MySQL NO está corriendo</strong> (proceso mysqld.exe no encontrado)<br>";
        echo "<p style='color: red;'><strong>SOLUCIÓN:</strong> Abre Laragon y activa MySQL. Debe aparecer en verde.</p>";
    }
}

echo "<h4>2. Instrucciones:</h4>";
echo "<ol>";
echo "<li>Abre <strong>Laragon</strong></li>";
echo "<li>Busca el servicio <strong>MySQL</strong></li>";
echo "<li>Si está en <span style='color: red;'>rojo</span> o dice <strong>OFF</strong>, haz clic para activarlo</li>";
echo "<li>Espera 10-15 segundos a que inicie completamente</li>";
echo "<li>Debería aparecer en <span style='color: green;'>verde</span> o <strong>ON</strong></li>";
echo "<li>Recarga esta página</li>";
echo "</ol>";

echo "<h4>3. Verificar Firewall:</h4>";
echo "<p>Si MySQL está activo pero sigue sin conectar, puede ser el firewall:</p>";
echo "<ul>";
echo "<li>Ve a: <strong>Configuración de Windows → Firewall de Windows</strong></li>";
echo "<li>Permite a Laragon/MySQL a través del firewall</li>";
echo "</ul>";

echo "<h4>4. Alternativa: Verificar en phpMyAdmin</h4>";
echo "<p>Abre: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></p>";
echo "<p>Si phpMyAdmin abre correctamente, MySQL está corriendo y el problema es de configuración.</p>";
?>

