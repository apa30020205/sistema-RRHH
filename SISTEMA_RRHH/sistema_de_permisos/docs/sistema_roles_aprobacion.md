# Sistema de Roles y Aprobaciones

## Descripción General

El sistema implementa un flujo de aprobación de **3 niveles** para los 6 formularios del sistema de recursos humanos:

1. **Nivel 1: Jefe Inmediato** - Primera aprobación
2. **Nivel 2: Revisor** - Segunda revisión
3. **Nivel 3: Jefe Institucional de RRHH** - Aprobación final

## Flujo de Aprobación

```
Funcionario → Llena Formulario
    ↓
Email al Jefe Inmediato (Nivel 1)
    ↓
Jefe Inmediato → Aprueba/Rechaza
    ↓
Si Aprobado → Email al Revisor (Nivel 2)
    ↓
Revisor → Aprueba/Rechaza
    ↓
Si Aprobado → Email al Jefe RRHH (Nivel 3)
    ↓
Jefe RRHH → Aprueba/Rechaza (Decisión Final)
    ↓
Email al Funcionario con Resultado
```

## Estructura de Base de Datos

### Tablas Nuevas

1. **`aprobaciones`** - Rastrea cada paso del flujo de aprobación
2. **`configuracion_emails`** - Configuración SMTP para envío de emails

### Campos Agregados a `funcionarios`

- `email` - Email del funcionario
- `email_jefe_inmediato` - Email del jefe inmediato
- `email_revisor` - Email de la persona que revisa
- `email_jefe_rrhh` - Email del jefe institucional de RRHH
- `jefe_inmediato_id` - ID del funcionario que es su jefe
- `rol` - Rol del usuario (funcionario, jefe_inmediato, revisor, jefe_rrhh)

### Campos Agregados a Cada Tabla de Formulario

- `nivel_aprobacion_actual` - Nivel actual (1, 2, o 3)
- `aprobado_jefe_inmediato` - Boolean
- `nombre_jefe_inmediato` - Nombre del jefe que aprobó
- `fecha_aprobacion_jefe` - Fecha de aprobación del jefe
- `aprobado_revisor` - Boolean
- `nombre_revisor` - Nombre del revisor que aprobó
- `fecha_aprobacion_revisor` - Fecha de aprobación del revisor
- `aprobado_jefe_rrhh` - Boolean
- `nombre_jefe_rrhh` - Nombre del jefe RRHH que aprobó
- `fecha_aprobacion_jefe_rrhh` - Fecha de aprobación final
- `motivo_rechazo` - Motivo si fue rechazado

## Archivos Creados

### 1. `database/schema_roles_aprobacion.sql`
Script SQL para actualizar el esquema de base de datos con todas las tablas y campos necesarios.

**Ejecutar este script después de tener la base de datos inicial.**

### 2. `includes/email.php`
Sistema de envío de emails con:
- Soporte para PHPMailer (si está instalado) o función `mail()` nativa
- Plantillas HTML para emails
- Funciones para generar tokens de aprobación

### 3. `includes/aprobaciones.php`
Lógica completa del flujo de aprobación:
- `iniciarFlujoAprobacion()` - Inicia el flujo cuando se crea una solicitud
- `procesarAprobacion()` - Procesa aprobación/rechazo
- `avanzarANivel2()` - Avanza al nivel de revisor
- `avanzarANivel3()` - Avanza al nivel de jefe RRHH
- `finalizarAprobacion()` - Finaliza el flujo

### 4. `aprobaciones/revisar.php`
Interfaz web para que los aprobadores revisen y aprueben/rechacen solicitudes mediante el link del email.

## Integración en Formularios

Para integrar el sistema en un formulario, después de guardar la solicitud:

```php
require_once '../includes/aprobaciones.php';

// Después de guardar exitosamente
if ($stmt->execute()) {
    $solicitud_id = $stmt->insert_id;
    
    // Iniciar flujo de aprobación
    iniciarFlujoAprobacion($conn, 'vacaciones', $solicitud_id, $funcionario_id);
    
    $mensaje = '¡Solicitud guardada exitosamente! Se ha enviado un email a su jefe inmediato.';
    header('Location: vacaciones.php?guardado=1');
    exit();
}
```

## Configuración de Email

### Opción 1: Usar PHPMailer (Recomendado)

```bash
composer require phpmailer/phpmailer
```

### Opción 2: Usar función mail() nativa

Requiere configuración del servidor SMTP en `php.ini` o configuración del servidor.

### Configurar en Base de Datos

Insertar configuración en la tabla `configuracion_emails`:

```sql
INSERT INTO configuracion_emails 
(smtp_host, smtp_port, smtp_usuario, smtp_password, smtp_seguridad, email_remitente, nombre_remitente) 
VALUES 
('smtp.gmail.com', 587, 'tu-email@gmail.com', 'tu-password', 'tls', 'tu-email@gmail.com', 'Sistema RRHH');
```

## Uso del Sistema

### 1. Registrar Funcionarios con Emails

Al registrar un funcionario, incluir:
- Email del funcionario
- Email del jefe inmediato
- Email del revisor (opcional, puede ser global)
- Email del jefe RRHH (opcional, puede ser global)

### 2. Crear Solicitud

El funcionario llena el formulario normalmente. Al guardar:
- Se crea el registro en la base de datos
- Se inicia el flujo de aprobación
- Se envía email al jefe inmediato con link de aprobación

### 3. Aprobar/Rechazar

El aprobador:
1. Recibe email con link único
2. Hace clic en el link
3. Ve los detalles de la solicitud
4. Escribe su nombre
5. Opcionalmente agrega observaciones
6. Aprueba o rechaza

### 4. Notificaciones

- Si se aprueba en nivel 1 → Email al revisor (nivel 2)
- Si se aprueba en nivel 2 → Email al jefe RRHH (nivel 3)
- Si se aprueba en nivel 3 → Email al funcionario (aprobación final)
- Si se rechaza en cualquier nivel → Email al funcionario (rechazo)

## Seguridad

- Los tokens de aprobación expiran en 7 días
- Cada token es único y solo puede usarse una vez
- Los tokens se validan antes de procesar aprobaciones
- Los nombres de aprobadores se registran para auditoría

## Próximos Pasos

1. **Ejecutar script SQL**: `database/schema_roles_aprobacion.sql`
2. **Configurar emails**: Insertar configuración SMTP en `configuracion_emails`
3. **Actualizar formularios**: Integrar `iniciarFlujoAprobacion()` en los 6 formularios
4. **Probar flujo completo**: Crear solicitud de prueba y verificar emails
5. **Personalizar plantillas**: Ajustar diseño de emails según necesidades

## Notas Importantes

- El sistema es **100% digital**, no requiere impresión
- Todos los emails incluyen links directos para aprobar/rechazar
- El sistema rastrea todo el historial de aprobaciones
- Los funcionarios reciben notificaciones en cada paso



