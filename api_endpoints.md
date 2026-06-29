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
    "address_description": "Entrada principal, preguntar por Maria.",
    "audit_name": "Pedro Pérez",
    "audit_phone": "+584121234567"
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
    "severity": "critico",
    "audit_name": "Pedro Pérez",
    "audit_phone": "+584121234567"
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

### Actualizar Paciente (PUT) - Requiere Autenticación
**Endpoint:** `PUT /api/v1/patients/{id}`
**Headers Requeridos:** `Authorization: Bearer {token}` (Debe estar registrado e iniciado sesión)
*Permite a usuarios registrados corregir errores en pacientes ingresados previamente.*
**Cuerpo de la petición (JSON):**
- `first_name` (string, opcional)
- `last_name` (string, opcional)
- `approximate_age` (integer, opcional)
- `physical_description` (string, opcional)
- `medical_status` (string, opcional)

**Posibles Códigos de Error:**
- **401 Unauthorized:** El usuario no está autenticado o el token es inválido.
- **404 Not Found:** El paciente no existe.
- **422 Unprocessable Entity:** Intento de modificar el DNI o el hospital (prohibido por seguridad).

---

## 6. Mascotas Perdidas (`/lost-pets`)

### Registrar Mascota Perdida (POST)
**Endpoint:** `POST /api/v1/lost-pets`
*No requiere autenticación. Soporta subida de foto a Cloudflare R2 y retorna un secure_token.*
**Cuerpo de la petición (FormData):**
- `reporter_name` (string, max 150)
- `contact_phone` (string, regex: `+58...`)
- `pet_name` (string, opcional)
- `species` (string): 'perro', 'gato', 'otro'
- `breed` (string, opcional)
- `description` (string)
- `latitude` (numeric)
- `longitude` (numeric)
- `address_description` (string)
- `photo` (file, opcional, max: 5MB)

**Ejemplo de Respuesta Exitoso (201 Created):**
```json
{
  "success": true,
  "message": "Mascota perdida registrada exitosamente",
  "data": {
    "id": 1,
    "reporter_name": "Luis Pérez",
    "contact_phone": "+584141234567",
    "pet_name": "Firulais",
    "species": "perro",
    "status": "perdido",
    "photo_url": "https://pub-....r2.dev/lost_pets/...",
    "secure_token": "a1b2c3d4-...",
    "created_at": "2026-06-26T23:00:00.000000Z"
  }
}
```
**Posibles Códigos de Error:**
- **422 Unprocessable Entity:** Faltan campos requeridos o el teléfono no es válido (no comienza con +58412/414/etc).

### Actualizar Estado (PUT)
**Endpoint:** `PUT /api/v1/lost-pets/{id}`
**Headers Requeridos:** `X-Secure-Token`
**Cuerpo (JSON):**
- `status` (string, opcional): 'perdido', 'encontrado', 'en_refugio'

**Posibles Códigos de Error:**
- **403 Forbidden:** El `X-Secure-Token` no coincide.
- **404 Not Found:** La mascota no existe.
- **422 Unprocessable Entity:** Intento de modificar el número telefónico (está prohibido).

---

## 7. Zonas Afectadas (`/affected-zones`)

### Registrar Zona Afectada (POST)
**Endpoint:** `POST /api/v1/affected-zones`
*No requiere autenticación. Retorna un secure_token.*
**Cuerpo de la petición (JSON):**
- `name` (string, max 150)
- `severity` (string, opcional): 'leve', 'moderada', 'grave', 'desastre_total'
- `description` (string)
- `latitude` (numeric)
- `longitude` (numeric)
- `status` (string, opcional): 'activa', 'controlada', 'evacuada'
- `audit_name` (string) - Requerido
- `audit_phone` (string) - Requerido

