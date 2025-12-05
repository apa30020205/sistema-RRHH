<?php
/**
 * SCRIPT DE INSTALACIÓN DE PHPMailer
 * 
 * Este script descarga e instala PHPMailer automáticamente
 */

echo "<h2>📦 Instalador de PHPMailer</h2>";
echo "<hr>";

$vendor_dir = __DIR__ . '/vendor';
$phpmailer_dir = $vendor_dir . '/PHPMailer';

// Verificar si ya está instalado
if (file_exists($phpmailer_dir . '/src/PHPMailer.php')) {
    echo "<p style='color: green;'>✅ PHPMailer ya está instalado en: " . htmlspecialchars($phpmailer_dir) . "</p>";
    echo "<p><a href='test_email.php'>Probar envío de email</a></p>";
    exit;
}

echo "<p>Instalando PHPMailer...</p>";

// Crear directorios
if (!file_exists($vendor_dir)) {
    mkdir($vendor_dir, 0777, true);
    echo "<p>✅ Directorio vendor creado</p>";
}

if (!file_exists($phpmailer_dir)) {
    mkdir($phpmailer_dir, 0777, true);
    echo "<p>✅ Directorio PHPMailer creado</p>";
}

// URL de descarga de PHPMailer (última versión estable)
$phpmailer_url = 'https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.zip';
$zip_file = $vendor_dir . '/phpmailer.zip';

echo "<p>Descargando PHPMailer desde GitHub...</p>";

// Descargar ZIP
$zip_content = @file_get_contents($phpmailer_url);

if ($zip_content === false) {
    echo "<p style='color: red;'>❌ Error: No se pudo descargar PHPMailer.</p>";
    echo "<p><strong>Solución manual:</strong></p>";
    echo "<ol>";
    echo "<li>Descarga PHPMailer desde: <a href='https://github.com/PHPMailer/PHPMailer/releases' target='_blank'>GitHub Releases</a></li>";
    echo "<li>Extrae el ZIP</li>";
    echo "<li>Copia la carpeta 'src' a: <code>" . htmlspecialchars($phpmailer_dir) . "/src</code></li>";
    echo "</ol>";
    exit;
}

// Guardar ZIP
file_put_contents($zip_file, $zip_content);
echo "<p>✅ ZIP descargado</p>";

// Verificar si ZipArchive está disponible
if (!class_exists('ZipArchive')) {
    echo "<p style='color: orange;'>⚠️ ZipArchive no está disponible. Necesitas extraer manualmente.</p>";
    echo "<p>Extrae el archivo: <code>" . htmlspecialchars($zip_file) . "</code></p>";
    echo "<p>Y copia la carpeta 'src' a: <code>" . htmlspecialchars($phpmailer_dir) . "/src</code></p>";
    exit;
}

// Extraer ZIP
$zip = new ZipArchive;
if ($zip->open($zip_file) === TRUE) {
    // Extraer todo el contenido
    $zip->extractTo($phpmailer_dir);
    $zip->close();
    
    // Buscar la carpeta src dentro del ZIP extraído
    $extracted_dir = $phpmailer_dir . '/PHPMailer-6.9.1';
    if (file_exists($extracted_dir . '/src')) {
        // Mover src al lugar correcto
        rename($extracted_dir . '/src', $phpmailer_dir . '/src');
        // Eliminar carpeta temporal
        deleteDirectory($extracted_dir);
    }
    
    echo "<p>✅ PHPMailer extraído</p>";
} else {
    echo "<p style='color: red;'>❌ Error al extraer el ZIP</p>";
    exit;
}

// Crear autoload.php
$autoload_content = <<<'PHP'
<?php
/**
 * Autoloader simple para PHPMailer
 */
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
PHP;

file_put_contents($vendor_dir . '/autoload.php', $autoload_content);
echo "<p>✅ autoload.php creado</p>";

// Eliminar ZIP
unlink($zip_file);

echo "<hr>";
echo "<p style='color: green; font-size: 18px;'>✅ PHPMailer instalado exitosamente!</p>";
echo "<p><a href='test_email.php'>Probar envío de email ahora</a></p>";

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}
?>


