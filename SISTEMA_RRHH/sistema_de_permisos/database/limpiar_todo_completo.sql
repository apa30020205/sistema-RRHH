-- =====================================================
-- LIMPIAR TODO COMPLETAMENTE (APROBACIONES + FORMULARIOS)
-- =====================================================
-- ⚠️ ADVERTENCIA: Esto borrará TODAS las aprobaciones Y los formularios
-- Solo ejecuta esto si quieres empezar completamente de cero

USE recursos_humanos;

-- 1. Borrar todas las aprobaciones
DELETE FROM aprobaciones;
SELECT 'Aprobaciones borradas' as resultado;

-- 2. Borrar todos los formularios (opcional - descomenta si quieres)
-- DELETE FROM solicitudes_vacaciones;
-- DELETE FROM vacaciones_detalle;
-- DELETE FROM solicitudes_permiso;
-- DELETE FROM misiones_oficiales;
-- DELETE FROM jornadas_extraordinarias;
-- DELETE FROM jornadas_extraordinarias_horarios;
-- DELETE FROM tiempo_compensatorio;
-- DELETE FROM reincorporaciones;

-- 3. Resetear niveles de aprobación en formularios existentes (si no los borraste)
UPDATE solicitudes_vacaciones 
SET nivel_aprobacion_actual = 1, 
    aprobado_jefe_inmediato = 0, 
    aprobado_revisor = 0, 
    aprobado_jefe_rrhh = 0,
    fecha_aprobacion_jefe = NULL,
    fecha_aprobacion_revisor = NULL,
    fecha_aprobacion_jefe_rrhh = NULL;

UPDATE solicitudes_permiso 
SET nivel_aprobacion_actual = 1, 
    aprobado_jefe_inmediato = 0, 
    aprobado_revisor = 0, 
    aprobado_jefe_rrhh = 0,
    fecha_aprobacion_jefe = NULL,
    fecha_aprobacion_revisor = NULL,
    fecha_aprobacion_jefe_rrhh = NULL;

UPDATE misiones_oficiales 
SET nivel_aprobacion_actual = 1, 
    aprobado_jefe_inmediato = 0, 
    aprobado_revisor = 0, 
    aprobado_jefe_rrhh = 0,
    fecha_aprobacion_jefe = NULL,
    fecha_aprobacion_revisor = NULL,
    fecha_aprobacion_jefe_rrhh = NULL;

UPDATE jornadas_extraordinarias 
SET nivel_aprobacion_actual = 1, 
    aprobado_jefe_inmediato = 0, 
    aprobado_revisor = 0, 
    aprobado_jefe_rrhh = 0,
    fecha_aprobacion_jefe = NULL,
    fecha_aprobacion_revisor = NULL,
    fecha_aprobacion_jefe_rrhh = NULL;

UPDATE tiempo_compensatorio 
SET nivel_aprobacion_actual = 1, 
    aprobado_jefe_inmediato = 0, 
    aprobado_revisor = 0, 
    aprobado_jefe_rrhh = 0,
    fecha_aprobacion_jefe = NULL,
    fecha_aprobacion_revisor = NULL,
    fecha_aprobacion_jefe_rrhh = NULL;

UPDATE reincorporaciones 
SET nivel_aprobacion_actual = 1, 
    aprobado_jefe_inmediato = 0, 
    aprobado_revisor = 0, 
    aprobado_jefe_rrhh = 0,
    fecha_aprobacion_jefe = NULL,
    fecha_aprobacion_revisor = NULL,
    fecha_aprobacion_jefe_rrhh = NULL;

SELECT 'Niveles de aprobación reseteados' as resultado;

-- Verificar
SELECT 'Verificación:' as resultado;
SELECT COUNT(*) as aprobaciones_restantes FROM aprobaciones;
SELECT COUNT(*) as solicitudes_vacaciones FROM solicitudes_vacaciones;
SELECT COUNT(*) as solicitudes_permiso FROM solicitudes_permiso;