**Ejemplo de Respuesta Exitoso (201 Created):**
```json
{
  "success": true,
  "message": "Zona afectada registrada exitosamente",
  "data": {
    "id": 1,
    "name": "Sector Las Tejerías",
    "severity": "desastre_total",
    "description": "Deslizamiento de tierra bloqueando accesos.",
    "latitude": 10.2458,
    "longitude": -67.1685,
    "status": "activa",
    "secure_token": "f4e3d2c1-...",
    "created_at": "2026-06-26T23:05:00.000000Z"
  }
}
```

### Actualizar Zona (PUT)
**Endpoint:** `PUT /api/v1/affected-zones/{id}`
**Headers Requeridos:** `X-Secure-Token`
**Cuerpo (JSON):**
- `severity` (string, opcional)
- `status` (string, opcional)
- `description` (string, opcional)

**Posibles Códigos de Error:**
- **403 Forbidden:** El `X-Secure-Token` no coincide.
- **404 Not Found:** La zona no existe.

---

## 8. Edificios Dañados (`/damaged-buildings`)

### Listar Edificios Dañados (GET)
**Endpoint:** `GET /api/v1/damaged-buildings`
*Permite consultar la lista de edificios afectados. Soporta filtros.*
**Query Params:**
- `city` (string) - *Búsqueda parcial (ILIKE) por ciudad.*
- `damage_level` (string) - *Filtro exacto (ej: 'parcial', 'severo', 'total').*

### Registrar Edificio Dañado (POST)
**Endpoint:** `POST /api/v1/damaged-buildings`
*Registro público Zero-login. Retorna un `secure_token`.*
**Cuerpo de la petición (JSON):**
- `name` (string, max 255) - Requerido
- `city` (string, max 150)
- `zone` (string, max 150)
- `latitude` (numeric)
- `longitude` (numeric)
- `damage_level` (string, max 50) - 'parcial', 'severo', 'total'
- `status` (string, max 50) - 'reportado', 'verificado'
- `general_source` (string, max 255)
- `notes` (string)
- `audit_name` (string) - Requerido
- `audit_phone` (string) - Requerido

**Posibles Códigos de Error:**
- **422 Unprocessable Entity:** Errores de validación.

### Actualizar Edificio (PUT)
**Endpoint:** `PUT /api/v1/damaged-buildings/{id}`
**Headers Requeridos:** `X-Secure-Token`
*Permite corregir información o actualizar el estado de verificación.*

---

## 9. Voluntarios (`/volunteers`)

### Listar Voluntarios (GET)
**Endpoint:** `GET /api/v1/volunteers`
*Obtiene la lista de voluntarios. Permite filtros vía Query Params para llenar la interfaz de búsqueda.*
**Query Params Soportados:**
- `skill`: Filtra por categoría profesional (ej: `medico`, `rescatista`, `informatico`).
- `modality`: Filtra por modalidad (ej: `presencial`, `virtual`).
- `search`: Búsqueda de texto (busca coincidencias en el nombre o en la descripción de ubicación `location_description`).

**Ejemplo de Petición:** `GET /api/v1/volunteers?skill=medico&modality=virtual`

### Registrar Voluntario (POST)
**Endpoint:** `POST /api/v1/volunteers`
*Registro público Zero-login. Retorna un secure_token.*
**Cuerpo de la petición (JSON o FormData):**
- `full_name` (string, max 150)
- `dni` (string, max 20)
- `phone` (string, regex: `+58...`)
- `email` (string, opcional)
- `skill_category` (string, max 50): ej. 'medico', 'enfermero', 'psicologo', 'rescatista', 'conductor', 'informatico', 'otra_ayuda'
- `availability_description` (string, max 150): ej. 'Disponible 24hs'
- `modality` (string): 'presencial', 'virtual'
- `location_description` (string)
- `latitude` (numeric, opcional)
- `longitude` (numeric, opcional)
- `description` (string)

