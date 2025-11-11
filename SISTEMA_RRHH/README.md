# 🎓 GUÍA COMPLETA: Sistema de Gestión de Recursos Humanos

## 👋 Bienvenido

Esta guía está diseñada para personas que están retomando la programación. Te explicaré cada paso de forma clara y sencilla.

---

## 📋 PASO 1: Configurar la Base de Datos

### 1.1. Abrir phpMyAdmin en Laragon

1. Abre Laragon
2. Haz clic en el botón **"Database"** o abre tu navegador en: `http://localhost/phpmyadmin`
3. Usuario: `root` (contraseña vacía por defecto)

### 1.2. Crear la Base de Datos

1. En phpMyAdmin, ve a la pestaña **"SQL"**
2. Copia y pega el contenido del archivo `database/schema.sql`
3. Haz clic en **"Ejecutar"**

**✅ Verifica que se hayan creado todas las tablas:**
- funcionarios
- jornadas_extraordinarias
- jornadas_extraordinarias_horarios
- misiones_oficiales
- reincorporaciones
- tiempo_compensatorio
- solicitudes_permiso
- solicitudes_vacaciones
- vacaciones_detalle
- sesiones

---

## 📋 PASO 2: Configurar el Sistema

### 2.1. Verificar Configuración

Abre el archivo `config/database.php` y verifica:
- **DB_HOST**: `localhost` ✅
- **DB_USER**: `root` ✅
- **DB_PASS**: `''` (vacía) ✅
- **DB_NAME**: `recursos_humanos` ✅

### 2.2. Ubicación del Sistema

El sistema debe estar en:
```
C:\laragon\www\RECURSOS HUMANOS\SISTEMA_RRHH\
```

---

## 📋 PASO 3: Probar el Sistema

### 3.1. Acceder al Sistema

1. Abre Laragon y asegúrate que esté corriendo
2. Abre tu navegador y ve a:
   ```
   http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/
   ```
   O también puedes acceder por:
   ```
   http://localhost/RECURSOS HUMANOS/SISTEMA_RRHH/index.php
   ```

### 3.2. Registrarse

1. Haz clic en **"Regístrate aquí"**
2. Llena todos los campos del formulario
3. Crea una contraseña (entre 4 y 12 caracteres)
4. Haz clic en **"Registrarse"**

### 3.3. Iniciar Sesión

1. Ingresa tu **Cédula** y **Contraseña**
2. Haz clic en **"Iniciar Sesión"**
3. Serás redirigido al **Dashboard** (menú principal)

---

## 📋 PASO 4: Entender la Estructura

### Archivos Principales

```
SISTEMA_RRHH/
│
├── config/
│   └── database.php          ← Conexión a MySQL
│
├── includes/
│   ├── session.php           ← Manejo de sesiones
│   └── funciones.php         ← Funciones auxiliares
│
├── database/
│   └── schema.sql            ← Estructura de la base de datos
│
├── forms/                    ← Formularios (se crearán después)
│
├── index.php                 ← Página de LOGIN
├── registro.php              ← Página de REGISTRO
├── dashboard.php             ← MENÚ PRINCIPAL
└── logout.php                ← Cerrar sesión
```

---

## 📋 PASO 5: Cómo Funciona el Sistema

### 5.1. Flujo del Usuario

```
1. Registro → Guarda datos en tabla "funcionarios"
2. Login → Verifica cédula y contraseña
3. Sesión → Crea sesión PHP (cookie)
4. Dashboard → Muestra menú de formularios
5. Formulario → Llena y guarda en base de datos
```

### 5.2. Seguridad

- **Contraseñas**: Se encriptan con `password_hash()` de PHP
- **Sesiones**: Se usan sesiones PHP nativas
- **Validación**: Los datos se limpian antes de guardarse
- **SQL Injection**: Se previene con `prepare()` y `bind_param()`

---

## 🔧 CONCEPTOS IMPORTANTES

### ¿Qué es una Sesión?

Una sesión es como una "tarjeta de identificación" que el servidor te da cuando te logueas. Te permite estar "identificado" mientras navegas por el sistema.

### ¿Qué es prepare() y bind_param()?

Son funciones de MySQLi que previenen **inyección SQL** (hackers intentando insertar código malicioso).

**Ejemplo:**
```php
// ❌ MALO (vulnerable):
$query = "SELECT * FROM funcionarios WHERE cedula = '$cedula'";

// ✅ BUENO (seguro):
$stmt = $conn->prepare("SELECT * FROM funcionarios WHERE cedula = ?");
$stmt->bind_param("s", $cedula);
```

---

## 📝 PRÓXIMOS PASOS

1. ✅ Base de datos creada
2. ✅ Registro funcionando
3. ✅ Login funcionando
4. ✅ Dashboard funcionando
5. ⏳ Integrar formularios (siguiente paso)

---

## 🆘 SOLUCIÓN DE PROBLEMAS

### Error: "Error de conexión"
- Verifica que Laragon esté corriendo
- Verifica que MySQL esté activo en Laragon
- Verifica usuario y contraseña en `config/database.php`

### Error: "Table doesn't exist"
- Ejecuta el archivo `database/schema.sql` en phpMyAdmin

### Página en blanco
- Verifica que tengas errores en PHP: abre `php.ini` y pon `display_errors = On`
- Revisa los logs de Laragon

---

## 📚 RECURSOS PARA APRENDER

- **PHP Manual**: https://www.php.net/manual/es/
- **MySQLi**: https://www.php.net/manual/es/book.mysqli.php
- **Bootstrap**: https://getbootstrap.com/docs/5.3/

---

## 💡 TIPS PARA PROGRAMAR

1. **Lee el código**: Trata de entender qué hace cada línea
2. **Experimenta**: Cambia valores y ve qué pasa
3. **Comenta**: Usa `//` para explicar qué hace tu código
4. **Divide en partes**: Un problema grande se divide en pequeños
5. **Prueba constantemente**: No esperes a terminar todo para probar

---

**¡Éxito en tu aprendizaje! 🚀**












