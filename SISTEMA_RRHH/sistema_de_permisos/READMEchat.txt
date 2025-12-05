================================================================================
SISTEMA DE GESTIÓN DE RECURSOS HUMANOS - GUÍA COMPLETA PARA PRINCIPIANTES
================================================================================

Fecha: 2025
Autor: Sistema de Recursos Humanos
Nivel: Principiante (30 años sin programar)

================================================================================
ÍNDICE
================================================================================

1. ¿QUÉ ES ESTE SISTEMA?
2. ESTRUCTURA DEL PROYECTO
3. CONFIGURACIÓN INICIAL (MySQL/Laragon)
4. CÓMO FUNCIONA EL SISTEMA
5. ARCHIVOS IMPORTANTES EXPLICADOS
6. INTEGRACIÓN DE FORMULARIOS
7. RESOLUCIÓN DE PROBLEMAS
8. PRÓXIMOS PASOS PARA APRENDER

================================================================================
1. ¿QUÉ ES ESTE SISTEMA?
================================================================================

Este es un sistema web para gestionar formularios de Recursos Humanos. Los 
funcionarios pueden:
- Iniciar sesión con su cédula y contraseña
- Llenar formularios digitales (Jornada Extraordinaria, Permisos, Vacaciones, etc.)
- Ver sus formularios enviados
- Todo se guarda en una base de datos MySQL

TECNOLOGÍAS USADAS:
- PHP: Lenguaje del servidor (el "cerebro" que procesa todo)
- MySQL: Base de datos (donde se guarda la información)
- HTML/CSS: La apariencia visual (lo que ves en el navegador)
- JavaScript: Hace que los formularios sean interactivos

================================================================================
2. ESTRUCTURA DEL PROYECTO
================================================================================

RECURSOS HUMANOS/
│
├── FORMULARIOS HTML ORIGINALES/     ← Formularios HTML originales (solo diseño)
│   ├── FORMULARIO DE JORNADA EXTRAORDINARIA 2025.html
│   ├── FORMULARIO DE MISIÓN OFICIAL.html
│   ├── FORMULARIO DE REINCORPORACIÓN.html
│   ├── FORMULARIO DEL USO DE TIEMPO COMPENSATORIO.html
│   ├── SOLICITUD DE PERMISO 2025.html
│   └── SOLICITUD VACACIONES 2025.html
│
└── SISTEMA_RRHH/                    ← Sistema completo funcionando
    ├── config/
    │   └── database.php             ← Configuración de MySQL
    │
    ├── database/
    │   └── schema.sql               ← Estructura de la base de datos
    │
    ├── includes/
    │   ├── session.php              ← Manejo de sesiones (login/logout)
    │   └── funciones.php            ← Funciones auxiliares
    │
    ├── forms/                        ← Formularios integrados con PHP
    │   ├── jornada_extraordinaria.php
    │   ├── mision_oficial.php       ← FALTA CREAR
    │   ├── reincorporacion.php      ← FALTA CREAR
    │   ├── tiempo_compensatorio.php ← FALTA CREAR
    │   ├── permiso.php              ← FALTA CREAR
    │   └── vacaciones.php            ← FALTA CREAR
    │
    ├── index.php                     ← Página de login
    ├── registro.php                  ← Registro de nuevos usuarios
    ├── dashboard.php                 ← Menú principal después del login
    ├── mis_formularios.php           ← Ver formularios enviados
    ├── logout.php                    ← Cerrar sesión
    └── READMEchat.txt                ← ESTE ARCHIVO

EXPLICACIÓN SIMPLE:
- Los HTML originales son solo "diseño" (como un dibujo)
- Los PHP en /forms/ son los formularios "funcionando" (guardan datos)
- La base de datos es como un "archivo gigante" donde se guarda todo

================================================================================
3. CONFIGURACIÓN INICIAL (MySQL/Laragon)
================================================================================

