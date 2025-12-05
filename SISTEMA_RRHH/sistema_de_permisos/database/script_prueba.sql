-- =====================================================
-- SCRIPT DE PRUEBA DEL SISTEMA DE APROBACIONES
-- Ejecuta esto paso a paso para probar el sistema
-- =====================================================

USE recursos_humanos;

-- =====================================================
-- PASO 1: Configurar Email (REEMPLAZA CON TUS DATOS)
-- =====================================================
-- IMPORTANTE: Cambia estos valores por tus datos reales
INSERT INTO configuracion_emails 
(smtp_host, smtp_port, smtp_usuario, smtp_password, smtp_seguridad, email_remitente, nombre_remitente, activo) 
VALUES 
('smtp.gmail.com', 587, 'tu-email@gmail.com', 'tu-password-app', 'tls', 'tu-email@gmail.com', 'Sistema RRHH', 1);

-- Verificar configuración
SELECT 'Configuración de email:' AS mensaje;
SELECT * FROM configuracion_emails;

-- =====================================================
-- PASO 2: Actualizar un funcionario con emails de prueba
-- =====================================================
-- IMPORTANTE: Cambia el ID por el de un funcionario real que tengas
-- Primero ve cuántos funcionarios tienes:
SELECT 'Funcionarios disponibles:' AS mensaje;
SELECT id, cedula, nombre_completo FROM funcionarios;

-- Luego actualiza uno (cambia el ID y los emails):
-- Ejemplo: Actualizar funcionario con ID = 1
UPDATE funcionarios 
SET 
    email = 'funcionario@ejemplo.com',
    email_jefe_inmediato = 'jefe@ejemplo.com',
    email_revisor = 'revisor@ejemplo.com',
    email_jefe_rrhh = 'jefe-rrhh@ejemplo.com'
WHERE id = 1;

-- Verificar actualización
SELECT 'Funcionario actualizado:' AS mensaje;
SELECT id, nombre_completo, email, email_jefe_inmediato, email_revisor, email_jefe_rrhh 
FROM funcionarios 
WHERE id = 1;



