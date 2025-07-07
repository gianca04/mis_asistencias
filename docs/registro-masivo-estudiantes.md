# Registro Masivo de Estudiantes

Este sistema permite registrar estudiantes masivamente en las matrículas de varias formas:

## Funcionalidades Disponibles

### 1. Registro Individual
- Crear estudiantes uno por uno desde el formulario
- Usar la acción "Attach" para asociar estudiantes existentes

### 2. Registro Masivo de Estudiantes Existentes
- **Acción**: "Registrar Múltiples Estudiantes"
- **Descripción**: Permite seleccionar múltiples estudiantes existentes y asociarlos a la matrícula
- **Ubicación**: En las acciones del header de la tabla de estudiantes

### 3. Importación desde Excel
- **Acción**: "Importar desde Excel"
- **Descripción**: Permite importar estudiantes desde un archivo Excel o CSV
- **Ubicación**: En las acciones del header de la tabla de estudiantes

#### Formato del archivo Excel/CSV
El archivo debe contener las siguientes columnas (headers en la primera fila):

| Columna | Obligatorio | Descripción |
|---------|-------------|-------------|
| nombre | Sí | Primer nombre del estudiante |
| apellido | Sí | Apellido del estudiante |
| dni | Sí | DNI de 8 dígitos |
| codigo_estudiante | Sí | Código único del estudiante |
| telefono | No | Teléfono de 9 dígitos |
| direccion | No | Dirección del estudiante |

#### Ejemplo de archivo Excel:

```
nombre    | apellido | dni      | codigo_estudiante | telefono  | direccion
Juan      | Pérez    | 12345678 | EST001           | 987654321 | Av. Principal 123
María     | García   | 87654321 | EST002           | 123456789 | Calle Secundaria 456
```

### 4. Descargar Plantilla
- **Acción**: "Descargar Plantilla"
- **Descripción**: Descarga un archivo Excel de ejemplo con el formato correcto
- **Ubicación**: En las acciones del header de la tabla de estudiantes

## Instrucciones de Uso

### Para importar estudiantes desde Excel:

1. Ve a la matrícula donde quieres registrar estudiantes
2. Haz clic en la pestaña "Estudiantes"
3. Haz clic en "Descargar Plantilla" para obtener el formato correcto
4. Completa el archivo Excel con los datos de los estudiantes
5. Guarda el archivo
6. Haz clic en "Importar desde Excel"
7. Sube tu archivo
8. El sistema procesará el archivo y te mostrará los resultados

### Para registro masivo de estudiantes existentes:

1. Ve a la matrícula donde quieres registrar estudiantes
2. Haz clic en la pestaña "Estudiantes"
3. Haz clic en "Registrar Múltiples Estudiantes"
4. Selecciona los estudiantes que quieres asociar a esta matrícula
5. Confirma la acción

## Notas Importantes

- Los estudiantes con DNI duplicado no se crearán nuevamente
- Solo se mostrarán estudiantes que no estén ya registrados en la matrícula actual
- Los archivos Excel deben tener headers en la primera fila
- Se aceptan archivos .xlsx, .xls y .csv
- El sistema validará que todos los campos obligatorios estén presentes

## Validaciones

- **DNI**: Debe ser único y tener exactamente 8 dígitos
- **Teléfono**: Debe tener exactamente 9 dígitos (opcional)
- **Código de estudiante**: Debe ser único en el sistema
- **Nombre y apellido**: Son campos obligatorios

## Mensajes de Error Comunes

- "El DNI debe tener exactamente 8 dígitos": El DNI proporcionado no tiene el formato correcto
- "Faltan campos requeridos": Alguna de las columnas obligatorias está vacía
- "Algunos estudiantes ya estaban registrados": Intentaste registrar estudiantes que ya están en la matrícula