PASO 1: VERIFICAR QUE LARAGON ESTÉ CORRIENDO
--------------------------------------------
1. Abre Laragon
2. Verifica que MySQL esté en VERDE (ON)
3. Si está en ROJO, haz clic en MySQL para activarlo
4. Espera 5-10 segundos hasta que esté completamente iniciado

PASO 2: CREAR LA BASE DE DATOS
-------------------------------
OPCIÓN A: Usando phpMyAdmin (MÁS FÁCIL)
1. Abre tu navegador
2. Ve a: http://localhost/phpmyadmin
3. En el menú izquierdo, haz clic en "Nueva" o "New"
4. Nombre de la base de datos: recursos_humanos
5. Cotejamiento: utf8mb4_unicode_ci
6. Haz clic en "Crear"

OPCIÓN B: Usando el archivo SQL (MÁS RÁPIDO)
1. Abre: http://localhost/phpmyadmin
2. Haz clic en la pestaña "SQL" (arriba)
3. Abre el archivo: SISTEMA_RRHH/database/schema.sql
4. Copia TODO el contenido (Ctrl+A, Ctrl+C)
5. Pégalo en phpMyAdmin (Ctrl+V)
6. Haz clic en "Continuar" o "Ejecutar"
7. ¡Listo! La base de datos está creada con todas las tablas

PASO 3: VERIFICAR LA CONFIGURACIÓN
-----------------------------------
Abre el archivo: SISTEMA_RRHH/config/database.php

Debería tener estos valores (por defecto en Laragon):
- DB_HOST: 'localhost'
- DB_USER: 'root'
- DB_PASS: '' (vacío)
- DB_NAME: 'recursos_humanos'
- DB_PORT: 3306

Si tu MySQL usa otro puerto (como 3307), cámbialo aquí.

PASO 4: PROBAR LA CONEXIÓN
----------------------------
1. Abre: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/test_conexion.php
2. Si ves "✅ Conexión exitosa", ¡todo está bien!
3. Si ves un error, revisa los pasos anteriores

================================================================================
4. CÓMO FUNCIONA EL SISTEMA
================================================================================

FLUJO DE USO (Paso a paso):
----------------------------

1. USUARIO VA A: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/
   → Ve la página de login (index.php)

2. USUARIO SE REGISTRA O INICIA SESIÓN
   → Si es nuevo: va a registro.php
   → Si ya tiene cuenta: ingresa cédula y contraseña

3. DESPUÉS DEL LOGIN
   → El sistema guarda su sesión (como una "tarjeta de identificación")
   → Lo redirige a dashboard.php

4. EN EL DASHBOARD
   → Ve 6 tarjetas con los formularios disponibles
   → Hace clic en el formulario que necesita

5. LLENA EL FORMULARIO
   → Los datos personales se llenan automáticamente
   → Solo completa los campos específicos del formulario
   → Hace clic en "Guardar"

6. EL SISTEMA GUARDA EN LA BASE DE DATOS
   → PHP procesa el formulario
   → Guarda los datos en MySQL
   → Muestra mensaje de éxito

7. PUEDE VER SUS FORMULARIOS
   → Va a "Mis Formularios"
   → Ve todos los formularios que ha enviado

CÓMO FUNCIONA TÉCNICAMENTE:
---------------------------

1. HTML: La "cara bonita" que ves
2. PHP: El "cerebro" que procesa todo
3. MySQL: El "almacén" donde se guarda todo
4. JavaScript: Hace que los formularios sean "inteligentes"

EJEMPLO PRÁCTICO:
-----------------
Cuando llenas "Jornada Extraordinaria":
1. HTML muestra el formulario bonito
2. JavaScript calcula las horas automáticamente
3. Cuando haces clic en "Guardar", PHP:
   - Toma todos los datos
   - Los limpia (para seguridad)
   - Los guarda en MySQL
   - Te muestra "¡Guardado exitosamente!"

================================================================================
5. ARCHIVOS IMPORTANTES EXPLICADOS
================================================================================

config/database.php
-------------------
¿QUÉ HACE?
Conecta el sistema con MySQL.

