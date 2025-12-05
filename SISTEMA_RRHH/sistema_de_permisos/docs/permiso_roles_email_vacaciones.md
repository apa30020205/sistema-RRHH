# 📋 Resumen Completo: Sistema de Permisos, Roles, Email y Vacaciones

**Fecha:** 13 de noviembre de 2025  
**Sesión:** Implementación del sistema de aprobación de 3 niveles con notificaciones por email

---

## 📌 ÍNDICE

1. [Cambios Iniciales en Formulario de Vacaciones](#cambios-iniciales)
2. [Sistema de Roles y Aprobación de 3 Niveles](#sistema-roles)
3. [Configuración de Email (Gmail)](#configuracion-email)
4. [Correcciones y Mejoras](#correcciones)
5. [Página de Confirmación](#confirmacion)
6. [Mejoras en Emails](#mejoras-emails)

---

## 1. Cambios Iniciales en Formulario de Vacaciones {#cambios-iniciales}

### Cambio de Etiqueta y Placeholder
- **Antes:** "V° B° Director del Área:"
- **Después:** "Revisado por:"
- **Estilo:** Campo con fondo rojo claro (`bg-red-50`) y texto gris (`text-gray-600`)

### Cambio de Texto en Botones y Mensajes
- **Botones:** "Guardar Solicitud" → "Enviar Solicitud"
- **Mensajes:** "guardada exitosamente" → "enviada exitosamente"
- **Ícono:** `fa-save` → `fa-paper-plane`

**Archivos modificados:**
- `forms/vacaciones.php`
- `forms/permiso.php`
- `forms/mision_oficial.php`
- `forms/jornada_extraordinaria.php`
- `forms/tiempo_compensatorio.php`
- `forms/reincorporacion.php`

---

## 2. Sistema de Roles y Aprobación de 3 Niveles {#sistema-roles}

### Flujo de Aprobación

```
Funcionario → Nivel 1 (Jefe Inmediato) → Nivel 2 (Revisor) → Nivel 3 (Jefe RRHH) → Funcionario
```

### Niveles de Aprobación

1. **Nivel 1 - Jefe Inmediato**
   - Cargo: "Jefe inmediato"
   - Email: `email_jefe_inmediato`
   - Acción: Aprobar/Rechazar

2. **Nivel 2 - Revisor**
   - Cargo: "Revisado por"
   - Email: `email_revisor`
   - Acción: Revisar, actualizar si es necesario, Aprobar/Rechazar

3. **Nivel 3 - Jefe RRHH**
   - Cargo: "Jefe Institucional de Recursos Humanos"
   - Email: `email_jefe_rrhh`
   - Acción: Aprobación final, Aprobar/Rechazar

### Base de Datos

#### Tabla: `aprobaciones`
```sql
CREATE TABLE IF NOT EXISTS aprobaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_formulario VARCHAR(50) NOT NULL,
    formulario_id INT NOT NULL,
    nivel_aprobacion INT NOT NULL COMMENT '1=Jefe Inmediato, 2=Revisor, 3=Jefe RRHH',
    aprobado_por_id INT DEFAULT NULL,
    aprobado_por_nombre VARCHAR(255) DEFAULT NULL,
    accion VARCHAR(20) DEFAULT NULL COMMENT 'aprobado, rechazado',
    observaciones TEXT,
    fecha_aprobacion DATETIME DEFAULT NULL,
    token_aprobacion VARCHAR(255) UNIQUE,
    fecha_expiracion_token DATETIME,
    email_enviado TINYINT(1) DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

#### Tabla: `configuracion_emails`
```sql
CREATE TABLE IF NOT EXISTS configuracion_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    smtp_host VARCHAR(255) NOT NULL DEFAULT 'smtp.gmail.com',
    smtp_port INT NOT NULL DEFAULT 587,
    smtp_usuario VARCHAR(255) NOT NULL,
    smtp_password VARCHAR(255) NOT NULL,
    smtp_seguridad VARCHAR(10) DEFAULT 'tls',
    email_remitente VARCHAR(255) NOT NULL,
    nombre_remitente VARCHAR(255) NOT NULL DEFAULT 'Sistema RRHH',
    activo TINYINT(1) DEFAULT 1
);
```

#### Campos agregados a `funcionarios`
- `email` - Email del funcionario
- `email_jefe_inmediato` - Email del jefe inmediato
- `email_revisor` - Email del revisor
- `email_jefe_rrhh` - Email del jefe RRHH
- `jefe_inmediato_id` - ID del jefe inmediato
- `rol` - Rol del funcionario

#### Campos agregados a tablas de formularios
- `nivel_aprobacion_actual` - Nivel actual (1, 2, o 3)
- `aprobado_jefe_inmediato` - Boolean
- `aprobado_revisor` - Boolean
- `aprobado_jefe_rrhh` - Boolean
- `fecha_aprobacion_jefe` - DateTime
- `fecha_aprobacion_revisor` - DateTime
- `fecha_aprobacion_jefe_rrhh` - DateTime

### Archivos Creados

1. **`database/schema_roles_aprobacion_compatible.sql`**
   - Script SQL para crear tablas y agregar columnas
   - Compatible con phpMyAdmin (sin `IF NOT EXISTS` en `ALTER TABLE`)

2. **`includes/aprobaciones.php`**
   - Funciones para manejar el flujo de aprobación
   - `iniciarFlujoAprobacion()` - Inicia el proceso
   - `procesarAprobacion()` - Procesa aprobación/rechazo
   - `avanzarANivel2()` - Avanza al nivel 2
   - `avanzarANivel3()` - Avanza al nivel 3
   - `finalizarAprobacion()` - Finaliza el proceso

3. **`includes/email.php`**
   - Funciones para enviar emails
   - `enviarEmail()` - Envía email usando PHPMailer o mail() nativa
   - `crearEmailSolicitudPendiente()` - Crea email de solicitud pendiente
   - `crearEmailNotificacionFuncionario()` - Crea email de notificación final
   - `obtenerBaseURL()` - Obtiene URL base del sistema

4. **`aprobaciones/revisar.php`**
   - Interfaz web para aprobar/rechazar solicitudes
   - Accesible mediante token único en el email
   - Muestra detalles de la solicitud
   - Permite aprobar/rechazar con nombre y observaciones

### Integración en Formularios

**Ejemplo en `forms/vacaciones.php`:**
```php
require_once '../includes/aprobaciones.php';

// Después de guardar la solicitud
if ($stmt->execute()) {
    $solicitud_id = $stmt->insert_id;
    iniciarFlujoAprobacion($conn, 'vacaciones', $solicitud_id, $funcionario_id);
    header('Location: confirmacion.php?tipo=vacaciones&id=' . $solicitud_id);
    exit();
}
```

---

## 3. Configuración de Email (Gmail) {#configuracion-email}

### Configuración Final

- **Email:** `juanaparicioapa@gmail.com`
- **Contraseña de Aplicación:** `xhkh yorr nwzj aogm`
- **SMTP Host:** `smtp.gmail.com`
- **SMTP Port:** `587`
- **SMTP Seguridad:** `tls`

### Script SQL de Configuración

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

### Instalación de PHPMailer

1. **Script automático:** `instalar_phpmailer.php`
2. **Estructura final:**
   ```
   vendor/
   ├── autoload.php
   └── PHPMailer/
       └── src/
           ├── Exception.php
           ├── PHPMailer.php
           └── SMTP.php
   ```

### Problemas Resueltos

1. **Hotmail/Outlook no funciona:** Requiere OAuth2, se cambió a Gmail
2. **PHPMailer no instalado:** Se instaló manualmente
3. **Tokens truncados:** Se corrigió usando `CAST(? AS CHAR(255))` en SQL
4. **URLs incorrectas:** Se mejoró la función `obtenerBaseURL()`

---

## 4. Correcciones y Mejoras {#correcciones}

### Corrección de Tokens

**Problema:** Los tokens se guardaban como números en lugar de strings completos (64 caracteres).

**Solución:**
```php
// Forzar token como string
$token = (string)$token;

// Usar CAST en SQL
$stmt = $conn->prepare("INSERT INTO aprobaciones 
    (tipo_formulario, formulario_id, nivel_aprobacion, token_aprobacion, fecha_expiracion_token) 
    VALUES (?, ?, 1, CAST(? AS CHAR(255)), ?)");
```

### Corrección de Campos NULL

**Problema:** `aprobado_por_nombre` y `accion` eran `NOT NULL` pero no tenían valores al crear el registro.

**Solución:**
```sql
ALTER TABLE aprobaciones 
MODIFY COLUMN aprobado_por_nombre VARCHAR(255) DEFAULT NULL;

ALTER TABLE aprobaciones 
MODIFY COLUMN accion VARCHAR(20) DEFAULT NULL;
```

### Scripts de Diagnóstico Creados

1. **`test_email.php`** - Prueba envío de emails
2. **`test_token.php`** - Diagnóstico de tokens
3. **`test_url.php`** - Prueba generación de URLs
4. **`instalar_phpmailer.php`** - Instalador de PHPMailer

---

## 5. Página de Confirmación {#confirmacion}

### Archivo: `forms/confirmacion.php`

**Características:**
- Muestra confirmación después de enviar solicitud
- Información mostrada:
  - "Solicitud Recibida"
  - Estado: "Pendiente de aprobación"
  - Aprobado por: (nombre del funcionario)
  - Fecha: (fecha actual)
- Botón para volver al Dashboard
- Redirección automática después de 10 segundos

**Integración:**
```php
// En vacaciones.php, después de guardar:
header('Location: confirmacion.php?tipo=vacaciones&id=' . $solicitud_id);
exit();
```

---

## 6. Mejoras en Emails {#mejoras-emails}

### Email de Solicitud Pendiente

**Mejoras:**
- Muestra el cargo según el nivel:
  - Nivel 1: "Jefe inmediato"
  - Nivel 2: "Revisado por"
  - Nivel 3: "Jefe Institucional de Recursos Humanos"
- Caja destacada con el cargo
- Link de aprobación con token único

### Email de Notificación Final

**Mejoras:**
- Saludo personalizado con nombre del funcionario
- Eliminado: "Puede revisar el estado de su solicitud ingresando al sistema"
- Muestra estado (APROBADA/RECHAZADA)
- Muestra nombre del aprobador
- Muestra observaciones si las hay

**Ejemplo:**
```
✓ Su Solicitud ha sido APROBADA

Estimado/a [Nombre del Funcionario],

Su Solicitud de Vacaciones ha sido APROBADA por [Nombre del Aprobador].

[Observaciones si las hay]
```

---

## 📁 Archivos Modificados/Creados

### Archivos Nuevos
- `database/schema_roles_aprobacion_compatible.sql`
- `database/fix_aprobaciones_null.sql`
- `database/verificar_instalacion.sql`
- `database/script_prueba.sql`
- `database/configuracion_outlook.sql`
- `database/cambiar_a_gmail.sql`
- `database/configurar_gmail_juan.sql`
- `database/actualizar_emails_funcionarios.sql`
- `database/borrar_solo_tokens.sql`
- `database/limpiar_aprobaciones.sql`
- `database/limpiar_todo_completo.sql`
- `database/verificar_tabla_aprobaciones.sql`
- `database/verificar_y_corregir_email.sql`
- `includes/aprobaciones.php`
- `includes/email.php`
- `aprobaciones/revisar.php`
- `forms/confirmacion.php`
- `test_email.php`
- `test_token.php`
- `test_url.php`
- `instalar_phpmailer.php`
- `docs/sistema_roles_aprobacion.md`
- `docs/configuracion_outlook.md`
- `docs/configuracion_gmail.md`
- `docs/PASOS_GMAIL.md`
- `docs/guia_prueba_sistema.md`
- `docs/solucion_problema_email.md`
- `docs/RESUMEN_SISTEMA_ROLES_APROBACION.md`

### Archivos Modificados
- `forms/vacaciones.php` - Integración de aprobación + confirmación
- `forms/permiso.php` - Cambio de textos
- `forms/mision_oficial.php` - Cambio de textos
- `forms/jornada_extraordinaria.php` - Cambio de textos
- `forms/tiempo_compensatorio.php` - Cambio de textos
- `forms/reincorporacion.php` - Cambio de textos

---

## 🔧 Configuración de Email para Pruebas

### Email de Prueba Configurado

```sql
USE recursos_humanos;

-- Configuración de email
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

-- Funcionario de prueba (todos los emails iguales para pruebas)
UPDATE funcionarios 
SET 
    email = 'juanaparicioapa@gmail.com',
    email_jefe_inmediato = 'juanaparicioapa@gmail.com',
    email_revisor = 'juanaparicioapa@gmail.com',
    email_jefe_rrhh = 'juanaparicioapa@gmail.com'
WHERE id = 1;
```

---

## ✅ Estado Final del Sistema

### Funcionalidades Implementadas

1. ✅ Sistema de aprobación de 3 niveles
2. ✅ Notificaciones por email en cada nivel
3. ✅ Tokens únicos para aprobación por email
4. ✅ Página de confirmación después de enviar solicitud
5. ✅ Emails personalizados con cargo según nivel
6. ✅ Emails finales con nombre del funcionario
7. ✅ Integración en formulario de vacaciones
8. ✅ PHPMailer instalado y configurado
9. ✅ Gmail configurado y funcionando

### Pendiente

1. ⏳ Integrar sistema de aprobación en los otros 5 formularios
2. ⏳ Configurar emails reales de funcionarios, jefes, revisores y jefe RRHH
3. ⏳ Módulo de datos biométricos (siguiente tarea)

---

## 📝 Notas Importantes

### Tokens
- Los tokens deben tener 64 caracteres
- Se guardan como VARCHAR(255) con CAST explícito
- Expiran después de 7 días

### Emails
- Se usa Gmail con contraseña de aplicación
- PHPMailer es necesario para entornos locales
- Los emails muestran el cargo según el nivel de aprobación

### Seguridad
- Los tokens son únicos y seguros
- Los links expiran después de 7 días
- Se valida que el token no haya sido usado antes

---

## 🔗 Enlaces Útiles

- **Test Email:** `http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/test_email.php`
- **Test Token:** `http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/test_token.php`
- **Instalar PHPMailer:** `http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/instalar_phpmailer.php`
- **Contraseñas de Aplicación Gmail:** https://myaccount.google.com/apppasswords

---

**Última actualización:** 13 de noviembre de 2025  
**Estado:** Sistema de aprobación funcionando correctamente ✅


