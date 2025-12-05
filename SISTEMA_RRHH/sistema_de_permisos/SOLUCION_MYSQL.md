# 🔧 SOLUCIÓN: MySQL No Está Corriendo

## ⚠️ PROBLEMA DETECTADO
El error "denegó expresamente dicha conexión" significa que **MySQL NO está corriendo** en tu computadora.

---

## ✅ SOLUCIÓN PASO A PASO

### PASO 1: Abrir Laragon

1. **Busca el ícono de Laragon** en la barra de tareas (esquina inferior derecha)
2. Si no lo ves:
   - Presiona `Windows + S`
   - Busca "Laragon"
   - Haz doble clic para abrirlo

---

### PASO 2: Ver la Ventana de Laragon

En la ventana principal de Laragon deberías ver algo como esto:

```
┌─────────────────────────────┐
│   Laragon 8.3.0             │
├─────────────────────────────┤
│ Services:                   │
│   Apache   [ON]  ← Verde   │
│   MySQL    [OFF] ← Rojo    │ ← ESTE ES EL PROBLEMA
└─────────────────────────────┘
```

---

### PASO 3: Activar MySQL

**Opción A: Desde la lista de servicios**
1. En la ventana de Laragon, busca la línea que dice **"MySQL"**
2. Verás que dice **[OFF]** o está en **rojo**
3. **Haz clic** en la palabra "MySQL" o en el botón [OFF]
4. Debería cambiar a **[ON]** o **verde**
5. **Espera 15-20 segundos** mientras inicia

**Opción B: Desde el menú**
1. **Clic derecho** en el ícono de Laragon (barra de tareas)
2. Busca **"MySQL"** en el menú
3. Haz clic en **"Start"** o **"Iniciar"**
4. Espera 15-20 segundos

**Opción C: Botón Start All**
1. En la ventana de Laragon, busca el botón **"Start All"**
2. Haz clic en él
3. Esto iniciará Apache Y MySQL
4. Espera 20-30 segundos

---

### PASO 4: Verificar que MySQL Esté Corriendo

Después de activarlo, deberías ver:

```
┌─────────────────────────────┐
│   Laragon 8.3.0             │
├─────────────────────────────┤
│ Services:                   │
│   Apache   [ON]  ← Verde   │
│   MySQL    [ON]  ← Verde   │ ← ¡DEBE ESTAR ASÍ!
└─────────────────────────────┘
```

---

### PASO 5: Probar la Conexión

1. **Abre tu navegador**
2. Ve a: `http://localhost/RECURSOS%20HUMANOS/SISTEMA_RRHH/test_conexion.php`
3. **Recarga la página** (F5)
4. Ahora debería mostrar que encontró la conexión ✅

---

## 🆘 SI AÚN NO FUNCIONA

### Verificar en el Administrador de Tareas

1. Presiona `Ctrl + Shift + Esc`
2. Ve a la pestaña **"Detalles"**
3. Busca **"mysqld.exe"** o **"mysql.exe"** en la lista
4. Si **NO lo encuentras** = MySQL no está corriendo
5. Si **SÍ lo encuentras** = MySQL está corriendo pero hay otro problema

---

### Verificar Puerto

A veces Laragon usa el puerto 3307 en lugar de 3306.

**Prueba esto:**
1. Abre el archivo: `config/database.php`
2. Cambia la línea:
   ```php
   define('DB_PORT', 3306);
   ```
   Por:
   ```php
   define('DB_PORT', 3307);
   ```
3. Guarda el archivo
4. Prueba de nuevo

---

### Reiniciar MySQL Completamente

1. En Laragon, **detén MySQL** (clic en MySQL → Stop)
2. Espera 10 segundos
3. **Inicia MySQL** nuevamente (clic en MySQL → Start)
4. Espera 20 segundos
5. Prueba de nuevo

---

## 📞 RESUMEN

**Lo más importante:**
- MySQL debe estar **[ON]** o en **verde** en Laragon
- Si está **[OFF]** o en **rojo**, haz clic para activarlo
- Espera 15-20 segundos después de activarlo
- Luego prueba de nuevo

**¿Qué hacer ahora?**
1. Abre Laragon
2. Verifica si MySQL está ON o OFF
3. Si está OFF, actívalo
4. Espera 20 segundos
5. Prueba el diagnóstico otra vez

---

## ✅ SEÑAL DE ÉXITO

Cuando MySQL esté corriendo correctamente, el script de diagnóstico mostrará:

```
✅ ¡ÉXITO! Conexión establecida
Puerto usado: 3306
Host usado: localhost
✅ Base de datos creada o ya existe

🎉 ¡CONFIGURACIÓN FUNCIONAL ENCONTRADA!
```

¡Eso significa que ya puedes registrar funcionarios!












