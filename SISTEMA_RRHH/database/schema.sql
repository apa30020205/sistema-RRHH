-- =====================================================
-- SISTEMA DE GESTIÓN DE RECURSOS HUMANOS
-- Base de Datos: recursos_humanos
-- =====================================================

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS recursos_humanos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE recursos_humanos;

-- =====================================================
-- TABLA: funcionarios
-- Almacena la información básica de cada funcionario
-- =====================================================
CREATE TABLE IF NOT EXISTS funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) UNIQUE NOT NULL COMMENT 'Cédula del funcionario (única)',
    nombre_completo VARCHAR(255) NOT NULL,
    cargo VARCHAR(255) NOT NULL,
    numero_posicion VARCHAR(50) NOT NULL,
    sede VARCHAR(255) NOT NULL COMMENT 'Dirección/Departamento',
    oficina_regional VARCHAR(255) NOT NULL COMMENT 'Provincia/Comarca',
    nombre_jefe_inmediato VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL COMMENT 'Contraseña encriptada',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo TINYINT(1) DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
    INDEX idx_cedula (cedula),
    INDEX idx_nombre (nombre_completo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: jornadas_extraordinarias
-- Formulario de Jornada Extraordinaria
-- =====================================================
CREATE TABLE IF NOT EXISTS jornadas_extraordinarias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    justificacion TEXT NOT NULL COMMENT 'Tareas a realizar',
    fecha_solicitud DATE NOT NULL,
    autorizado_por VARCHAR(255),
    fecha_autorizacion DATE,
    superior_inmediato VARCHAR(255),
    fecha_superior DATE,
    director_area VARCHAR(255),
    fecha_director DATE,
    jefe_rrhh VARCHAR(255),
    fecha_jefe_rrhh DATE,
    estado VARCHAR(50) DEFAULT 'pendiente' COMMENT 'pendiente, aprobado, rechazado',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE,
    INDEX idx_funcionario (funcionario_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: jornadas_extraordinarias_horarios
-- Horarios de jornada extraordinaria (relación uno a muchos)
-- =====================================================
CREATE TABLE IF NOT EXISTS jornadas_extraordinarias_horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jornada_id INT NOT NULL,
    fecha DATE NOT NULL,
    desde_hora TIME NOT NULL,
    hasta_hora TIME NOT NULL,
    total_horas DECIMAL(5,2) NOT NULL,
    FOREIGN KEY (jornada_id) REFERENCES jornadas_extraordinarias(id) ON DELETE CASCADE,
    INDEX idx_jornada (jornada_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: misiones_oficiales
-- Formulario de Misión Oficial
-- =====================================================
CREATE TABLE IF NOT EXISTS misiones_oficiales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    fecha_mision DATE NOT NULL,
    desde_hora TIME NOT NULL,
    hasta_hora TIME NOT NULL,
    motivo TEXT NOT NULL,
    fecha_solicitud DATE,
    revisado_por VARCHAR(255),
    fecha_revision DATE,
    observaciones TEXT,
    estado VARCHAR(50) DEFAULT 'pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE,
    INDEX idx_funcionario (funcionario_id),
    INDEX idx_fecha (fecha_mision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: reincorporaciones
-- Formulario de Reincorporación
-- =====================================================
CREATE TABLE IF NOT EXISTS reincorporaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    motivo_ausencia VARCHAR(255) NOT NULL COMMENT 'licencia con sueldo, sin sueldo, vacaciones, etc.',
    puesto VARCHAR(255) NOT NULL,
    posicion_numero VARCHAR(50) NOT NULL,
    unidad_administrativa VARCHAR(255) NOT NULL,
    fecha_reincorporacion DATE NOT NULL,
    fecha_firma_funcionario DATE,
    fecha_firma_jefe DATE,
    jefe_oirh VARCHAR(255),
    fecha_jefe_oirh DATE,
    estado VARCHAR(50) DEFAULT 'pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE,
    INDEX idx_funcionario (funcionario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: tiempo_compensatorio
-- Formulario del Uso de Tiempo Compensatorio
-- =====================================================
CREATE TABLE IF NOT EXISTS tiempo_compensatorio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    horas INT NOT NULL DEFAULT 0,
    dias INT NOT NULL DEFAULT 0,
    fecha_uso DATE NOT NULL,
    fecha_solicitud DATE,
    fecha_aprobacion_jefe DATE,
    saldo VARCHAR(100),
    tiempo_tomado VARCHAR(100),
    pendiente_por_tomar VARCHAR(100),
    observaciones TEXT,
    registrado_por VARCHAR(255),
    fecha_registro DATE,
    estado VARCHAR(50) DEFAULT 'pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE,
    INDEX idx_funcionario (funcionario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: solicitudes_permiso
-- Solicitud de Permiso
-- =====================================================
CREATE TABLE IF NOT EXISTS solicitudes_permiso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    motivo TEXT NOT NULL COMMENT 'enfermedad, duelo, matrimonio, etc.',
    motivo_otros TEXT COMMENT 'Si selecciona "otros"',
    desde_hora TIME,
    desde_dia INT,
    desde_mes INT,
    desde_anio INT,
    hasta_hora TIME,
    hasta_dia INT,
    hasta_mes INT,
    hasta_anio INT,
    fecha_solicitud DATE,
    fecha_firma_jefe DATE,
    total VARCHAR(100),
    utilizado VARCHAR(100),
    saldo VARCHAR(100),
    observaciones TEXT,
    registrado_por VARCHAR(255),
    fecha_registro DATE,
    enterado VARCHAR(255),
    estado VARCHAR(50) DEFAULT 'pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE,
    INDEX idx_funcionario (funcionario_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: solicitudes_vacaciones
-- Solicitud de Vacaciones
-- =====================================================
CREATE TABLE IF NOT EXISTS solicitudes_vacaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    dias_declaracion INT NOT NULL COMMENT 'Días de vacaciones solicitados',
    fecha_efectiva DATE NOT NULL COMMENT 'Las mismas se harán efectivas',
    fecha_retorno DATE NOT NULL COMMENT 'Retornando a mis labores',
    observaciones TEXT,
    revisado_por VARCHAR(255),
    fecha_revision DATE,
    autorizado_oirh VARCHAR(255),
    fecha_autorizacion_oirh DATE,
    fecha_firma_funcionario DATE,
    fecha_firma_jefe DATE,
    estado VARCHAR(50) DEFAULT 'pendiente',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE,
    INDEX idx_funcionario (funcionario_id),
    INDEX idx_fecha (fecha_efectiva)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: vacaciones_detalle
-- Detalle de vacaciones correspondientes (relación uno a muchos)
-- =====================================================
CREATE TABLE IF NOT EXISTS vacaciones_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NOT NULL,
    resolucion INT NOT NULL,
    fecha DATE NOT NULL,
    dias INT NOT NULL,
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes_vacaciones(id) ON DELETE CASCADE,
    INDEX idx_solicitud (solicitud_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: sesiones
-- Control de sesiones de usuario
-- =====================================================
CREATE TABLE IF NOT EXISTS sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    funcionario_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATETIME NOT NULL,
    activa TINYINT(1) DEFAULT 1,
    FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_funcionario (funcionario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;












