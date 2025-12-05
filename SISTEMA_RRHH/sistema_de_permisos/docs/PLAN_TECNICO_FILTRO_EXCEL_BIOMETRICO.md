# 📋 PLAN TÉCNICO: Módulo Filtro Excel Biométrico

## 🎯 OBJETIVO

Crear un módulo independiente que procese datos del biométrico (~290 personas) usando un Excel de filtro con datos correctos, generando una base de datos limpia y normalizada.

---

## 🛠️ TECNOLOGÍAS Y HERRAMIENTAS

### Backend (PHP)
- **PHP 7.4+** (ya disponible en Laragon)
- **PhpSpreadsheet** (biblioteca para leer Excel)
  - Alternativa: `PHPExcel` (más antigua pero funcional)
  - Ventaja: No requiere extensiones adicionales, funciona con archivos .xls y .xlsx

### Frontend
- **HTML5 + CSS3 + JavaScript Vanilla**
  - Drag & Drop API nativa del navegador
  - Sin frameworks adicionales (mantiene consistencia con el sistema actual)
  - Tailwind CSS (ya usado en el sistema)

### Base de Datos
- **MySQL** (misma base de datos `recursos_humanos`)
  - Nueva tabla: `personal_biometrico` o `funcionarios_biometrico`
  - Mantiene consistencia con el sistema existente

### Estructura de Archivos
```
SISTEMA_RRHH/
└── filtro_excel_biometrico/
    ├── index.php                    # Página principal con drag & drop
    ├── procesar.php                 # Procesa los archivos Excel
    ├── verificar.php                 # Verifica datos antes de guardar
    ├── base_datos.php                # Vista de la base de datos generada
    ├── includes/
    │   ├── procesar_excel.php        # Funciones para leer Excel
    │   ├── normalizar_datos.php      # Funciones de normalización
    │   └── validar_datos.php         # Validaciones
    ├── uploads/                      # Carpeta temporal para archivos
    └── docs/
        └── estructura_datos.md       # Documentación de estructura
```

---

## 📊 PROCESO DE FILTRADO Y NORMALIZACIÓN

### Paso 1: Lectura de Archivos Excel

**Archivo Biométrico:**
- Leer todas las filas
- Columnas esperadas: ID (cédula sin guiones), Nombre, Apellido, etc.
- ~290 registros

**Archivo Filtro (Excel):**
- Leer todas las filas
- Columnas esperadas: Cédula (con guiones), Nombre completo, etc.
- Menos registros (solo personal activo)

### Paso 2: Normalización de Datos

**Cédulas:**
```php
// Del biométrico: "123456789" (sin guiones)
// Del Excel filtro: "1-2345-6789" (con guiones)
// Normalizar: Quitar guiones del Excel para comparar
// Guardar: Con guiones (formato del Excel filtro)
```

**Nombres:**
```php
// Del biométrico: "JUAN CARLOS" "PEREZ GARCIA" (todo mayúsculas)
// Del Excel: "Juan Carlos" "Perez Garcia" (puede estar mezclado)
// Normalizar: Primera letra mayúscula, resto minúsculas
// Resultado: "Juan Carlos" "Perez Garcia"
```

### Paso 3: Proceso de Filtrado

**IMPORTANTE:** El Excel filtro tiene TODA la información correcta y relevante. Solo se reemplazan nombre y apellido.

```
1. Leer archivo biométrico → Array de datos
2. Leer archivo filtro → Array de datos
3. Para cada registro del filtro:
   a. Buscar en biométrico usando cédula (sin guiones)
   b. Si encuentra:
      - Tomar TODOS los datos del filtro (cédula con guiones, y todos los demás campos)
      - EXCEPTO: nombre y apellido (que están juntos en el filtro)
      - Tomar nombre y apellido SEPARADOS del biométrico
      - Normalizar nombres (primera letra mayúscula, resto minúsculas)
      - Combinar: Datos del filtro + Nombre/Apellido del biométrico normalizados
   c. Si no encuentra: 
      - Tomar datos del filtro igualmente
      - Marcar como "No encontrado en biométrico" (sin nombre/apellido separados)
4. Generar array de datos limpios
5. Mostrar vista previa para revisión
6. Guardar en base de datos
```

