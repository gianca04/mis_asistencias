# API de Cámaras

## Endpoint: Obtener Cámaras Activas

### URL
```
GET /api/camaras/activas
```

### Descripción
Devuelve todas las cámaras que están marcadas como activas en el sistema, incluyendo información detallada de la matrícula asociada.

### Headers
```
Accept: application/json
```

### Respuesta Exitosa

**Código:** `200 OK`

**Contenido:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "url_stream": "http://192.168.1.100:8080/stream",
            "matricula_id": 1,
            "matricula": {
                "id": 1,
                "codigo_matricula": "2025PRIMERO1A",
                "anio_escolar": "2025",
                "grado": "1er Grado",
                "seccion": "A",
                "regla": {
                    "id": 1,
                    "name": "Horario Matutino",
                    "hora_entrada": "07:30:00",
                    "hora_tardanza": "08:00:00",
                    "comentarios": "Horario regular de mañana"
                }
            },
            "activo": true,
            "created_at": "2025-07-14T04:46:25.000000Z",
            "updated_at": "2025-07-14T04:46:25.000000Z"
        }
    ],
    "total": 1
}
```

### Campos de Respuesta

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `success` | boolean | Indica si la operación fue exitosa |
| `data` | array | Array de objetos cámara |
| `data[].id` | integer | ID único de la cámara |
| `data[].url_stream` | string | URL del stream de video |
| `data[].matricula_id` | integer | ID de la matrícula asociada |
| `data[].matricula` | object | Información detallada de la matrícula |
| `data[].matricula.codigo_matricula` | string | Código único de la matrícula |
| `data[].matricula.anio_escolar` | string | Año escolar |
| `data[].matricula.grado` | string | Nombre del grado |
| `data[].matricula.seccion` | string | Nombre de la sección |
| `data[].matricula.regla` | object/null | Información de la regla de horarios asociada |
| `data[].matricula.regla.id` | integer | ID único de la regla |
| `data[].matricula.regla.name` | string | Nombre de la regla |
| `data[].matricula.regla.hora_entrada` | time | Hora de entrada permitida |
| `data[].matricula.regla.hora_tardanza` | time | Hora límite para tardanza |
| `data[].matricula.regla.comentarios` | string | Comentarios adicionales de la regla |
| `data[].activo` | boolean | Estado de la cámara (siempre true en este endpoint) |
| `data[].created_at` | timestamp | Fecha de creación |
| `data[].updated_at` | timestamp | Fecha de última actualización |
| `total` | integer | Número total de cámaras activas |

### Ejemplo de Uso

#### cURL
```bash
curl -X GET "http://localhost/mis_asistencias/public/api/camaras/activas" \
     -H "Accept: application/json"
```

#### JavaScript (fetch)
```javascript
fetch('/api/camaras/activas', {
    method: 'GET',
    headers: {
        'Accept': 'application/json'
    }
})
.then(response => response.json())
.then(data => {
    console.log('Cámaras activas:', data.data);
    console.log('Total:', data.total);
});
```

#### Python (requests)
```python
import requests

response = requests.get('http://localhost/mis_asistencias/public/api/camaras/activas')
data = response.json()

print(f"Total de cámaras activas: {data['total']}")
for camara in data['data']:
    print(f"Cámara {camara['id']}: {camara['url_stream']}")
```

### Notas
- Solo devuelve cámaras con `activo = true`
- Incluye información completa de la matrícula asociada
- La respuesta está optimizada con eager loading para evitar consultas N+1
- El endpoint no requiere autenticación (ajustar según necesidades de seguridad)
