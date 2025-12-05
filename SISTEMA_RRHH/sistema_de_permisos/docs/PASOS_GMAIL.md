# 📧 Configurar Gmail - Pasos Rápidos

## ¿Por qué Gmail?

Hotmail/Outlook ya no permite autenticación básica. Gmail es más fácil y funciona perfectamente.

## ⚡ Pasos Rápidos (5 minutos)

### Paso 1: Obtener Contraseña de Aplicación de Gmail

1. Ve a: https://myaccount.google.com/apppasswords
2. Si te pide verificación en 2 pasos:
   - Ve primero a: https://myaccount.google.com/security
   - Activa "Verificación en 2 pasos"
   - Luego vuelve a apppasswords
3. En "Contraseñas de aplicaciones":
   - **Aplicación**: Selecciona "Correo"
   - **Dispositivo**: Selecciona "Otro (nombre personalizado)"
   - Escribe: "Sistema RRHH"
   - Haz clic en **Generar**
4. **Copia la contraseña de 16 caracteres** (puede tener espacios, está bien)

### Paso 2: Actualizar Base de Datos

Ejecuta en phpMyAdmin (reemplaza los valores):

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

**Ejemplo:**
```sql
UPDATE configuracion_emails 
SET 
    smtp_host = 'smtp.gmail.com',
    smtp_port = 587,
    smtp_usuario = 'juan@gmail.com',
    smtp_password = 'abcd efgh ijkl mnop',
    smtp_seguridad = 'tls',
    email_remitente = 'juan@gmail.com',
    nombre_remitente = 'Sistema RRHH',
    activo = 1
WHERE id = 1;
```

### Paso 3: Probar

Abre:
```
http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/test_email.php
```

Deberías ver: **✅ Email enviado exitosamente!**

## ✅ Listo!

Ahora el sistema enviará emails usando Gmail.

## 📝 Nota

- La contraseña de aplicación es diferente a tu contraseña normal de Gmail
- Puedes tener múltiples contraseñas de aplicación (una por sistema)
- Si necesitas revocar una, vuelve a apppasswords y bórrala