**Ejemplo:**
- Excel Filtro: Cédula="1-2345-6789", NombreCompleto="Juan Perez", Cargo="Analista", Departamento="IT", etc.
- Biométrico: ID="123456789", Nombre="JUAN", Apellido="PEREZ"
- Resultado: Cédula="1-2345-6789", Nombre="Juan", Apellido="Perez", Cargo="Analista", Departamento="IT", etc.

### Paso 4: Estructura de Base de Datos

**NOTA:** La estructura dependerá de las columnas que tenga el Excel filtro. Esta es una estructura base que se puede expandir:

```sql
CREATE TABLE personal_biometrico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) UNIQUE NOT NULL COMMENT 'Cédula con guiones (del Excel filtro)',
    cedula_sin_guiones VARCHAR(20) NOT NULL COMMENT 'Cédula sin guiones (para comparar con ID biométrico)',
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre del biométrico normalizado (primera letra mayúscula)',
    apellido VARCHAR(255) NOT NULL COMMENT 'Apellido del biométrico normalizado (primera letra mayúscula)',
    nombre_completo VARCHAR(255) NOT NULL COMMENT 'Nombre + Apellido concatenados',
    id_biometrico VARCHAR(50) COMMENT 'ID del biométrico (para matching futuro con asistencia)',
    
    -- Campos adicionales del Excel filtro (ejemplos, se ajustarán según el Excel real)
    cargo VARCHAR(255) COMMENT 'Del Excel filtro',
    departamento VARCHAR(255) COMMENT 'Del Excel filtro',
    sede VARCHAR(255) COMMENT 'Del Excel filtro',
    oficina_regional VARCHAR(255) COMMENT 'Del Excel filtro',
    numero_posicion VARCHAR(50) COMMENT 'Del Excel filtro',
    -- ... otros campos que tenga el Excel filtro
    
    activo TINYINT(1) DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
    encontrado_biometrico TINYINT(1) DEFAULT 0 COMMENT '1=Encontrado en biométrico, 0=No encontrado',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_cedula (cedula),
    INDEX idx_cedula_sin_guiones (cedula_sin_guiones),
    INDEX idx_id_biometrico (id_biometrico)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Importante:** La estructura de la tabla se generará dinámicamente según las columnas que tenga el Excel filtro.

---

## 🎨 INTERFAZ DE USUARIO

### Página Principal (index.php)

**Diseño:**
- Dos áreas de drag & drop (una para biométrico, otra para filtro)
- Botón alternativo para seleccionar archivos
- Indicador visual cuando se arrastra un archivo
- Botón "Procesar" que se activa cuando ambos archivos están listos

**Características:**
- Validación de tipo de archivo (.xls, .xlsx, .csv)
- Preview de nombres de archivos seleccionados
- Mensajes de error claros

### Vista de Base de Datos (base_datos.php)

**Características:**
- Tabla con paginación
- Búsqueda por cédula, nombre, apellido
- Filtros: Activos/Inactivos, Encontrados/No encontrados
- Exportar a Excel
- Edición inline (opcional)
- Estadísticas: Total registros, Activos, etc.

---

## 🔧 FUNCIONES PRINCIPALES

### 1. Leer Excel (procesar_excel.php)

```php
function leerArchivoExcel($ruta_archivo) {
    // Usar PhpSpreadsheet
    // Retornar array asociativo con datos
}

function detectarColumnas($datos) {
    // Detectar automáticamente qué columna es cédula, nombre, etc.
    // O usar configuración manual
}
```

### 2. Normalizar Datos (normalizar_datos.php)

```php
function normalizarCedula($cedula) {
    // Quitar guiones para comparar
    // Retornar con guiones para guardar
}