¿CÓMO FUNCIONA?
- Define las credenciales (usuario, contraseña, nombre de BD)
- Tiene una función conectarDB() que crea la conexión
- Si hay error, muestra un mensaje claro

¿QUÉ MODIFICAR?
Solo si tu MySQL usa otro puerto o contraseña diferente.

includes/session.php
---------------------
¿QUÉ HACE?
Maneja el "login" y "logout" de usuarios.

¿CÓMO FUNCIONA?
- Cuando te logueas, guarda tu ID en una "sesión PHP"
- Cada página verifica si estás logueado
- Si no estás logueado, te redirige al login

FUNCIONES IMPORTANTES:
- estaLogueado(): Verifica si hay sesión activa
- iniciarSesion(): Guarda tu sesión
- cerrarSesion(): Elimina tu sesión
- requerirLogin(): Protege páginas (solo logueados pueden entrar)

includes/funciones.php
-----------------------
¿QUÉ HACE?
Funciones útiles que se usan en todo el sistema.

FUNCIONES:
- limpiarDatos(): Limpia datos para evitar hackeos
- obtenerFuncionario(): Obtiene datos del usuario logueado
- mostrarExito(): Muestra mensaje verde de éxito
- mostrarError(): Muestra mensaje rojo de error

index.php
----------
¿QUÉ HACE?
Página de inicio de sesión.

¿CÓMO FUNCIONA?
1. Muestra un formulario de login
2. Cuando envías el formulario:
   - Busca tu cédula en la base de datos
   - Verifica tu contraseña
   - Si es correcta, inicia sesión
   - Te redirige al dashboard

dashboard.php
-------------
¿QUÉ HACE?
Menú principal después del login.

¿CÓMO FUNCIONA?
- Muestra tu información personal
- Muestra 6 tarjetas con los formularios
- Cada tarjeta tiene un botón que te lleva al formulario

forms/jornada_extraordinaria.php
----------------------------------
¿QUÉ HACE?
Formulario completo de Jornada Extraordinaria integrado.

¿CÓMO FUNCIONA?
1. Verifica que estés logueado
2. Pre-llena tus datos personales automáticamente
3. Te muestra el formulario
4. Cuando lo envías:
   - Valida los datos
   - Guarda en la tabla "jornadas_extraordinarias"
   - Guarda los horarios en "jornadas_extraordinarias_horarios"
   - Te muestra mensaje de éxito

ESTRUCTURA:
- Parte superior: PHP (procesa el formulario)
- Parte media: HTML (muestra el formulario)
- Parte inferior: JavaScript (hace cálculos automáticos)

================================================================================
6. INTEGRACIÓN DE FORMULARIOS
================================================================================

ESTADO ACTUAL:
--------------
✅ INTEGRADO: Jornada Extraordinaria
✅ INTEGRADO: Misión Oficial
❌ FALTAN: Reincorporación, Tiempo Compensatorio, Permiso, Vacaciones

NOTA: El formulario de Misión Oficial ya está integrado y funcionando.
      Puedes acceder desde el dashboard haciendo clic en "Misión Oficial".

CÓMO INTEGRAR UN FORMULARIO (Paso a paso):
-------------------------------------------

PASO 1: Crear el archivo PHP
- Ubicación: SISTEMA_RRHH/forms/nombre_formulario.php
- Copia la estructura de jornada_extraordinaria.php

PASO 2: Adaptar el HTML
- Toma el HTML del formulario original
- Reemplaza los campos estáticos con PHP
- Agrega "name" a todos los inputs para que PHP los capture

PASO 3: Agregar el procesamiento PHP
- Al inicio del archivo, agrega el código PHP
- Valida los datos recibidos
- Inserta en la base de datos usando INSERT INTO

PASO 4: Probar
- Llena el formulario
- Verifica que se guarde en la base de datos
- Revisa en phpMyAdmin que los datos estén ahí

