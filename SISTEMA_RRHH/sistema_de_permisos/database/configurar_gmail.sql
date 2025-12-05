-- =====================================================
-- CONFIGURACIÓN PARA GMAIL
-- =====================================================

USE recursos_humanos;

-- Actualizar configuración para Gmail
UPDATE configuracion_emails 
SET 
    smtp_host = 'smtp.gmail.com',
    smtp_port = 587,
    smtp_usuario = 'tu-email@gmail.com',
    smtp_password = 'TU-CONTRASEÑA-DE-APLICACION',
    smtp_seguridad = 'tls',
    email_remitente = 'tu-email@gmail.com',
    nombre_remitente = 'Sistema RRHH',
    activo = 1
WHERE id = 1;

-- Verificar
SELECT * FROM configuracion_emails;


