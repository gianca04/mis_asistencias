# Registro Masivo de Asistencias

## Funcionalidades Implementadas

### 1. Acciones Header en la Tabla de Asistencias

Se han agregado dos nuevas acciones en la cabecera de la tabla de asistencias:

#### Registro Masivo por Matrícula
- **Icono**: Grupo de usuarios (user-group)
- **Color**: Verde (success)
- **Funcionalidad**: Permite registrar asistencias para todos los estudiantes de una matrícula específica
- **Formulario incluye**:
  - Selector de matrícula (con información de grado, sección y año escolar)
  - Selector de fecha
  - Estado por defecto (presente, tardanza, falta, justificado)
  - Comentario general opcional

#### Registro Masivo por Grado y Sección
- **Icono**: Gorro académico (academic-cap)
- **Color**: Azul (info)
- **Funcionalidad**: Permite registrar asistencias seleccionando grado, sección y año escolar específicos
- **Formulario incluye**:
  - Selector de grado
  - Selector de sección
  - Selector de año escolar
  - Selector de fecha
  - Estado por defecto
  - Comentario general opcional

### 2. Acciones Bulk Mejoradas

Se han mejorado las acciones bulk para el manejo masivo de asistencias existentes:

#### Marcar como Falta
- **Color**: Rojo (danger)
- **Icono**: X circle
- **Incluye confirmación**: Sí

#### Marcar como Presente
- **Color**: Verde (success)
- **Icono**: Check circle
- **Incluye confirmación**: Sí

#### Marcar como Tardanza
- **Color**: Amarillo (warning)
- **Icono**: Clock
- **Incluye confirmación**: Sí

#### Marcar como Justificado
- **Color**: Azul (info)
- **Icono**: Document check
- **Incluye formulario**: Sí (requiere comentario de justificación)

### 3. Página de Registro Masivo Interactiva

Se ha creado una página dedicada (`/dashboard/asistencias/registro-masivo`) con las siguientes características:

#### Selección de Salón
- Formulario dinámico para seleccionar matrícula y fecha
- Actualización en tiempo real de la lista de estudiantes

#### Lista Interactiva de Estudiantes
- Tabla con todos los estudiantes de la matrícula seleccionada
- Campos editables para cada estudiante:
  - Estado de asistencia (dropdown)
  - Comentario individual (texto libre)
- Indicador visual del estado de registro (ya registrado vs pendiente)

#### Acciones Rápidas
- **Botón "Todos Presentes"**: Marca a todos los estudiantes como presentes
- **Botón "Todos Falta"**: Marca a todos los estudiantes como falta
- **Botón "Todos Tardanza"**: Marca a todos los estudiantes como tardanza

#### Funcionalidades Avanzadas
- **Detección de registros existentes**: Si ya existe un registro de asistencia para un estudiante en la fecha seleccionada, se muestra y permite editarlo
- **Creación y actualización**: El sistema crea nuevos registros o actualiza los existentes según corresponda
- **Validaciones**: Verifica que se haya seleccionado matrícula y fecha antes de procesar
- **Notificaciones**: Muestra mensajes de éxito con el resumen de registros creados/actualizados

### 4. Mejoras Visuales en la Tabla Principal

#### Columna de Estado con Badges
- **Presente**: Badge verde
- **Tardanza**: Badge amarillo
- **Falta**: Badge rojo
- **Justificado**: Badge azul

#### Nuevas Columnas
- **Código Matrícula**: Searchable y sortable
- **Grado**: Con relación al modelo, toggleable
- **Sección**: Con relación al modelo, toggleable

### 5. Navegación Mejorada

#### Botón en Lista de Asistencias
- Acceso directo al registro masivo desde la página principal de asistencias
- Ubicado en las acciones de cabecera junto al botón "Crear"

## Cómo Usar

### Registro Masivo desde la Tabla Principal

1. Ve a la página de Asistencias
2. Haz clic en "Registro Masivo" en la cabecera
3. Completa el formulario:
   - Selecciona la matrícula (salón)
   - Elige la fecha
   - Selecciona el estado por defecto
   - Agrega un comentario general (opcional)
4. Haz clic en "Registrar Asistencias"

### Registro Masivo desde la Página Dedicada

1. Ve a `/dashboard/asistencias/registro-masivo` o haz clic en "Registro Masivo" desde la lista de asistencias
2. Selecciona la matrícula y fecha
3. La lista de estudiantes se cargará automáticamente
4. Usa los botones de acción rápida o edita individualmente cada estudiante
5. Haz clic en "Guardar Todas las Asistencias"

### Edición Masiva de Asistencias Existentes

1. En la tabla de asistencias, selecciona múltiples registros usando los checkboxes
2. Usa las acciones bulk para cambiar el estado de todos los seleccionados
3. Para justificaciones, se abrirá un formulario para agregar el comentario

## Ventajas del Sistema

1. **Eficiencia**: Registro de múltiples asistencias en una sola operación
2. **Flexibilidad**: Permite tanto registro masivo como edición individual
3. **Prevención de duplicados**: Detecta registros existentes y permite actualizarlos
4. **Interfaz intuitiva**: Uso de badges coloridos y acciones claras
5. **Validaciones robustas**: Verificaciones antes de procesar los datos
6. **Feedback claro**: Notificaciones detalladas del resultado de las operaciones

## Archivos Modificados/Creados

1. `app/Filament/Resources/AsistenciaResource.php` - Agregadas acciones header y bulk mejoradas
2. `app/Filament/Resources/AsistenciaResource/Pages/RegistroMasivo.php` - Nueva página de registro masivo
3. `app/Filament/Resources/AsistenciaResource/Pages/ListAsistencias.php` - Agregado botón de acceso
4. `resources/views/filament/resources/asistencia-resource/pages/registro-masivo.blade.php` - Vista de la página de registro masivo

## Tecnologías Utilizadas

- **Filament v3**: Framework para paneles de administración
- **Livewire**: Para interactividad en tiempo real
- **Laravel**: Framework backend
- **Tailwind CSS**: Para estilos y componentes visuales