EJEMPLO DE CÓDIGO PHP PARA GUARDAR:
------------------------------------
<?php
// 1. Incluir archivos necesarios
require_once '../config/database.php';
require_once '../includes/session.php';
requerirLogin();

// 2. Conectar a la base de datos
$conn = conectarDB();

// 3. Si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 4. Obtener datos del formulario
    $campo1 = limpiarDatos($_POST['campo1'] ?? '');
    $campo2 = limpiarDatos($_POST['campo2'] ?? '');
    
    // 5. Validar
    if (empty($campo1)) {
        $error = 'El campo 1 es obligatorio';
    } else {
        // 6. Guardar en la base de datos
        $stmt = $conn->prepare("INSERT INTO nombre_tabla (campo1, campo2) VALUES (?, ?)");
        $stmt->bind_param("ss", $campo1, $campo2);
        
        if ($stmt->execute()) {
            $mensaje = '¡Guardado exitosamente!';
        } else {
            $error = 'Error: ' . $conn->error;
        }
    }
}
?>

EXPLICACIÓN DEL CÓDIGO:
-----------------------
- require_once: Incluye otros archivos PHP
- $_POST: Datos que vienen del formulario
- limpiarDatos(): Limpia los datos para seguridad
- prepare(): Prepara una consulta SQL (más seguro)
- bind_param(): Asigna valores a los ? en la consulta
- execute(): Ejecuta la consulta

================================================================================
7. RESOLUCIÓN DE PROBLEMAS
================================================================================

PROBLEMA: "Error: MySQL no está corriendo"
SOLUCIÓN:
1. Abre Laragon
2. Verifica que MySQL esté en VERDE
3. Si está en ROJO, haz clic para activarlo
4. Espera 10 segundos y recarga la página

PROBLEMA: "Base de datos no encontrada"
SOLUCIÓN:
1. Ve a phpMyAdmin: http://localhost/phpmyadmin
2. Crea la base de datos "recursos_humanos"
3. O ejecuta el archivo schema.sql

PROBLEMA: "Error de conexión"
SOLUCIÓN:
1. Verifica config/database.php
2. Asegúrate que DB_NAME sea "recursos_humanos"
3. Verifica que MySQL esté corriendo

PROBLEMA: "No puedo iniciar sesión"
SOLUCIÓN:
1. Verifica que tengas un usuario registrado
2. Ve a registro.php y crea uno nuevo
3. Asegúrate de recordar tu cédula y contraseña

PROBLEMA: "Los formularios no se guardan"
SOLUCIÓN:
1. Verifica que la base de datos tenga las tablas
2. Revisa que el formulario tenga method="POST"
3. Verifica que los campos tengan "name"
4. Revisa los errores en la consola del navegador (F12)

PROBLEMA: "No veo los estilos (se ve feo)"
SOLUCIÓN:
1. Verifica tu conexión a internet (usa CDN)
2. Abre la consola del navegador (F12)
3. Busca errores en la pestaña "Console"

================================================================================
8. PRÓXIMOS PASOS PARA APRENDER
================================================================================

NIVEL 1: ENTENDER LO BÁSICO
---------------------------
1. Aprende qué es PHP (lenguaje del servidor)
2. Aprende qué es MySQL (base de datos)
3. Aprende HTML básico (estructura de páginas)
4. Aprende CSS básico (diseño visual)

RECURSOS RECOMENDADOS:
- w3schools.com (tutoriales gratis)
- PHP.net (documentación oficial)
- YouTube: "PHP para principiantes"

NIVEL 2: MODIFICAR EL SISTEMA
------------------------------
1. Cambia colores en los formularios
2. Agrega campos nuevos a un formulario
3. Modifica mensajes de éxito/error
4. Cambia el diseño del dashboard

NIVEL 3: CREAR NUEVAS FUNCIONALIDADES
--------------------------------------
1. Agrega un formulario nuevo
2. Crea una página para ver estadísticas
3. Agrega búsqueda de formularios
4. Crea reportes en PDF

