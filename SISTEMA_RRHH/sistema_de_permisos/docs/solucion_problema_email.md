# 🔧 Solución al Problema de Emails - Guía Completa

## 📋 Resumen

Este documento contiene toda la información sobre la configuración de email del sistema, incluyendo la solución final implementada con Gmail.

---

## ✅ CONFIGURACIÓN ACTUAL (Gmail)

### Datos de Configuración

- **Email**: `juanaparicioapa@gmail.com`
- **Contraseña de Aplicación**: `xhkh yorr nwzj aogm`
- **SMTP Host**: `smtp.gmail.com`
- **SMTP Port**: `587`
- **SMTP Seguridad**: `tls`
- **Nombre Remitente**: `Sistema RRHH`

### Script SQL para Configurar

```sql
USE recursos_humanos;

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
```

---

## 🔍 Diagnóstico de Problemas

### Script de Prueba

Para diagnosticar problemas de email, ejecuta:

```
http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/test_email.php
```

Este script muestra:
- ✅ Si la configuración está correcta
- ❌ Si falta la contraseña
- ⚠️ Si PHPMailer no está instalado
- 📧 Resultado del envío de prueba

### Verificar Configuración en Base de Datos

```sql
USE recursos_humanos;
SELECT * FROM configuracion_emails;
```

**Verifica que:**
- ✅ `activo = 1`
- ✅ `smtp_password` NO esté vacío
- ✅ `smtp_usuario` sea correcto
- ✅ `smtp_host` sea `smtp.gmail.com` para Gmail

---

## 📧 Configuración de Gmail

### ¿Por qué Gmail?

Gmail es más fácil de configurar que Hotmail/Outlook porque:
- Permite usar contraseñas de aplicación sin OAuth2 complejo
- Funciona perfectamente con PHPMailer
- No requiere configuración adicional del servidor

### Obtener Contraseña de Aplicación

1. Ve a: https://myaccount.google.com/apppasswords
2. Si te pide verificación en 2 pasos:
   - Ve primero a: https://myaccount.google.com/security
   - Activa "Verificación en 2 pasos"
   - Luego vuelve a apppasswords
3. En "Contraseñas de aplicaciones":
   - **Aplicación**: Selecciona "Correo"
   - **App name**: Escribe "Sistema RRHH" (o cualquier nombre)
   - Haz clic en **Generar**
4. **Copia la contraseña de 16 caracteres** (puede tener espacios)

### Actualizar Base de Datos

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

---

## 🚨 Problemas Comunes y Soluciones

### Error: "No hay configuración de email activa"
**Solución:** Ejecuta el script SQL de configuración (ver arriba)

### Error: "PHPMailer no encontrado"
**Solución:** 
1. Abre: `http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/instalar_phpmailer.php`
2. O instala manualmente copiando la carpeta `src` de PHPMailer a `vendor/PHPMailer/src/`

### Error: "Authentication failed" o "Could not authenticate"
**Causas posibles:**
1. **Contraseña incorrecta**: Verifica que uses la contraseña de aplicación, NO la contraseña regular
2. **Hotmail/Outlook**: Ya no permite autenticación básica, requiere OAuth2 (usa Gmail en su lugar)

**Solución:**
- Para Gmail: Usa la contraseña de aplicación de 16 caracteres
- Verifica que la contraseña en la base de datos sea correcta

### Error: "Connection timeout"
**Solución:**
- Verifica tu conexión a internet
- Verifica que el puerto 587 no esté bloqueado por firewall

### Email no llega pero no hay error
**Solución:**
- Revisa la carpeta de SPAM
- Verifica que el email destinatario sea correcto
- Espera unos minutos (puede haber retraso)

---

## 📦 Instalación de PHPMailer

### Opción A: Script Automático

Abre en tu navegador:
```
http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/instalar_phpmailer.php
```

### Opción B: Manual

1. Descarga PHPMailer desde: https://github.com/PHPMailer/PHPMailer/releases
2. Extrae el ZIP
3. Copia la carpeta `src` a: `SISTEMA_RRHH/vendor/PHPMailer/src/`
4. El archivo `vendor/autoload.php` ya debe existir

### Estructura Correcta

```
SISTEMA_RRHH/vendor/
├── autoload.php
└── PHPMailer/
    └── src/
        ├── Exception.php
        ├── PHPMailer.php
        └── SMTP.php
```

---

## ✅ Verificación Final

### Paso 1: Verificar PHPMailer

Ejecuta `test_email.php` y verifica que diga:
- ✅ "PHPMailer está instalado y cargado correctamente"

### Paso 2: Verificar Configuración

Ejecuta `test_email.php` y verifica que diga:
- ✅ "Configuración encontrada"
- ✅ "Email enviado exitosamente!"

### Paso 3: Probar Sistema Completo

1. Inicia sesión en el sistema
2. Ve a "Solicitud de Vacaciones"
3. Llena el formulario y haz clic en "Enviar Solicitud"
4. Deberías ver: "¡Solicitud de vacaciones enviada exitosamente!"
5. Revisa tu bandeja de entrada (y spam) en `juanaparicioapa@gmail.com`

---

## 📝 Notas Importantes

### Contraseña de Aplicación vs Contraseña Regular

- ❌ **NO uses** tu contraseña regular de Gmail (`M(1967apa`)
- ✅ **USA** la contraseña de aplicación de 16 caracteres (`xhkh yorr nwzj aogm`)

### Hotmail/Outlook

Hotmail/Outlook ya no permite autenticación básica. Si necesitas usarlo, requiere OAuth2 (más complejo). Se recomienda usar Gmail.

### Seguridad

- La contraseña de aplicación es específica para este sistema
- Puedes tener múltiples contraseñas de aplicación (una por sistema)
- Si necesitas revocar una, vuelve a apppasswords y bórrala

---

## 🔗 Enlaces Útiles

- **Contraseñas de Aplicación Gmail**: https://myaccount.google.com/apppasswords
- **Seguridad de Google**: https://myaccount.google.com/security
- **PHPMailer GitHub**: https://github.com/PHPMailer/PHPMailer

---

## 📞 Si Aún No Funciona

1. Revisa los logs de error de PHP (en Laragon: `laragon/logs/php/`)
2. Ejecuta `test_email.php` y copia el mensaje de error completo
3. Verifica que Laragon esté corriendo
4. Verifica que PHP tenga habilitado `openssl` (necesario para TLS)

---

**Última actualización**: 13 de noviembre de 2025
**Configuración activa**: Gmail (juanaparicioapa@gmail.com)


