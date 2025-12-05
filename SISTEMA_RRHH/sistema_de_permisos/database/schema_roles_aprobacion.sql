-- =====================================================
-- ACTUALIZACIÓN: Sistema de Roles y Aprobaciones
-- Base de Datos: recursos_humanos
-- =====================================================

USE recursos_humanos;

-- =====================================================
-- ACTUALIZAR TABLA: funcionarios
-- Agregar campos de email y relaciones con roles
-- =====================================================
ALTER TABLE funcionarios 
ADD COLUMN IF NOT EXISTS email VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Email del funcionario',
ADD COLUMN IF NOT EXISTS email_jefe_inmediato VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Email del jefe inmediato',
ADD COLUMN IF NOT EXISTS email_revisor VARCHAR(255) DEFAULT NULL COMMENT 'Email de la persona que revisa',
ADD COLUMN IF NOT EXISTS email_jefe_rrhh VARCHAR(255) DEFAULT NULL COMMENT 'Email del jefe institucional de RRHH',
ADD COLUMN IF NOT EXISTS jefe_inmediato_id INT DEFAULT NULL COMMENT 'ID del funcionario que es su jefe inmediato',
ADD COLUMN IF NOT EXISTS rol VARCHAR(50) DEFAULT 'funcionario' COMMENT 'funcionario, jefe_inmediato, revisor, jefe_rrhh',
ADD INDEX idx_email (email),
ADD INDEX idx_rol (rol),
ADD INDEX idx_jefe_inmediato (jefe_inmediato_id);

-- =====================================================
-- TABLA: aprobaciones
-- Rastrea el flujo de aprobación de cada solicitud
-- =====================================================
CREATE TABLE IF NOT EXISTS aprobaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_formulario VARCHAR(50) NOT NULL COMMENT 'permiso, vacaciones, mision_oficial, jornada_extraordinaria, tiempo_compensatorio, reincorporacion',
    formulario_id INT NOT NULL COMMENT 'ID del formulario específico',
    nivel_aprobacion INT NOT NULL COMMENT '1=Jefe Inmediato, 2=Revisor, 3=Jefe RRHH',
    aprobado_por_id INT DEFAULT NULL COMMENT 'ID del funcionario que aprobó/rechazó',
    aprobado_por_nombre VARCHAR(255) NOT NULL COMMENT 'Nombre de quien aprobó/rechazó',
    accion VARCHAR(20) NOT NULL COMMENT 'aprobado, rechazado',
    observaciones TEXT COMMENT 'Observaciones del aprobador',
    fecha_aprobacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    token_aprobacion VARCHAR(255) UNIQUE COMMENT 'Token único para link de aprobación por email',
    fecha_expiracion_token DATETIME COMMENT 'Fecha de expiración del token',
    email_enviado TINYINT(1) DEFAULT 0 COMMENT '1=Email enviado, 0=No enviado',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo_formulario (tipo_formulario, formulario_id),
    INDEX idx_token (token_aprobacion),
    INDEX idx_nivel (nivel_aprobacion),
    INDEX idx_aprobado_por (aprobado_por_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: configuracion_emails
-- Configuración para envío de emails
-- =====================================================
CREATE TABLE IF NOT EXISTS configuracion_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    smtp_host VARCHAR(255) NOT NULL DEFAULT 'smtp.gmail.com',
    smtp_port INT NOT NULL DEFAULT 587,
    smtp_usuario VARCHAR(255) NOT NULL,
    smtp_password VARCHAR(255) NOT NULL,
    smtp_seguridad VARCHAR(10) DEFAULT 'tls' COMMENT 'tls, ssl',
    email_remitente VARCHAR(255) NOT NULL,
    nombre_remitente VARCHAR(255) NOT NULL DEFAULT 'Sistema RRHH',
    activo TINYINT(1) DEFAULT 1,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ACTUALIZAR TABLAS DE FORMULARIOS
-- Agregar campos para rastrear el flujo de aprobación
-- =====================================================

-- Solicitudes de Permiso
ALTER TABLE solicitudes_permiso
ADD COLUMN IF NOT EXISTS nivel_aprobacion_actual INT DEFAULT 1 COMMENT '1=Jefe Inmediato, 2=Revisor, 3=Jefe RRHH',
ADD COLUMN IF NOT EXISTS aprobado_jefe_inmediato TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_inmediato VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_revisor TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_revisor VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_revisor DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_jefe_rrhh TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_rrhh VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe_rrhh DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS motivo_rechazo TEXT DEFAULT NULL;

-- Solicitudes de Vacaciones
ALTER TABLE solicitudes_vacaciones
ADD COLUMN IF NOT EXISTS nivel_aprobacion_actual INT DEFAULT 1,
ADD COLUMN IF NOT EXISTS aprobado_jefe_inmediato TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_inmediato VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_revisor TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_revisor VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_revisor DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_jefe_rrhh TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_rrhh VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe_rrhh DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS motivo_rechazo TEXT DEFAULT NULL;

-- Misiones Oficiales
ALTER TABLE misiones_oficiales
ADD COLUMN IF NOT EXISTS nivel_aprobacion_actual INT DEFAULT 1,
ADD COLUMN IF NOT EXISTS aprobado_jefe_inmediato TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_inmediato VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_revisor TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_revisor VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_revisor DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_jefe_rrhh TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_rrhh VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe_rrhh DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS motivo_rechazo TEXT DEFAULT NULL;

-- Jornadas Extraordinarias
ALTER TABLE jornadas_extraordinarias
ADD COLUMN IF NOT EXISTS nivel_aprobacion_actual INT DEFAULT 1,
ADD COLUMN IF NOT EXISTS aprobado_jefe_inmediato TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_inmediato VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_revisor TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_revisor VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_revisor DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_jefe_rrhh TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_rrhh VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe_rrhh DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS motivo_rechazo TEXT DEFAULT NULL;

-- Tiempo Compensatorio
ALTER TABLE tiempo_compensatorio
ADD COLUMN IF NOT EXISTS nivel_aprobacion_actual INT DEFAULT 1,
ADD COLUMN IF NOT EXISTS aprobado_jefe_inmediato TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_inmediato VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_revisor TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_revisor VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_revisor DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_jefe_rrhh TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_rrhh VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe_rrhh DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS motivo_rechazo TEXT DEFAULT NULL;

-- Reincorporaciones
ALTER TABLE reincorporaciones
ADD COLUMN IF NOT EXISTS nivel_aprobacion_actual INT DEFAULT 1,
ADD COLUMN IF NOT EXISTS aprobado_jefe_inmediato TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_inmediato VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_revisor TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_revisor VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_revisor DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS aprobado_jefe_rrhh TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS nombre_jefe_rrhh VARCHAR(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS fecha_aprobacion_jefe_rrhh DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS motivo_rechazo TEXT DEFAULT NULL;



