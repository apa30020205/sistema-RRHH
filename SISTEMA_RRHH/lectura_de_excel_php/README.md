# 📊 Lector de Excel

Interfaz simple con drag & drop para leer archivos Excel usando el microservicio Python.

## 🚀 Características

- ✅ **Drag & Drop**: Arrastra y suelta archivos Excel directamente
- ✅ **Microservicio Python**: Usa el microservicio en `C:\AMPYME\MICROSERVICIO LECTURA DE EXCEL`
- ✅ **Soporte múltiple**: Lee archivos .xlsx, .xls, .csv
- ✅ **Visualización JSON**: Muestra los datos en formato JSON
- ✅ **Dos áreas independientes**: Una para archivo biométrico y otra para filtro

## 📋 Requisitos

1. **Microservicio Python corriendo**:
   - Ubicación: `C:\AMPYME\MICROSERVICIO LECTURA DE EXCEL`
   - Puerto: `5000`
   - URL: `http://localhost:5000`

2. **Iniciar el microservicio**:
   ```bash
   cd "C:\AMPYME\MICROSERVICIO LECTURA DE EXCEL"
   python app.py
   ```

## 🎯 Uso

1. Asegúrate de que el microservicio Python esté corriendo
2. Accede a la interfaz desde el Dashboard: **Herramientas > Lector de Excel**
3. Arrastra y suelta un archivo Excel en cualquiera de las dos áreas:
   - **Archivo Biométrico**: Datos del sistema biométrico
   - **Archivo Filtro**: Excel con datos filtrados
4. Los datos se mostrarán automáticamente en formato JSON debajo del área de carga

## 📝 Notas

- El sistema verifica automáticamente si el microservicio está disponible
- Si el microservicio no está corriendo, verás un mensaje de error
- Los archivos se procesan automáticamente al soltarlos o seleccionarlos
- No se guardan archivos en el servidor, todo se procesa en memoria

## 🔧 Configuración

Si el microservicio está en otra ubicación o puerto, edita `index.php`:

```php
define('MICROSERVICIO_URL', 'http://localhost:5000/api/read-excel');
```

## 📚 Documentación del Microservicio

Para más información sobre el microservicio Python, consulta:
- `C:\AMPYME\MICROSERVICIO LECTURA DE EXCEL\README.md`