CONCEPTOS IMPORTANTES A APRENDER:
----------------------------------
1. VARIABLES: Guardan información ($nombre = "Juan")
2. FUNCIONES: Código reutilizable (function sumar() { ... })
3. ARRAYS: Listas de datos ($usuarios = ["Juan", "María"])
4. CONDICIONALES: Si/entonces (if ($edad > 18) { ... })
5. BUCLES: Repetir código (for, while)
6. SQL: Consultas a la base de datos (SELECT, INSERT, UPDATE)

PRÁCTICA RECOMENDADA:
---------------------
1. Crea un formulario simple desde cero
2. Modifica un formulario existente
3. Agrega validaciones nuevas
4. Experimenta con los estilos CSS

================================================================================
ENLACES ÚTILES DEL SISTEMA
================================================================================

LOCAL (en tu computadora):
--------------------------
- Login: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/
- Dashboard: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/dashboard.php
- Registro: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/registro.php
- phpMyAdmin: http://localhost/phpmyadmin
- Test Conexión: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/test_conexion.php

NOTA: El %20 es un espacio en la URL. Si tu carpeta tiene espacios, 
      Laragon los convierte automáticamente.

ARCHIVOS IMPORTANTES:
----------------------
- Configuración BD: SISTEMA_RRHH/config/database.php
- Estructura BD: SISTEMA_RRHH/database/schema.sql
- Sesiones: SISTEMA_RRHH/includes/session.php
- Funciones: SISTEMA_RRHH/includes/funciones.php

================================================================================
GLOSARIO DE TÉRMINOS
================================================================================

PHP: Lenguaje de programación del servidor
MySQL: Sistema de base de datos
HTML: Lenguaje de marcado (estructura de páginas)
CSS: Hojas de estilo (diseño visual)
JavaScript: Lenguaje del navegador (interactividad)
Laragon: Servidor local (corre PHP y MySQL en tu PC)
phpMyAdmin: Interfaz web para gestionar MySQL
CDN: Servicios externos (Bootstrap, Font Awesome)
Session: Sesión de usuario (mantiene al usuario logueado)
SQL: Lenguaje para consultar bases de datos
POST: Método para enviar datos de formularios
GET: Método para obtener datos de la URL

================================================================================
9. VERIFICACIÓN COMPLETA DEL SISTEMA
================================================================================

PASO 1: VERIFICAR MYSQL
-----------------------
1. Abre: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/test_conexion.php
2. Debe mostrar: "✅ ¡ÉXITO! Conexión establecida"
3. Si muestra error, sigue las instrucciones en pantalla

PASO 2: VERIFICAR BASE DE DATOS
---------------------------------
1. Abre: http://localhost/phpmyadmin
2. En el menú izquierdo, busca "recursos_humanos"
3. Si no existe, crea la base de datos (ver PASO 2 en sección 3)
4. Debe tener estas tablas:
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

PASO 3: VERIFICAR LOGIN
------------------------
1. Abre: http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/
2. Debe mostrar la página de login
3. Si no tienes usuario, ve a registro.php
4. Crea un usuario de prueba
5. Inicia sesión

PASO 4: VERIFICAR DASHBOARD
-----------------------------
1. Después del login, debe mostrarte el dashboard
2. Debe mostrar 6 tarjetas con los formularios
3. Verifica que tu información personal aparezca correctamente

PASO 5: VERIFICAR FORMULARIOS
------------------------------
1. Haz clic en "Jornada Extraordinaria"
   → Debe abrir el formulario
   → Debe pre-llenar tus datos
   → Debe permitir guardar

2. Haz clic en "Misión Oficial"
   → Debe abrir el formulario
   → Debe pre-llenar tus datos
   → Debe permitir guardar

3. Los otros formularios aún no están integrados (aparecerá error 404)

PASO 6: VERIFICAR GUARDADO
---------------------------
1. Llena un formulario completo
2. Haz clic en "Guardar"
3. Debe mostrar mensaje de éxito
4. Ve a phpMyAdmin
5. Busca la tabla correspondiente (ej: misiones_oficiales)
6. Debe aparecer tu registro guardado

