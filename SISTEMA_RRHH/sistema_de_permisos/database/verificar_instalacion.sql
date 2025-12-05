-- =====================================================
-- SCRIPT DE VERIFICACIÓN
-- Ejecuta esto para verificar que todo se instaló correctamente
-- =====================================================

USE recursos_humanos;

-- 1. Verificar tablas nuevas
SELECT 'Verificando tablas nuevas...' AS mensaje;
SHOW TABLES LIKE 'aprobaciones';
SHOW TABLES LIKE 'configuracion_emails';

-- 2. Verificar campos nuevos en funcionarios
SELECT 'Verificando campos en funcionarios...' AS mensaje;
DESCRIBE funcionarios;

-- 3. Verificar campos nuevos en solicitudes_vacaciones (ejemplo)
SELECT 'Verificando campos en solicitudes_vacaciones...' AS mensaje;
DESCRIBE solicitudes_vacaciones;

-- 4. Verificar que hay funcionarios registrados
SELECT 'Funcionarios registrados:' AS mensaje;
SELECT id, cedula, nombre_completo, email, email_jefe_inmediato, rol FROM funcionarios LIMIT 5;



