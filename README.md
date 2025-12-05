# Sistema de Gestión de Recursos Humanos

Sistema web completo para la gestión de formularios de Recursos Humanos, desarrollado en PHP con MySQL.

## 📋 Descripción

Este sistema permite a los funcionarios:
- Iniciar sesión con su cédula y contraseña
- Llenar formularios digitales (Jornada Extraordinaria, Permisos, Vacaciones, Misiones Oficiales, etc.)
- Ver sus formularios enviados
- Sistema de aprobaciones por niveles (Jefe Inmediato, Revisor, Jefe RRHH)
- Notificaciones por email

## 🚀 Tecnologías Utilizadas

- **PHP 7.4+**: Lenguaje del servidor
- **MySQL**: Base de datos
- **Bootstrap 5**: Framework CSS
- **PHPMailer**: Envío de emails
- **HTML5/CSS3/JavaScript**: Frontend

## 📁 Estructura del Proyecto

```
RECURSOS HUMANOS/
│
├── FORMULARIOS HTML ORIGINALES/     # Formularios HTML originales (diseño)
│   ├── FORMULARIO DE JORNADA EXTRAORDINARIA 2025.html
│   ├── FORMULARIO DE MISIÓN OFICIAL.html
│   ├── FORMULARIO DE REINCORPORACIÓN.html
│   ├── FORMULARIO DEL USO DE TIEMPO COMPENSATORIO.html
│   ├── SOLICITUD DE PERMISO 2025.html
│   └── SOLICITUD VACACIONES 2025.html
│
├── PDF ORIGINALES/                   # Formularios PDF originales
│
└── SISTEMA_RRHH/                     # Sistema completo funcionando
    ├── config/
    │   └── database.php              # Configuración de MySQL
    │
    ├── database/
    │   └── schema.sql                # Estructura de la base de datos
    │
    ├── includes/
    │   ├── session.php               # Manejo de sesiones
    │   ├── funciones.php             # Funciones auxiliares
    │   ├── email.php                 # Envío de emails
    │   └── aprobaciones.php          # Sistema de aprobaciones
    │
    ├── forms/                        # Formularios integrados
    │   ├── jornada_extraordinaria.php
    │   ├── mision_oficial.php
    │   ├── reincorporacion.php
    │   ├── tiempo_compensatorio.php
    │   ├── permiso.php
    │   └── vacaciones.php
    │
    ├── aprobaciones/
    │   └── revisar.php               # Interfaz de aprobación
    │
    ├── index.php                      # Página de login
    ├── registro.php                   # Registro de usuarios
    ├── dashboard.php                  # Menú principal
    ├── mis_formularios.php            # Ver formularios enviados
    └── README.md                       # Documentación
```

## ⚙️ Instalación

### Requisitos Previos

- Laragon (o XAMPP/WAMP) con PHP 7.4+
- MySQL 5.7+
- Navegador web moderno

### Pasos de Instalación

1. **Clonar o descargar el repositorio**
   ```bash
   git clone [URL_DEL_REPOSITORIO]
   ```

2. **Configurar la base de datos**
   - Abrir phpMyAdmin: `http://localhost/phpmyadmin`
   - Ejecutar el archivo `SISTEMA_RRHH/sistema_de_permisos/database/schema.sql`
   - Verificar que se hayan creado todas las tablas

3. **Configurar la conexión a la base de datos**
   - Editar `SISTEMA_RRHH/sistema_de_permisos/config/database.php`
   - Ajustar los valores según tu configuración:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'recursos_humanos');
     ```

4. **Configurar el envío de emails (opcional)**
   - Editar `SISTEMA_RRHH/sistema_de_permisos/includes/email.php`
   - Configurar SMTP según tu proveedor (Gmail, Outlook, etc.)
   - Ver documentación en `SISTEMA_RRHH/sistema_de_permisos/docs/`

5. **Acceder al sistema**
   - Abrir en el navegador: `http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/`
   - Registrarse como nuevo usuario
   - Iniciar sesión

## 📚 Documentación

La documentación completa está disponible en:
- `SISTEMA_RRHH/sistema_de_permisos/README.md` - Guía principal
- `SISTEMA_RRHH/sistema_de_permisos/READMEchat.txt` - Guía detallada
- `SISTEMA_RRHH/sistema_de_permisos/docs/` - Documentación adicional

## 🔐 Seguridad

- Contraseñas encriptadas con `password_hash()`
- Prevención de SQL Injection con `prepare()` y `bind_param()`
- Validación y limpieza de datos de entrada
- Sistema de sesiones con expiración automática

## 📝 Formularios Disponibles

1. **Jornada Extraordinaria**: Autorización para laborar en jornada extraordinaria
2. **Misión Oficial**: Solicitud de misión oficial
3. **Reincorporación**: Notificación de reincorporación
4. **Tiempo Compensatorio**: Solicitud de uso de tiempo compensatorio
5. **Permiso**: Solicitud de permiso personal
6. **Vacaciones**: Solicitud de vacaciones

## 🔄 Sistema de Aprobaciones

El sistema cuenta con un flujo de aprobación de 3 niveles:
1. **Jefe Inmediato**: Primera aprobación
2. **Revisor**: Segunda aprobación
3. **Jefe RRHH**: Aprobación final

Cada nivel recibe un email con un enlace único para aprobar o rechazar.

## 🛠️ Desarrollo

### Estructura de Base de Datos

Las tablas principales son:
- `funcionarios`: Datos de los funcionarios
- `jornadas_extraordinarias`: Solicitudes de jornada extraordinaria
- `solicitudes_permiso`: Solicitudes de permiso
- `solicitudes_vacaciones`: Solicitudes de vacaciones
- `aprobaciones`: Registro de aprobaciones
- Y más...

Ver `database/schema.sql` para la estructura completa.

## 📄 Licencia

Este proyecto es de uso interno.

## 👥 Autor

Sistema desarrollado para la gestión de Recursos Humanos.

## 📞 Soporte

Para problemas o preguntas, consultar la documentación en la carpeta `docs/`.