LISTA DE VERIFICACIÓN RÁPIDA:
-----------------------------
□ MySQL está corriendo (verde en Laragon)
□ Base de datos "recursos_humanos" existe
□ Todas las tablas están creadas
□ Puedo iniciar sesión
□ Veo el dashboard correctamente
□ Formulario Jornada Extraordinaria funciona
□ Formulario Misión Oficial funciona
□ Los datos se guardan en la base de datos

================================================================================
10. CONFIGURACIÓN DE MYSQL EN LARAGON
================================================================================

VERIFICAR PUERTO DE MYSQL:
---------------------------
1. Abre Laragon
2. Haz clic derecho en MySQL
3. Selecciona "Config" o "Configuración"
4. Busca el puerto (normalmente 3306 o 3307)
5. Anota el puerto que usa

CONFIGURAR database.php:
------------------------
Abre: SISTEMA_RRHH/config/database.php

Si tu MySQL usa puerto 3306:
    define('DB_PORT', 3306);

Si tu MySQL usa puerto 3307:
    define('DB_PORT', 3307);

Si no estás seguro:
1. Usa el test_conexion.php (ver sección 9)
2. Te dirá exactamente qué puerto usar

VERIFICAR CREDENCIALES:
-----------------------
Por defecto en Laragon:
- Usuario: root
- Contraseña: (vacía, sin nada)
- Host: localhost o 127.0.0.1

Si cambiaste la contraseña de MySQL:
1. Actualiza DB_PASS en database.php
2. O restablece la contraseña en Laragon

================================================================================
NOTAS FINALES
================================================================================

- Este sistema está diseñado para ser fácil de entender
- Todos los archivos tienen comentarios explicativos
- Si tienes dudas, revisa este README primero
- Experimenta sin miedo (siempre puedes restaurar desde Git)
- Aprende paso a paso, no intentes entender todo de una vez

ESTADO ACTUAL DEL PROYECTO:
---------------------------
✅ Sistema de login funcionando
✅ Dashboard funcionando
✅ Base de datos configurada
✅ Formulario Jornada Extraordinaria integrado (con selectores de hora mejorados)
✅ Formulario Misión Oficial integrado (con selectores de hora mejorados)
✅ Sistema de sesiones con expiración automática (2 horas)
✅ Selectores de hora mejorados (se puede hacer clic para seleccionar)
⏳ Pendiente: Integrar 4 formularios más

MEJORAS RECIENTES:
------------------
✅ Selectores de hora: Ahora puedes hacer clic en los campos de hora para 
   seleccionar con un selector visual (no hay que escribir manualmente)
✅ Sesiones: Las sesiones expiran automáticamente después de 2 horas
✅ Verificación: El sistema verifica automáticamente si la sesión sigue activa

CÓMO USAR LOS SELECTORES DE HORA:
----------------------------------
1. Haz clic en cualquier campo de hora (verás un ícono de reloj 🕐)
2. En navegadores modernos (Chrome, Edge, Firefox reciente):
   - Aparecerá un selector visual con números
   - Puedes hacer clic en las horas y minutos
3. Si no aparece el selector visual:
   - Puedes escribir la hora manualmente en formato 24h (ej: 14:30)
   - O hacer doble clic en el campo para que aparezca
4. Formato: HH:MM (ejemplo: 09:00, 14:30, 18:45)

PRÓXIMOS PASOS SUGERIDOS:
--------------------------
1. Probar los formularios integrados
2. Verificar que los datos se guarden correctamente
3. Integrar los formularios faltantes (siguiendo el ejemplo de jornada_extraordinaria.php)
4. Agregar funcionalidad para ver formularios enviados
5. Mejorar el diseño según tus necesidades

¡ÉXITO EN TU APRENDIZAJE! 🚀

================================================================================
FIN DEL README
================================================================================

