<?php
/**
 * LECTOR DE EXCEL - Interfaz con Drag & Drop
 * 
 * Interfaz simple para leer archivos Excel usando el microservicio Python
 */

session_start();

// URL del microservicio Python
define('MICROSERVICIO_URL', 'http://localhost:5000/api/read-excel');

$error = '';
$mensaje = '';
$datos_biometrico = null;
$datos_filtro = null;

// Verificar conexión con microservicio
function verificarMicroservicio() {
    $ch = curl_init('http://localhost:5000/api/health');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $http_code === 200;
}

$microservicio_disponible = verificarMicroservicio();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lector de Excel - Sistema RRHH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .drop-zone {
            border: 3px dashed #cbd5e0;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            background: #f7fafc;
            cursor: pointer;
            min-height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .drop-zone:hover {
            border-color: #4299e1;
            background: #ebf8ff;
        }
        .drop-zone.dragover {
            border-color: #2b6cb0;
            background: #bee3f8;
            transform: scale(1.02);
        }
        .drop-zone.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .file-info {
            margin-top: 15px;
            padding: 10px;
            background: #e6fffa;
            border-radius: 8px;
            display: none;
        }
        .file-info.show {
            display: block;
        }
        .resultado {
            max-height: 400px;
            overflow-y: auto;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4 max-w-6xl">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        <i class="fas fa-file-excel text-green-600 mr-2"></i>
                        Lector de Excel
                    </h1>
                    <p class="text-gray-600 mt-2">Lee archivos Excel usando el microservicio Python</p>
                </div>
                <a href="../dashboard.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Estado del Microservicio -->
        <?php if (!$microservicio_disponible): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Error:</strong> El microservicio Python no está disponible.
            <p class="text-sm mt-2">Asegúrate de que el microservicio esté corriendo en <code class="bg-red-200 px-2 py-1 rounded">http://localhost:5000</code></p>
        </div>
        <?php else: ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <i class="fas fa-check-circle mr-2"></i>
            <strong>Microservicio conectado:</strong> El servicio está disponible y funcionando.
        </div>
        <?php endif; ?>

        <!-- Mensajes -->
        <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <i class="fas fa-check-circle mr-2"></i>
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
        <?php endif; ?>

        <!-- Formulario de Subida -->
        <form id="form-archivos" class="bg-white rounded-lg shadow-md p-6" <?php echo $microservicio_disponible ? '' : 'onsubmit="return false;"'; ?>>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Área Biométrico -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        <i class="fas fa-fingerprint text-purple-600 mr-2"></i>
                        Archivo Biométrico
                    </h2>
                    <div class="drop-zone <?php echo $microservicio_disponible ? '' : 'disabled'; ?>" id="drop-zone-biometrico">
                        <input type="file" name="archivo_biometrico" id="archivo_biometrico" 
                               accept=".xls,.xlsx,.csv" class="hidden">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-2">
                            <strong>Arrastra y suelta</strong> el archivo aquí
                        </p>
                        <p class="text-sm text-gray-500 mb-4">o</p>
                        <button type="button" onclick="document.getElementById('archivo_biometrico').click()" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition"
                                <?php echo $microservicio_disponible ? '' : 'disabled'; ?>>
                            <i class="fas fa-folder-open mr-2"></i>Seleccionar Archivo
                        </button>
                        <p class="text-xs text-gray-500 mt-4">
                            Formatos: .xls, .xlsx, .csv (máx. 50MB)
                        </p>
                        <div class="file-info" id="info-biometrico">
                            <i class="fas fa-file-excel text-green-600 mr-2"></i>
                            <span id="nombre-biometrico"></span>
                        </div>
                    </div>
                    <div id="resultado-biometrico" class="mt-4 hidden">
                        <h3 class="font-semibold mb-2">Resultado:</h3>
                        <div class="resultado" id="contenido-biometrico"></div>
                    </div>
                </div>

                <!-- Área Filtro -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">
                        <i class="fas fa-filter text-blue-600 mr-2"></i>
                        Archivo Filtro (Excel)
                    </h2>
                    <div class="drop-zone <?php echo $microservicio_disponible ? '' : 'disabled'; ?>" id="drop-zone-filtro">
                        <input type="file" name="archivo_filtro" id="archivo_filtro" 
                               accept=".xls,.xlsx,.csv" class="hidden">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-2">
                            <strong>Arrastra y suelta</strong> el archivo aquí
                        </p>
                        <p class="text-sm text-gray-500 mb-4">o</p>
                        <button type="button" onclick="document.getElementById('archivo_filtro').click()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                                <?php echo $microservicio_disponible ? '' : 'disabled'; ?>>
                            <i class="fas fa-folder-open mr-2"></i>Seleccionar Archivo
                        </button>
                        <p class="text-xs text-gray-500 mt-4">
                            Formatos: .xls, .xlsx, .csv (máx. 50MB)
                        </p>
                        <div class="file-info" id="info-filtro">
                            <i class="fas fa-file-excel text-green-600 mr-2"></i>
                            <span id="nombre-filtro"></span>
                        </div>
                    </div>
                    <div id="resultado-filtro" class="mt-4 hidden">
                        <h3 class="font-semibold mb-2">Resultado:</h3>
                        <div class="resultado" id="contenido-filtro"></div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Información -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-6 rounded">
            <h3 class="font-semibold text-blue-800 mb-2">
                <i class="fas fa-info-circle mr-2"></i>¿Cómo funciona?
            </h3>
            <ul class="text-blue-700 text-sm space-y-1">
                <li>1. Arrastra y suelta o selecciona un archivo Excel (.xlsx, .xls, .csv)</li>
                <li>2. El archivo se enviará automáticamente al microservicio Python</li>
                <li>3. Los datos se mostrarán en formato JSON debajo del área de carga</li>
                <li>4. Puedes cargar ambos archivos (biométrico y filtro) independientemente</li>
            </ul>
        </div>
    </div>

    <script>
        const MICROSERVICIO_URL = '<?php echo MICROSERVICIO_URL; ?>';
        const microservicioDisponible = <?php echo $microservicio_disponible ? 'true' : 'false'; ?>;

        // Función para enviar archivo al microservicio
        async function enviarArchivo(archivo, tipo) {
            if (!microservicioDisponible) {
                alert('El microservicio no está disponible');
                return;
            }

            const formData = new FormData();
            formData.append('file', archivo);
            formData.append('header_row', '0');

            const resultadoDiv = document.getElementById(`resultado-${tipo}`);
            const contenidoDiv = document.getElementById(`contenido-${tipo}`);
            
            // Mostrar loading
            resultadoDiv.classList.remove('hidden');
            contenidoDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i><p class="mt-2">Procesando archivo...</p></div>';

            try {
                const response = await fetch(MICROSERVICIO_URL, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    contenidoDiv.innerHTML = `
                        <div class="mb-2">
                            <strong>Total de filas:</strong> ${data.total_rows}<br>
                            <strong>Columnas:</strong> ${data.columns.join(', ')}
                        </div>
                        <pre class="whitespace-pre-wrap">${JSON.stringify(data.data, null, 2)}</pre>
                    `;
                } else {
                    contenidoDiv.innerHTML = `<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>Error: ${data.error}</div>`;
                }
            } catch (error) {
                contenidoDiv.innerHTML = `<div class="text-red-600"><i class="fas fa-exclamation-circle mr-2"></i>Error de conexión: ${error.message}</div>`;
            }
        }

        // Configurar drag & drop para biométrico
        const dropZoneBiometrico = document.getElementById('drop-zone-biometrico');
        const fileInputBiometrico = document.getElementById('archivo_biometrico');
        const infoBiometrico = document.getElementById('info-biometrico');
        const nombreBiometrico = document.getElementById('nombre-biometrico');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZoneBiometrico.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZoneBiometrico.addEventListener(eventName, () => {
                if (microservicioDisponible) {
                    dropZoneBiometrico.classList.add('dragover');
                }
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZoneBiometrico.addEventListener(eventName, () => {
                dropZoneBiometrico.classList.remove('dragover');
            }, false);
        });

        dropZoneBiometrico.addEventListener('drop', (e) => {
            if (!microservicioDisponible) return;
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInputBiometrico.files = files;
                mostrarInfoArchivo(files[0], nombreBiometrico, infoBiometrico);
                enviarArchivo(files[0], 'biometrico');
            }
        });

        fileInputBiometrico.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                mostrarInfoArchivo(e.target.files[0], nombreBiometrico, infoBiometrico);
                enviarArchivo(e.target.files[0], 'biometrico');
            }
        });

        // Configurar drag & drop para filtro
        const dropZoneFiltro = document.getElementById('drop-zone-filtro');
        const fileInputFiltro = document.getElementById('archivo_filtro');
        const infoFiltro = document.getElementById('info-filtro');
        const nombreFiltro = document.getElementById('nombre-filtro');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZoneFiltro.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZoneFiltro.addEventListener(eventName, () => {
                if (microservicioDisponible) {
                    dropZoneFiltro.classList.add('dragover');
                }
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZoneFiltro.addEventListener(eventName, () => {
                dropZoneFiltro.classList.remove('dragover');
            }, false);
        });

        dropZoneFiltro.addEventListener('drop', (e) => {
            if (!microservicioDisponible) return;
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                fileInputFiltro.files = files;
                mostrarInfoArchivo(files[0], nombreFiltro, infoFiltro);
                enviarArchivo(files[0], 'filtro');
            }
        });

        fileInputFiltro.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                mostrarInfoArchivo(e.target.files[0], nombreFiltro, infoFiltro);
                enviarArchivo(e.target.files[0], 'filtro');
            }
        });

        function mostrarInfoArchivo(archivo, elementoNombre, elementoInfo) {
            if (archivo) {
                elementoNombre.textContent = archivo.name + ' (' + (archivo.size / 1024 / 1024).toFixed(2) + ' MB)';
                elementoInfo.classList.add('show');
            }
        }
    </script>
</body>
</html>