function normalizarNombre($texto) {
    // Convertir a: Primera letra mayúscula, resto minúsculas
    // Manejar múltiples palabras
}
```

### 3. Filtrar y Combinar (procesar.php)

```php
function filtrarDatos($datos_biometrico, $datos_filtro) {
    // Para cada registro del filtro:
    // 1. Tomar TODOS los campos del filtro
    // 2. Buscar en biométrico por cédula (sin guiones)
    // 3. Si encuentra: Reemplazar nombre y apellido con los del biométrico (normalizados)
    // 4. Si no encuentra: Mantener datos del filtro, marcar como no encontrado
    // Retornar array de datos limpios
}

function combinarDatos($registro_filtro, $registro_biometrico) {
    // Tomar todo del filtro
    $resultado = $registro_filtro;
    
    // Reemplazar solo nombre y apellido del biométrico
    if ($registro_biometrico) {
        $resultado['nombre'] = normalizarNombre($registro_biometrico['nombre']);
        $resultado['apellido'] = normalizarNombre($registro_biometrico['apellido']);
        $resultado['nombre_completo'] = $resultado['nombre'] . ' ' . $resultado['apellido'];
        $resultado['id_biometrico'] = $registro_biometrico['id'];
        $resultado['encontrado_biometrico'] = 1;
    } else {
        $resultado['encontrado_biometrico'] = 0;
    }
    
    return $resultado;
}
```

---

## 📝 FLUJO DE USO

1. **Usuario abre:** `filtro_excel_biometrico/index.php`
2. **Arrastra archivo biométrico** → Se muestra nombre del archivo
3. **Arrastra archivo filtro** → Se muestra nombre del archivo
4. **Hace clic en "Procesar"** → Se suben los archivos
5. **Sistema procesa:**
   - Lee ambos archivos
   - Normaliza datos
   - Filtra y combina
   - Muestra vista previa
6. **Usuario revisa** → Puede corregir manualmente si es necesario
7. **Usuario confirma** → Se guarda en base de datos
8. **Usuario ve resultado** → Tabla con todos los registros

---

## ⚠️ CONSIDERACIONES IMPORTANTES

### Manejo de Errores
- Archivos corruptos
- Columnas faltantes
- Cédulas duplicadas
- Registros no encontrados en biométrico
- Nombres con caracteres especiales

### Validaciones
- Formato de cédula válido
- Nombres no vacíos
- Archivos no muy grandes (límite de memoria PHP)
- Tipos de archivo permitidos

### Seguridad
- Validar tipos MIME reales (no solo extensión)
- Limpiar nombres de archivos
- Limitar tamaño de archivos
- Eliminar archivos temporales después de procesar

---

## 🚀 VENTAJAS DE ESTE ENFOQUE

1. **Sin dependencias externas complejas:** PhpSpreadsheet es fácil de instalar
2. **Consistente con el sistema actual:** Mismo stack tecnológico
3. **Interfaz intuitiva:** Drag & drop es familiar para usuarios
4. **Base de datos normalizada:** Lista para usar en cálculos futuros
5. **Escalable:** Fácil agregar más normalizaciones o validaciones

---

## 📦 INSTALACIÓN DE PhpSpreadsheet

```bash
# Opción 1: Composer (recomendado)
composer require phpoffice/phpspreadsheet

# Opción 2: Descarga manual
# Descargar desde: https://github.com/PHPOffice/PhpSpreadsheet
# Extraer en: vendor/PhpOffice/PhpSpreadsheet
```

---

## 🔄 PRÓXIMOS PASOS (Futuro)

1. Integración con módulo de asistencia
2. Cálculo automático de horas trabajadas
3. Reportes y estadísticas
4. Exportación a diferentes formatos
5. Historial de cambios

---

**¿Te parece bien este enfoque?** Si estás de acuerdo, procedo a crear el plan detallado con todos los pasos de implementación.