**Ejemplo de Respuesta Exitoso (201 Created):**
```json
{
  "success": true,
  "message": "Voluntario registrado exitosamente",
  "data": {
    "id": 1,
    "full_name": "María González",
    "dni": "V-12345678",
    "phone": "+584141234567",
    "skill_category": "medico",
    "availability_description": "Fines de semana",
    "modality": "presencial",
    "location_description": "Mérida, Mérida",
    "description": "Atención primaria.",
    "status": "disponible",
    "secure_token": "a1b2c3d4-...",
    "created_at": "2026-06-26T23:00:00.000000Z"
  }
}
```
**Posibles Códigos de Error:**
- **422 Unprocessable Entity:** Faltan campos requeridos o el teléfono no tiene formato válido de Venezuela.

### Actualizar Estado de Voluntario (PUT)
**Endpoint:** `PUT /api/v1/volunteers/{id}`
**Headers Requeridos:** `X-Secure-Token`
**Cuerpo (JSON):**
- `status` (string, opcional): 'disponible', 'asignado', 'inactivo'
- `availability_description` (string, opcional)
- `location_description` (string, opcional)

**Posibles Códigos de Error:**
- **403 Forbidden:** El `X-Secure-Token` no coincide.
- **404 Not Found:** El voluntario no existe.
- **422 Unprocessable Entity:** Intentar modificar DNI o Teléfono (está prohibido por seguridad).

---

## 9. Autenticación (Fase II)

* **Registro:** `POST /api/v1/auth/register` (Requiere `name`, `dni`, `phone`, `email`, `password`) - Retorna el `token` (JWT).
## 10. Damnificados / Personas Evacuadas (`/evacuees`)

### Listar Damnificados (GET)
**Endpoint:** `GET /api/v1/evacuees`
*Obtiene la lista de damnificados. Soporta filtros vía Query Params.*
**Query Params Soportados:**
- `dni`: Filtro exacto por DNI.
- `name`: Búsqueda parcial (ILIKE) por nombre o apellido.
- `status`: Filtro por estado ('en_refugio', 'reubicado', 'desaparecido', 'fallecido').
- `current_location`: Búsqueda parcial por ubicación actual.

### Registrar Damnificado y Grupo Familiar (POST)
**Endpoint:** `POST /api/v1/evacuees`
*Permite registrar a una persona afectada. Además, soporta el registro anidado de un grupo familiar completo en una sola petición, asignando automáticamente al primer registrado como el jefe de familia de los demás.*
**Cuerpo de la petición (JSON o FormData):**
- `first_name` (string) - Requerido
- `last_name` (string, opcional)
- `dni` (string, opcional)
- `age` (integer, opcional)
- `gender` (string, opcional)
- `current_location` (string, opcional)
- `status` (string, opcional): 'en_refugio', 'reubicado', 'desaparecido', 'fallecido'
- `photo` (file, opcional)
- `family_members` (array, opcional): Lista de familiares que acompañan o dependen de esta persona.
  - `family_members[0][first_name]` (string) - Requerido si se envía array
  - `family_members[0][family_relationship]` (string) - Relación (ej: "hijo", "esposo")
  - `family_members[0][age]` (integer)
  - `family_members[0][photo]` (file)

**Ejemplo de Respuesta Exitoso (201 Created):**
Retorna al damnificado principal con su relación `family_members` anidada, conteniendo a todos los familiares insertados bajo su cargo.

### Ver Detalle de Damnificado (GET)
**Endpoint:** `GET /api/v1/evacuees/{id}`
Retorna los datos del damnificado. *Nota:* Incluye automáticamente los arrays `family_members` (si es jefe de hogar) y el objeto `head_of_family` (si este registro depende de otro).

### Actualizar Damnificado (PUT) - Requiere Auth
**Endpoint:** `PUT /api/v1/evacuees/{id}`
**Headers Requeridos:** `Authorization: Bearer {token}` (Solo superadmin, admin o coordinator)
**Cuerpo (JSON):**
Permite actualizar campos individuales del damnificado, e incluso reasignar a qué familia pertenece enviando un `head_of_family_id` válido.
