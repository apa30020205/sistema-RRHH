-- =====================================================
-- ACTUALIZAR EMAILS DE FUNCIONARIOS A GMAIL
-- =====================================================

USE recursos_humanos;

-- Ver funcionarios actuales
SELECT id, nombre_completo, email, email_jefe_inmediato, email_revisor, email_jefe_rrhh 
FROM funcionarios;

-- Actualizar funcionario ID 1 (o el que corresponda) con Gmail
UPDATE funcionarios 
SET 
    email = 'juanaparicioapa@gmail.com',
    email_jefe_inmediato = 'juanaparicioapa@gmail.com',
    email_revisor = 'juanaparicioapa@gmail.com',
    email_jefe_rrhh = 'juanaparicioapa@gmail.com'
WHERE id = 1;

-- Verificar que se actualizó
SELECT id, nombre_completo, email, email_jefe_inmediato, email_revisor, email_jefe_rrhh 
FROM funcionarios 
WHERE id = 1;


