# Configuración para Outlook/Exchange

## Configuración SMTP para Outlook

### Opción 1: Outlook.com / Office 365

```sql
USE recursos_humanos;

INSERT INTO configuracion_emails 
(smtp_host, smtp_port, smtp_usuario, smtp_password, smtp_seguridad, email_remitente, nombre_remitente, activo) 
VALUES 
('smtp-mail.outlook.com', 587, 'sistema-rrhh@tudominio.com', 'tu-password', 'tls', 'sistema-rrhh@tudominio.com', 'Sistema RRHH', 1);
```

**Parámetros:**
- `smtp_host`: `'smtp-mail.outlook.com'` (para Outlook.com/Office 365)
- `smtp_port`: `587` (TLS) o `465` (SSL)
- `smtp_usuario`: Email completo del sistema (ej: `sistema-rrhh@tudominio.com`)
- `smtp_password`: Contraseña del email del sistema
- `smtp_seguridad`: `'tls'` (puerto 587) o `'ssl'` (puerto 465)
- `email_remitente`: Mismo email que `smtp_usuario`
- `nombre_remitente`: `'Sistema RRHH'` o el nombre que prefieras

### Opción 2: Exchange Server Interno

Si tu oficina tiene un servidor Exchange interno:

```sql
USE recursos_humanos;

INSERT INTO configuracion_emails 
(smtp_host, smtp_port, smtp_usuario, smtp_password, smtp_seguridad, email_remitente, nombre_remitente, activo) 
VALUES 
('smtp.tudominio.com', 587, 'sistema-rrhh@tudominio.com', 'tu-password', 'tls', 'sistema-rrhh@tudominio.com', 'Sistema RRHH', 1);
```

**Parámetros:**
- `smtp_host`: Dirección del servidor Exchange (ej: `smtp.empresa.com` o `mail.empresa.com`)
- `smtp_port`: Generalmente `587` o `25`
- `smtp_usuario`: Email completo con dominio (ej: `sistema-rrhh@empresa.com`)
- `smtp_password`: Contraseña del email
- `smtp_seguridad`: `'tls'` o `'ssl'` según configuración del servidor

## Obtener Información del Servidor

Si no conoces los datos del servidor SMTP, puedes:

1. **Preguntar al administrador de TI** de tu oficina
2. **Revisar configuración en Outlook:**
   - Abre Outlook
   - Archivo → Configuración de cuenta → Configuración de cuenta
   - Selecciona tu cuenta → Cambiar
   - Ve a "Más configuraciones" → Pestaña "Servidor saliente"
   - Ahí verás: Servidor SMTP y Puerto

## Usar Emails Reales de Funcionarios

Como cada funcionario y jefe ya tiene su email asignado en Outlook, puedes:

### 1. Actualizar Funcionarios con sus Emails Reales

```sql
USE recursos_humanos;

-- Ver funcionarios actuales
SELECT id, cedula, nombre_completo FROM funcionarios;

-- Actualizar con emails reales de Outlook
UPDATE funcionarios 
SET 
    email = 'funcionario@tudominio.com',
    email_jefe_inmediato = 'jefe@tudominio.com',
    email_revisor = 'revisor@tudominio.com',
    email_jefe_rrhh = 'jefe-rrhh@tudominio.com'
WHERE id = 1;
```

### 2. Importar Emails desde Lista de Outlook

Si tienes una lista de todos los funcionarios con sus emails, puedes actualizarlos en lote:

```sql
-- Ejemplo: Actualizar múltiples funcionarios
UPDATE funcionarios 
SET email = 'juan.perez@tudominio.com',
    email_jefe_inmediato = 'maria.garcia@tudominio.com'
WHERE cedula = '123456789';

UPDATE funcionarios 
SET email = 'pedro.rodriguez@tudominio.com',
    email_jefe_inmediato = 'ana.lopez@tudominio.com'
WHERE cedula = '987654321';
```

## Configuración Recomendada

### Email del Sistema (Remitente)

Es recomendable crear un email específico para el sistema:
- `sistema-rrhh@tudominio.com`
- `noreply-rrhh@tudominio.com`
- `rrhh-automatico@tudominio.com`

Este email será el que aparezca como remitente en todos los emails del sistema.

### Emails de Funcionarios

Cada funcionario debe tener:
- Su email personal: `funcionario@tudominio.com`
- Email de su jefe inmediato: `jefe@tudominio.com`
- Email del revisor (puede ser el mismo para todos): `revisor@tudominio.com`
- Email del jefe RRHH: `jefe-rrhh@tudominio.com`

## Prueba de Configuración

Después de configurar, prueba:

```sql
-- Verificar configuración
SELECT * FROM configuracion_emails;

-- Verificar funcionarios con emails
SELECT id, nombre_completo, email, email_jefe_inmediato, email_revisor, email_jefe_rrhh 
FROM funcionarios 
WHERE email != '';
```

## Notas Importantes

1. **Autenticación:** Algunos servidores Exchange requieren autenticación específica. Si tienes problemas, consulta con tu administrador de TI.

2. **Firewall:** Asegúrate de que el servidor tenga acceso al puerto SMTP (587 o 25).

3. **Credenciales:** El email del sistema debe tener permisos para enviar emails.

4. **Dominio:** Todos los emails deben usar el mismo dominio de tu organización.



