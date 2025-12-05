-- =====================================================
-- LIMPIAR TODAS LAS APROBACIONES Y EMPEZAR DE NUEVO
-- =====================================================

USE recursos_humanos;

-- ⚠️ ADVERTENCIA: Esto borrará TODAS las aprobaciones
-- Solo ejecuta esto si quieres empezar completamente de cero

-- Ver cuántas aprobaciones hay antes de borrar
SELECT COUNT(*) as total_aprobaciones FROM aprobaciones;

-- Borrar todas las aprobaciones
DELETE FROM aprobaciones;

-- Verificar que se borraron
SELECT COUNT(*) as aprobaciones_restantes FROM aprobaciones;

-- (Opcional) Si también quieres resetear los niveles de aprobación en los formularios
-- Descomenta las siguientes líneas si quieres resetear los formularios también:

-- UPDATE solicitudes_vacaciones SET nivel_aprobacion_actual = 1, aprobado_jefe_inmediato = 0, aprobado_revisor = 0, aprobado_jefe_rrhh = 0;
-- UPDATE solicitudes_permiso SET nivel_aprobacion_actual = 1, aprobado_jefe_inmediato = 0, aprobado_revisor = 0, aprobado_jefe_rrhh = 0;
-- UPDATE misiones_oficiales SET nivel_aprobacion_actual = 1, aprobado_jefe_inmediato = 0, aprobado_revisor = 0, aprobado_jefe_rrhh = 0;
-- UPDATE jornadas_extraordinarias SET nivel_aprobacion_actual = 1, aprobado_jefe_inmediato = 0, aprobado_revisor = 0, aprobado_jefe_rrhh = 0;
-- UPDATE tiempo_compensatorio SET nivel_aprobacion_actual = 1, aprobado_jefe_inmediato = 0, aprobado_revisor = 0, aprobado_jefe_rrhh = 0;
-- UPDATE reincorporaciones SET nivel_aprobacion_actual = 1, aprobado_jefe_inmediato = 0, aprobado_revisor = 0, aprobado_jefe_rrhh = 0;


