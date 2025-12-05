-- =====================================================
-- VERIFICAR Y CORREGIR CONFIGURACIÓN DE EMAIL
-- =====================================================

USE recursos_humanos;

-- Primero, ver qué hay actualmente
SELECT * FROM configuracion_emails;

-- Actualizar a Gmail (ejecuta esto después de verificar)
UPDATE configuracion_emails 
SET 
    smtp_host = 'smtp.gmail.com',
    smtp_port = 587,
    smtp_usuario = 'juanaparicioapa@gmail.com',
    smtp_password = 'xhkh yorr nwzj aogm',
    smtp_seguridad = 'tls',
    email_remitente = 'juanaparicioapa@gmail.com',
    nombre_remitente = 'Sistema RRHH',
    activo = 1
WHERE id = 1;

-- Verificar que se actualizó correctamente
SELECT 
    id, 
    smtp_host, 
    smtp_usuario, 
    email_remitente, 
    activo,
    fecha_actualizacion
FROM configuracion_emails 
WHERE id = 1;


