# 📧 Configuración de Email con Gmail

## ¿Por qué Gmail?

Gmail es más fácil de configurar que Hotmail/Outlook porque permite usar contraseñas de aplicación sin requerir OAuth2 complejo.

## Pasos para Configurar Gmail

### Paso 1: Obtener Contraseña de Aplicación

1. Ve a tu cuenta de Google: https://myaccount.google.com/
2. Ve a **Seguridad** → **Verificación en 2 pasos**
3. Si no está activada, actívala primero
4. Luego ve a **Contraseñas de aplicaciones**: https://myaccount.google.com/apppasswords
5. Selecciona:
   - **Aplicación**: Correo
   - **Dispositivo**: Otro (nombre personalizado) → "Sistema RRHH"
6. Haz clic en **Generar**
7. Copia la contraseña de 16 caracteres (puede tener espacios, está bien)

### Paso 2: Actualizar Base de Datos

Ejecuta en phpMyAdmin:

```sql
USE recursos_humanos;

UPDATE configuracion_emails 
SET 
    smtp_host = 'smtp.gmail.com',
    smtp_port = 587,
    smtp_usuario = 'tu-email@gmail.com',
    smtp_password = 'LA-CONTRASEÑA-DE-16-CARACTERES',
    smtp_seguridad = 'tls',
    email_remitente = 'tu-email@gmail.com',
    nombre_remitente = 'Sistema RRHH',
    activo = 1
WHERE id = 1;
```

**Reemplaza:**
- `tu-email@gmail.com` → Tu email de Gmail
- `LA-CONTRASEÑA-DE-16-CARACTERES` → La contraseña de aplicación que copiaste

### Paso 3: Probar

Ejecuta:
```
http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/test_email.php
```

Deberías ver: "✅ Email enviado exitosamente!"

## Alternativa: Hotmail con OAuth2

Si necesitas usar Hotmail, necesitarás configurar OAuth2, que es más complejo. Contacta si necesitas ayuda con esto.


