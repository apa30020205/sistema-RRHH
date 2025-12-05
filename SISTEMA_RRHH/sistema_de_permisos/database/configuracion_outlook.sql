-- =====================================================
-- CONFIGURACIÓN PARA OUTLOOK/EXCHANGE
-- =====================================================

USE recursos_humanos;

-- Configuración para Outlook/Office 365
-- IMPORTANTE: Reemplaza con los datos reales de tu servidor
INSERT INTO configuracion_emails 
(smtp_host, smtp_port, smtp_usuario, smtp_password, smtp_seguridad, email_remitente, nombre_remitente, activo) 
VALUES 
('smtp-mail.outlook.com', 587, 'sistema-rrhh@tudominio.com', 'tu-password', 'tls', 'sistema-rrhh@tudominio.com', 'Sistema RRHH', 1);

-- O si usas Exchange Server interno:
-- 'smtp.tudominio.com', 587, 'sistema-rrhh@tudominio.com', 'password', 'tls', ...

-- Verificar configuración
SELECT * FROM configuracion_emails;



