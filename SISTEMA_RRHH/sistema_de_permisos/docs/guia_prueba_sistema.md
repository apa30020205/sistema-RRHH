# Guía de Prueba del Sistema de Aprobaciones

## Pasos para Probar el Sistema

### 1. Verificar Instalación

Ejecuta en phpMyAdmin el archivo: `database/verificar_instalacion.sql`

O ejecuta estas consultas manualmente:

```sql
USE recursos_humanos;

-- Verificar tablas
SHOW TABLES LIKE 'aprobaciones';
SHOW TABLES LIKE 'configuracion_emails';

-- Verificar campos en funcionarios
DESCRIBE funcionarios;
```

**Resultado esperado:** Debes ver las tablas `aprobaciones` y `configuracion_emails`, y los campos nuevos en `funcionarios`.

---

### 2. Configurar Email

**IMPORTANTE:** Necesitas configurar el email antes de que funcione el sistema.

#### Opción A: Gmail

1. Ve a tu cuenta de Gmail
2. Activa "Contraseñas de aplicaciones": https://myaccount.google.com/apppasswords
3. Genera una contraseña de aplicación
4. Ejecuta en phpMyAdmin:

```sql
USE recursos_humanos;

INSERT INTO configuracion_emails 
(smtp_host, smtp_port, smtp_usuario, smtp_password, smtp_seguridad, email_remitente, nombre_remitente, activo) 
VALUES 
('smtp.gmail.com', 587, 'tu-email@gmail.com', 'tu-contraseña-de-aplicacion', 'tls', 'tu-email@gmail.com', 'Sistema RRHH', 1);
```

#### Opción B: Otro servidor SMTP

Ajusta los valores según tu proveedor de email.

---

### 3. Actualizar Funcionario con Emails

Necesitas actualizar al menos un funcionario con emails reales:

```sql
USE recursos_humanos;

-- Ver funcionarios disponibles
SELECT id, cedula, nombre_completo FROM funcionarios;

-- Actualizar un funcionario (cambia el ID y los emails)
UPDATE funcionarios 
SET 
    email = 'funcionario@ejemplo.com',
    email_jefe_inmediato = 'jefe@ejemplo.com',
    email_revisor = 'revisor@ejemplo.com',
    email_jefe_rrhh = 'jefe-rrhh@ejemplo.com'
WHERE id = 1;  -- Cambia el ID por el de un funcionario real
```

---

### 4. Probar el Sistema

#### A. Crear una Solicitud de Prueba

1. Inicia sesión en el sistema como funcionario
2. Ve al formulario de **Vacaciones** (ya está integrado)
3. Llena el formulario y guárdalo
4. **Resultado esperado:**
   - Mensaje: "¡Solicitud guardada exitosamente! Se ha enviado un email a su jefe inmediato."
   - Se debe crear un registro en la tabla `aprobaciones`

#### B. Verificar que se creó la aprobación

```sql
USE recursos_humanos;

-- Ver aprobaciones pendientes
SELECT * FROM aprobaciones 
WHERE accion IS NULL OR accion = ''
ORDER BY fecha_creacion DESC 
LIMIT 5;
```

#### C. Verificar Email Enviado

Revisa el email del jefe inmediato. Debe contener:
- Asunto: "Nueva Solicitud Pendiente de Aprobación - Vacaciones"
- Link para aprobar/rechazar

#### D. Probar Aprobación

1. Haz clic en el link del email (o copia la URL)
2. Debe abrir: `http://localhost/SISTEMA_RRHH/aprobaciones/revisar.php?token=XXXXX`
3. Verás los detalles de la solicitud
4. Escribe tu nombre
5. Opcionalmente agrega observaciones
6. Haz clic en "Aprobar" o "Rechazar"

#### E. Verificar Flujo Completo

```sql
USE recursos_humanos;

-- Ver historial de aprobaciones
SELECT 
    a.id,
    a.tipo_formulario,
    a.nivel_aprobacion,
    a.aprobado_por_nombre,
    a.accion,
    a.fecha_aprobacion,
    a.observaciones
FROM aprobaciones a
ORDER BY a.fecha_creacion DESC
LIMIT 10;

-- Ver estado de solicitudes
SELECT 
    id,
    funcionario_id,
    nivel_aprobacion_actual,
    aprobado_jefe_inmediato,
    aprobado_revisor,
    aprobado_jefe_rrhh,
    estado
FROM solicitudes_vacaciones
ORDER BY fecha_creacion DESC
LIMIT 5;
```

---

### 5. Verificar Notificaciones

Después de cada aprobación/rechazo:
- El funcionario debe recibir un email con el resultado
- Si se aprueba en nivel 1, el revisor debe recibir email
- Si se aprueba en nivel 2, el jefe RRHH debe recibir email
- Si se aprueba en nivel 3, el funcionario recibe email final

---

## Solución de Problemas

### No se envían emails

1. Verifica la configuración en `configuracion_emails`
2. Verifica que el servidor tenga acceso a SMTP
3. Revisa los logs de PHP para errores
4. Prueba con función `mail()` nativa si PHPMailer no funciona

### Error "Token inválido o expirado"

- Los tokens expiran en 7 días
- Cada token solo se puede usar una vez
- Verifica que la URL del email sea correcta

### No se crean aprobaciones

- Verifica que el formulario llame a `iniciarFlujoAprobacion()`
- Verifica que el funcionario tenga `email_jefe_inmediato` configurado
- Revisa los logs de PHP para errores

---

## Próximos Pasos

1. ✅ Verificar instalación
2. ✅ Configurar emails
3. ✅ Actualizar funcionarios con emails
4. ✅ Probar crear solicitud
5. ✅ Probar aprobar/rechazar
6. ⏭️ Integrar los otros 5 formularios (siguiendo el ejemplo de vacaciones)



