# Monitor WebSocket - Sistema de Reconocimiento Facial

Esta vista personalizada de Filament v3 permite monitorear en tiempo real el sistema de reconocimiento facial a través de WebSocket.

## Características

### 🔄 Monitoreo en Tiempo Real
- **Estado de Conexión**: Indicador visual del estado de la conexión WebSocket
- **Estado del Sistema**: Información en tiempo real sobre el servicio de reconocimiento facial
- **Salones Activos**: Lista de salones siendo monitoreados actualmente
- **Eventos en Tiempo Real**: Stream de eventos del sistema con timestamps

### 📊 Información del Sistema
- Versión del sistema
- Estado del auto-sync
- Número total de salones
- Salones actualmente monitoreando
- Estado general del sistema

### 🏫 Información de Salones
- Código de matrícula del salón
- Estado (Activo/Inactivo)
- Número de rostros cargados
- Detecciones del día

### 📝 Eventos Monitoreados
- Conexiones/desconexiones del WebSocket
- Detecciones de rostros
- Actualizaciones de salones
- Logs del sistema
- Actualizaciones de caché
- Errores del sistema

## Configuración

### Variables de Entorno
Asegúrate de tener configurada la variable en tu archivo `.env`:

```env
FACE_SERVICE_URL=http://localhost:5000
```

### Archivo de Configuración
La configuración se almacena en `config/services.php`:

```php
'face_service' => [
    'url' => env('FACE_SERVICE_URL'),
],
```

## Uso

### Acceso a la Vista
1. Inicia sesión en Filament
2. Ve al menú lateral y busca "Monitor WebSocket" en la sección "Sistema"
3. La página se conectará automáticamente al WebSocket

### Funciones Disponibles
- **Actualizar Estado**: Solicita manualmente el estado del sistema
- **Limpiar Eventos**: Limpia la lista de eventos mostrados

### Comandos Artisan

#### Verificar Conectividad
Puedes verificar si el servicio de reconocimiento facial está disponible usando:

```bash
php artisan face:check
```

Este comando:
- Verifica la conectividad con el servicio
- Muestra información del estado del servicio
- Proporciona detalles de la respuesta

## Estructura de Archivos

```
app/
├── Console/Commands/
│   └── CheckFaceServiceCommand.php     # Comando para verificar conectividad
├── Filament/
│   ├── Pages/
│   │   └── WebSocketMonitor.php        # Página principal del monitor
│   └── Widgets/
│       └── FaceRecognitionStatsWidget.php  # Widget de estadísticas para dashboard
└── resources/views/filament/pages/
    └── websocket-monitor.blade.php     # Vista Blade con interfaz y JavaScript
```

## WebSocket - Eventos Soportados

### Eventos Enviados por el Cliente
- `solicitar_estado`: Solicita el estado actual del sistema

### Eventos Recibidos del Servidor
- `connect`: Conexión establecida
- `disconnect`: Conexión perdida
- `conexion_establecida`: Confirmación de conexión con datos del cliente
- `sistema_estado`: Estado completo del sistema y salones
- `deteccion_rostro`: Nueva detección de rostro
- `salon_update`: Actualización de estado de un salón
- `log_evento`: Eventos de log del sistema
- `cache_update`: Actualizaciones de caché
- `error`: Errores del sistema

## Personalización

### Estilos CSS
La vista incluye estilos personalizados para:
- Animaciones de conexión (pulse)
- Scroll personalizado para eventos
- Animaciones fade-in para nuevos eventos
- Colores según el tipo de evento

### Tipos de Eventos
Los eventos se colorean automáticamente según su tipo:
- **success**: Verde (conexiones exitosas, confirmaciones)
- **error**: Rojo (errores, desconexiones)
- **detection**: Púrpura (detecciones de rostros)
- **info**: Azul (información general)

## Troubleshooting

### Problemas Comunes

1. **WebSocket no se conecta**:
   - Verifica que `FACE_SERVICE_URL` esté configurado correctamente
   - Asegúrate de que el servicio de Python esté ejecutándose
   - Ejecuta `php artisan face:check` para verificar conectividad

2. **Eventos no aparecen**:
   - Verifica la consola del navegador para errores JavaScript
   - Confirma que el servicio WebSocket esté emitiendo eventos

3. **Página no carga**:
   - Limpia la caché de Filament: `php artisan filament:cache-components`
   - Verifica permisos de archivos

### Logs
Los eventos se muestran en tiempo real en la interfaz. Para debugging adicional, revisa:
- Logs de Laravel: `storage/logs/laravel.log`
- Consola del navegador (F12)
- Logs del servicio de Python

## Widget de Dashboard

El widget `FaceRecognitionStatsWidget` muestra estadísticas rápidas en el dashboard principal:
- Estado del servicio (En línea/Fuera de línea)
- Número de salones monitoreando
- Detecciones del día

Para personalizar el widget, edita `app/Filament/Widgets/FaceRecognitionStatsWidget.php`.

## Seguridad

- La conexión WebSocket debe estar protegida en producción
- Considera implementar autenticación para el WebSocket
- Las credenciales del servicio deben estar en variables de entorno

## Desarrollo Futuro

Posibles mejoras:
- Autenticación WebSocket
- Filtros de eventos
- Exportación de logs
- Notificaciones push
- Integración con sistema de alertas
