-- =====================================================
-- CAMBIAR CONFIGURACIÓN DE HOTMAIL A GMAIL
-- =====================================================

USE recursos_humanos;

-- IMPORTANTE: Reemplaza estos valores con tus datos reales de Gmail
UPDATE configuracion_emails 
SET 
    smtp_host = 'smtp.gmail.com',
    smtp_port = 587,
    smtp_usuario = 'tu-email@gmail.com',  -- ⚠️ CAMBIA ESTO
    smtp_password = 'TU-CONTRASEÑA-DE-APLICACION',  -- ⚠️ CAMBIA ESTO
    smtp_seguridad = 'tls',
    email_remitente = 'tu-email@gmail.com',  -- ⚠️ CAMBIA ESTO
    nombre_remitente = 'Sistema RRHH',
    activo = 1
WHERE id = 1;

-- Verificar
SELECT id, smtp_host, smtp_usuario, email_remitente, activo 
FROM configuracion_emails;


