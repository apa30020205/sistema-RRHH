-- =====================================================
-- CORRECCIÓN: Permitir NULL en campos de aprobación
-- =====================================================

USE recursos_humanos;

-- Permitir NULL en aprobado_por_nombre (cuando se crea el registro aún no hay aprobador)
ALTER TABLE aprobaciones 
MODIFY COLUMN aprobado_por_nombre VARCHAR(255) DEFAULT NULL COMMENT 'Nombre de quien aprobó/rechazó';

-- Permitir NULL en accion (cuando se crea el registro aún no hay acción)
ALTER TABLE aprobaciones 
MODIFY COLUMN accion VARCHAR(20) DEFAULT NULL COMMENT 'aprobado, rechazado';

-- Verificar cambios
DESCRIBE aprobaciones;


