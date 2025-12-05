-- =====================================================
-- CONFIGURACIÓN GMAIL PARA JUAN
-- =====================================================

USE recursos_humanos;

-- Actualizar con datos de Gmail
UPDATE configuracion_emails 
SET 
    smtp_host = 'smtp.gmail.com',
    smtp_port = 587,
    smtp_usuario = 'juanaparicioapa@gmail.com',
    smtp_password = 'CONTRASEÑA-DE-APLICACION-AQUI',  -- ⚠️ NECESITAS OBTENER ESTA
    smtp_seguridad = 'tls',
    email_remitente = 'juanaparicioapa@gmail.com',
    nombre_remitente = 'Sistema RRHH',
    activo = 1
WHERE id = 1;

-- Verificar
SELECT id, smtp_host, smtp_usuario, email_remitente, activo 
FROM configuracion_emails;


