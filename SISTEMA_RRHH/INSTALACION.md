# 🚀 GUÍA RÁPIDA DE INSTALACIÓN

## PASO A PASO (10 minutos)

### ✅ Paso 1: Crear la Base de Datos (5 minutos)

1. **Abrir phpMyAdmin**
   - Abre Laragon
   - Haz clic en **"Database"** o ve a: `http://localhost/phpmyadmin`
   - Usuario: `root` | Contraseña: *(dejar vacía)*

2. **Ejecutar el Script SQL**
   - En phpMyAdmin, clic en la pestaña **"SQL"** (arriba)
   - Abre el archivo `database/schema.sql` con el Bloc de Notas
   - **Copia TODO el contenido** del archivo
   - **Pega** en el cuadro SQL de phpMyAdmin
   - Haz clic en **"Ejecutar"** (botón azul abajo a la derecha)

3. **Verificar**
   - En el menú izquierdo de phpMyAdmin, deberías ver la base de datos `recursos_humanos`
   - Dentro deberías ver 10 tablas creadas

---

### ✅ Paso 2: Verificar Configuración (1 minuto)

Abre el archivo: `config/database.php`

Verifica que tenga esto:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Vacía
define('DB_NAME', 'recursos_humanos');
```

---

### ✅ Paso 3: Probar el Sistema (4 minutos)

1. **Abrir en el navegador:**
   ```
   http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/
   ```
   
   *Nota: Si no funciona, prueba:*
   ```
   http://localhost/RECURSOS HUMANOS/SISTEMA_RRHH/index.php
   ```

2. **Registrarse:**
   - Clic en "Regístrate aquí"
   - Llena todos los campos
   - Contraseña: entre 4 y 12 caracteres
   - Clic en "Registrarse"

3. **Iniciar Sesión:**
   - Ingresa tu cédula y contraseña
   - Clic en "Iniciar Sesión"
   - Deberías ver el Dashboard con 6 formularios

4. **Probar un Formulario:**
   - Clic en "Jornada Extraordinaria"
   - Verás que los datos del encabezado están pre-llenados
   - Llena el resto del formulario
   - Guarda

---

## ✅ VERIFICACIÓN FINAL

Si puedes:
- ✅ Ver la página de login
- ✅ Registrarte
- ✅ Iniciar sesión
- ✅ Ver el dashboard
- ✅ Ver un formulario con datos pre-llenados

**¡FELICIDADES! El sistema está funcionando correctamente.** 🎉

---

## 🐛 PROBLEMAS COMUNES

### "Error de conexión"
- Verifica que Laragon esté corriendo
- Verifica que MySQL esté activo (debería decir "MySQL ON" en Laragon)

### "Table doesn't exist"
- Vuelve al Paso 1 y ejecuta el SQL nuevamente

### Página en blanco
- Abre el menú de Laragon → Tools → View Logs
- Revisa si hay errores en los logs

---

## 📞 SIGUIENTE PASO

Una vez que tengas funcionando:
- Registro ✅
- Login ✅
- Dashboard ✅
- Formulario de Jornada Extraordinaria ✅

Puedo ayudarte a crear los otros 5 formularios siguiendo el mismo patrón.












