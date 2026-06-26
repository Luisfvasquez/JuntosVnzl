# Documentación de Endpoints API - Fase I

Esta documentación detalla los endpoints de la API RESTful correspondientes a la Fase I (Emergencia y Zero-Login), incluyendo la preparación para la Fase II (Autenticación JWT). Todas las respuestas siguen un estándar JSON estructurado con `success`, `message` y `data` / `errors`.

**Base URL:** `/api/v1`

---

## 1. Ofertas de Ayuda (`/aid-offers`)

### Crear Oferta (POST)
**Endpoint:** `POST /api/v1/aid-offers`
*No requiere autenticación.*
**Cuerpo de la petición (JSON):**
```json
{
    "provider_name": "Juan Perez",
    "dni": "V-12345678",
    "phone": "+584141234567",
    "title": "Donación de ropa de invierno",
    "description": "5 cajas de ropa limpia y en buen estado.",
    "category": "ropa",
    "latitude": 10.480593,
    "longitude": -66.903606,
    "address_description": "Plaza Venezuela, frente a la fuente."
}
```
**Respuesta Exitosa (201):**
Retorna la oferta creada e incluye el `secure_token` (UUID) generado, el cual debe ser guardado por el frontend para futuras modificaciones.

### Modificar Oferta (PUT)
**Endpoint:** `PUT /api/v1/aid-offers/{id}`
**Headers:** `X-Secure-Token: <secure_token_guardado>`
**Cuerpo de la petición (JSON):**
Se envían únicamente los campos a modificar. **Nota:** `dni` y `phone` son inmutables y retornarán error (422) si se intentan actualizar. Se permite actualizar el `status` (ej. a "reservado" o "entregado").

### Eliminar Oferta (DELETE)
**Endpoint:** `DELETE /api/v1/aid-offers/{id}`
**Headers:** `X-Secure-Token: <secure_token_guardado>`

---

## 2. Centros de Acopio (`/collection-centers`)

### Registrar Centro (POST)
**Endpoint:** `POST /api/v1/collection-centers`
*No requiere autenticación.*
**Cuerpo de la petición (JSON):**
```json
{
    "center_name": "Colegio San José",
    "manager_name": "Maria Lopez",
    "dni": "V-87654321",
    "phone": "+584127654321",
    "latitude": 10.490000,
    "longitude": -66.890000,
    "address_description": "Entrada principal, preguntar por Maria."
}
```
**Respuesta Exitosa (201):**
Retorna los datos del centro e incluye un `secure_token`.

### Modificar Centro (PUT)
**Endpoint:** `PUT /api/v1/collection-centers/{id}`
**Headers:** `X-Secure-Token: <secure_token_guardado>`
**Cuerpo de la petición (JSON):**
Actualización de campos (excepto DNI y teléfono). Permite actualizar el estado (`status`).

---

## 3. Incidentes y Reportes (`/incidents`)

### Reportar Incidente (POST)
**Endpoint:** `POST /api/v1/incidents`
*No requiere autenticación.*
**Cuerpo de la petición (JSON):**
```json
{
    "reporter_name": "Vecino Anónimo",
    "description": "Vía colapsada por deslizamiento de tierra, paso bloqueado.",
    "latitude": 10.450000,
    "longitude": -66.920000,
    "address_description": "Autopista Regional del Centro, km 5.",
    "severity": "critico"
}
```

### Listar Incidentes (GET)
**Endpoint:** `GET /api/v1/incidents`
Retorna todos los incidentes registrados para su renderizado en mapa.

---

## 4. Ayuda Internacional (`/international-aid`)

### Registrar Ayuda Internacional (POST)
**Endpoint:** `POST /api/v1/international-aid`
*No requiere autenticación.*
**Cuerpo de la petición (JSON):**
```json
{
    "country_name": "Colombia",
    "iso_code": "COL",
    "aid_type": "Rescate e Insumos",
    "description": "2 equipos de búsqueda y rescate USAR."
}
```

---

## 5. Red de Salud y Pacientes (`/hospitals` y `/patients`)

### Registrar Hospital/Centro (POST)
**Endpoint:** `POST /api/v1/hospitals`
*No requiere autenticación. Soporta subida de imágenes a Cloudflare R2.*
**Cuerpo de la petición (FormData):**
- `name` (string)
- `type` (string): 'hospital_publico', 'clinica_privada', 'carpa_campana'
- `latitude` (numeric)
- `longitude` (numeric)
- `address_description` (string)
- `status` (string): 'operativo', 'colapsado', 'sin_insumos', 'evacuado'
- `contact_phone` (string, opcional)
- `image` (file, opcional, max: 5MB)

### Listar Hospitales (GET)
**Endpoint:** `GET /api/v1/hospitals`

### Registrar Paciente Manualmente (POST)
**Endpoint:** `POST /api/v1/patients`
*No requiere autenticación. Soporta subida de imágenes a Cloudflare R2.*
**Cuerpo de la petición (FormData):**
- `hospital_id` (integer)
- `dni` (string, opcional)
- `first_name` (string, opcional)
- `last_name` (string, opcional)
- `approximate_age` (integer, opcional)
- `physical_description` (string, opcional)
- `medical_status` (string): 'estable', 'critico', 'observacion', 'dado_de_alta', 'fallecido', 'trasladado'
- `photo` (file, opcional, max: 5MB)

### Buscar Paciente (GET)
**Endpoint:** `GET /api/v1/patients/search`
*Permite buscar familiares.*
**Query Params:**
- `dni` (string)
- `name` (string) - *Busca coincidencias parciales (ILIKE) en first_name o last_name.*
- `description` (string) - *Busca coincidencias en physical_description.*

---

## 6. Autenticación (Fase II)

* **Registro:** `POST /api/v1/auth/register` (Requiere `name`, `dni`, `phone`, `email`, `password`) - Retorna el `token` (JWT).
* **Login:** `POST /api/v1/auth/login` (Requiere `dni`, `password`) - Retorna el `token` (JWT).
