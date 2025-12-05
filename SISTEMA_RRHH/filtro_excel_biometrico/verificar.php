<?php
/**
 * SCRIPT RÁPIDO DE VERIFICACIÓN
 * 
 * Ejecuta este archivo en tu navegador para verificar la instalación
 * 
 * URL: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/filtro_excel_biometrico/VERIFICAR.php
 */

echo "<h1>🔍 Verificación de PhpSpreadsheet</h1>";
echo "<hr>";

$vendor_dir = __DIR__ . '/../vendor';
$phpspreadsheet_dir = $vendor_dir . '/PhpOffice/PhpSpreadsheet/src';
$phpspreadsheet_file1 = $phpspreadsheet_dir . '/PhpSpreadsheet/Spreadsheet.php';
$phpspreadsheet_file2 = $phpspreadsheet_dir . '/PhpSpreadsheet.php';
// IOFactory puede estar en diferentes ubicaciones
$iofactory_file1 = $phpspreadsheet_dir . '/PhpSpreadsheet/IOFactory.php';
$iofactory_file2 = $phpspreadsheet_dir . '/IOFactory.php';
$iofactory_file = file_exists($iofactory_file1) ? $iofactory_file1 : $iofactory_file2;

$errores = [];
$exitos = [];

// Verificar estructura
echo "<h2>1. Verificando Estructura de Carpetas</h2>";

if (!file_exists($vendor_dir)) {
    $errores[] = "❌ La carpeta <code>vendor</code> no existe en: " . htmlspecialchars($vendor_dir);
} else {
    $exitos[] = "✅ Carpeta <code>vendor</code> existe";
}

if (!file_exists($phpspreadsheet_dir)) {
    $errores[] = "❌ La carpeta <code>PhpOffice/PhpSpreadsheet/src</code> no existe";
} else {
    $exitos[] = "✅ Carpeta <code>PhpSpreadsheet/src</code> existe";
}

// Verificar archivos
echo "<h2>2. Verificando Archivos Principales</h2>";

if (file_exists($phpspreadsheet_file1)) {
    $exitos[] = "✅ Archivo <code>PhpSpreadsheet/Spreadsheet.php</code> encontrado";
    $archivo_principal = $phpspreadsheet_file1;
} elseif (file_exists($phpspreadsheet_file2)) {
    $exitos[] = "✅ Archivo <code>PhpSpreadsheet.php</code> encontrado";
    $archivo_principal = $phpspreadsheet_file2;
} else {
    $errores[] = "❌ No se encontró el archivo principal de PhpSpreadsheet";
    $errores[] = "   Buscado en: " . htmlspecialchars($phpspreadsheet_file1);
    $errores[] = "   Buscado en: " . htmlspecialchars($phpspreadsheet_file2);
    $archivo_principal = null;
}

if (file_exists($iofactory_file1)) {
    $exitos[] = "✅ Archivo <code>PhpSpreadsheet/IOFactory.php</code> encontrado";
} elseif (file_exists($iofactory_file2)) {
    $exitos[] = "✅ Archivo <code>IOFactory.php</code> encontrado (ubicación alternativa)";
} else {
    $errores[] = "⚠️ Archivo <code>IOFactory.php</code> no encontrado";
    $errores[] = "   Buscado en: " . htmlspecialchars($iofactory_file1);
    $errores[] = "   Buscado en: " . htmlspecialchars($iofactory_file2);
}

// Intentar cargar PhpSpreadsheet
echo "<h2>3. Intentando Cargar PhpSpreadsheet</h2>";

if ($archivo_principal) {
    try {
        // Cargar autoloader
        $autoload_path = __DIR__ . '/../vendor/autoload_phpspreadsheet.php';
        if (file_exists($autoload_path)) {
            require_once $autoload_path;
            $exitos[] = "✅ Autoloader cargado";
        } else {
            // Intentar cargar manualmente
            if (file_exists($phpspreadsheet_file1)) {
                require_once $phpspreadsheet_file1;
            } else {
                require_once $phpspreadsheet_file2;
            }
            // Intentar cargar IOFactory en diferentes ubicaciones
            if (file_exists($iofactory_file1)) {
                require_once $iofactory_file1;
            } elseif (file_exists($iofactory_file2)) {
                require_once $iofactory_file2;
            }
        }
        
        // Verificar si las clases están disponibles
        if (class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
            $exitos[] = "✅ Clase <code>PhpOffice\\PhpSpreadsheet\\IOFactory</code> está disponible";
        } else {
            $errores[] = "❌ Clase <code>IOFactory</code> no se puede cargar";
        }
        
        if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $exitos[] = "✅ Clase <code>PhpOffice\\PhpSpreadsheet\\Spreadsheet</code> está disponible";
        }
        
    } catch (Exception $e) {
        $errores[] = "❌ Error al cargar PhpSpreadsheet: " . htmlspecialchars($e->getMessage());
    }
} else {
    $errores[] = "❌ No se puede cargar PhpSpreadsheet: archivo principal no encontrado";
}

// Mostrar resultados
echo "<h2>📊 Resultados</h2>";

if (!empty($exitos)) {
    echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Verificaciones Exitosas:</h3>";
    echo "<ul>";
    foreach ($exitos as $exito) {
        echo "<li style='color: #155724; margin: 5px 0;'>" . $exito . "</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (!empty($errores)) {
    echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>❌ Problemas Encontrados:</h3>";
    echo "<ul>";
    foreach ($errores as $error) {
        echo "<li style='color: #721c24; margin: 5px 0;'>" . $error . "</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Resultado final
echo "<hr>";
if (empty($errores) || (count($errores) == 1 && strpos($errores[0], 'IOFactory.php') !== false)) {
    echo "<div style='background: #d1ecf1; border: 3px solid #0c5460; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0;'>";
    echo "<h2 style='color: #0c5460; margin-top: 0;'>🎉 ¡PhpSpreadsheet Está Instalado Correctamente!</h2>";
    echo "<p style='color: #0c5460; font-size: 18px;'>Puedes usar el módulo de filtro Excel biométrico.</p>";
    echo "<a href='index.php' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background: #0c5460; color: white; text-decoration: none; border-radius: 5px;'>Ir al Módulo de Filtro</a>";
    echo "</div>";
} else {
    echo "<div style='background: #fff3cd; border: 3px solid #856404; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0;'>";
    echo "<h2 style='color: #856404; margin-top: 0;'>⚠️ PhpSpreadsheet No Está Completamente Instalado</h2>";
    echo "<p style='color: #856404;'>Revisa los errores arriba y sigue las instrucciones de instalación.</p>";
    echo "<a href='instrucciones_instalacion.php' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background: #856404; color: white; text-decoration: none; border-radius: 5px;'>Ver Instrucciones de Instalación</a>";
    echo "</div>";
}

// Información adicional
echo "<hr>";
echo "<h2>📁 Rutas Verificadas:</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px;'>";
echo "<strong>Vendor:</strong> " . htmlspecialchars($vendor_dir) . "<br>";
echo "<strong>PhpSpreadsheet:</strong> " . htmlspecialchars($phpspreadsheet_dir) . "<br>";
if ($archivo_principal) {
    echo "<strong>Archivo Principal:</strong> " . htmlspecialchars($archivo_principal) . "<br>";
}
echo "</div>";

echo "<hr>";
echo "<p><a href='index.php'>← Volver al Módulo</a> | <a href='verificar_instalacion.php'>Ver Verificación Detallada</a></p>";
?>






