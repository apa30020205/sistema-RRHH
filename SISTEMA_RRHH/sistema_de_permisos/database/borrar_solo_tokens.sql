-- =====================================================
-- BORRAR SOLO LOS TOKENS (TABLA aprobaciones)
-- =====================================================

USE recursos_humanos;

-- Borrar todos los registros de la tabla aprobaciones (solo tokens)
DELETE FROM aprobaciones;

-- Verificar que se borraron
SELECT COUNT(*) as tokens_restantes FROM aprobaciones;


