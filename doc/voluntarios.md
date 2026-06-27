# Guía Técnica - Módulo de Voluntarios (Registro y Gestión)

El módulo de voluntarios permite a los ciudadanos registrarse como colaboradores de apoyo para las labores de rescate, distribución de ayuda y soporte técnico/médico durante la emergencia.

---

## 1. Reglas de Negocio

1. **Campos Obligatorios de Identidad y Competencia:**
   * El nombre completo (`full_name`), DNI (`dni`), teléfono (`phone`) y habilidades principales (`skill_category`) son obligatorios para garantizar la validez del voluntario.
   * La descripción de la disponibilidad (`availability_description`), modalidad (`modality`) y descripción del área (`location_description`) son campos requeridos.
2. **Generación de Token de Seguridad (`secure_token`):**
   * Al registrarse un voluntario, el sistema genera automáticamente un **`secure_token`** (UUID único).
   * Este token es devuelto únicamente en la respuesta de la creación (`201 Created`). El frontend debe guardarlo localmente para permitir que el propio voluntario actualice su estado (ej. cambiar status a "asignado" o "inactivo") o cambie su descripción de disponibilidad más adelante.
3. **Restricción de Modificación (Inmutabilidad):**
   * El DNI (`dni`) y el teléfono (`phone`) son inmutables. Si se envían en una petición `PUT`, la API responderá con un error `422 Unprocessable Entity`.

---

## 2. Referencia de Endpoints

### 2.1. Registrar Voluntario (`POST /api/v1/volunteers`)

Registra la postulación de un nuevo voluntario en el sistema.

* **Headers:** `Content-Type: application/json`
* **Cuerpo de la Petición (JSON):**

| Clave | Tipo | ¿Obligatorio? | Descripción / Reglas |
| :--- | :--- | :---: | :--- |
| `full_name` | String | **Sí** | Nombre completo. Máx 150 caracteres. |
| `dni` | String | **Sí** | Documento de identidad (ej: `V-12345678`). Máx 20 caracteres. |
| `phone` | String | **Sí** | Teléfono de contacto. Formato: `+58(412/414/424/416/426)XXXXXXX` (12 dígitos). |
| `email` | String | No | Dirección de correo electrónico. Máx 150 caracteres. |
| `skill_category` | String | **Sí** | Categoría de habilidad (ej: `medicina`, `logistica`, `rescate`, `cocina`, `soporte_tecnico`). |
| `availability_description` | String | **Sí** | Descripción breve de horarios y días disponibles. Máx 150 caracteres. |
| `modality` | String | **Sí** | Modalidad del voluntariado. Valores: `presencial`, `virtual`. |
| `location_description` | String | **Sí** | Ubicación o zona geográfica de influencia. |
| `latitude` | Numeric | No | Latitud de la zona de cobertura. |
| `longitude` | Numeric | No | Longitud de la zona de cobertura. |
| `description` | String | **Sí** | Breve descripción de experiencia, profesión u observaciones. |

* **Respuesta Exitosa (201 Created):**
  ```json
  {
      "success": true,
      "message": "Voluntario registrado exitosamente",
      "data": {
          "id": 12,
          "full_name": "Pedro Armas",
          "dni": "V-18765432",
          "phone": "+584149876543",
          "email": "pedro.armas@example.com",
          "skill_category": "medicina",
          "availability_description": "Fines de semana y tardes",
          "modality": "presencial",
          "location_description": "Caracas - Chacao",
          "latitude": 10.4900,
          "longitude": -66.8600,
          "description": "Estudiante de medicina de último año, experiencia en primeros auxilios.",
          "status": "disponible",
          "secure_token": "f47ac10b-58cc-4372-a567-0e02b2c3d479",
          "created_at": "2026-06-26T21:05:00.000000Z",
          "updated_at": "2026-06-26T21:05:00.000000Z"
      }
  }
  ```

---

### 2.2. Buscar / Filtrar Voluntarios (`GET /api/v1/volunteers`)

Permite a los coordinadores buscar y filtrar la lista completa de voluntarios según sus habilidades, modalidad o palabras clave.

* **Headers:** `Content-Type: application/json`
* **Query Parameters (Opcionales):**
  * `skill` (string): Filtra por la categoría exacta de habilidad (`skill_category`).
  * `modality` (string): Filtra por la modalidad exacta (`presencial` o `virtual`).
  * `search` (string): Filtra por coincidencia parcial case-insensitive (`ILIKE`) en el nombre (`full_name`) o en la descripción de ubicación (`location_description`).

* **Respuesta Exitosa (200 OK):**
  ```json
  {
      "success": true,
      "data": [
          {
              "id": 12,
              "full_name": "Pedro Armas",
              "dni": "V-18765432",
              "phone": "+584149876543",
              "email": "pedro.armas@example.com",
              "skill_category": "medicina",
              "availability_description": "Fines de semana y tardes",
              "modality": "presencial",
              "location_description": "Caracas - Chacao",
              "latitude": 10.4900,
              "longitude": -66.8600,
              "description": "Estudiante de medicina de último año...",
              "status": "disponible",
              "created_at": "2026-06-26T21:05:00.000000Z",
              "updated_at": "2026-06-26T21:05:00.000000Z"
          }
      ]
  }
  ```
  *(Nota: El campo `secure_token` se oculta automáticamente en este listado).*

---

### 2.3. Obtener Detalle de un Voluntario (`GET /api/v1/volunteers/{id}`)

Obtiene el detalle completo de la ficha de un voluntario por su ID.

* **Headers:** `Content-Type: application/json`
* **Respuesta Exitosa (200 OK):**
  ```json
  {
      "success": true,
      "data": {
          "id": 12,
          "full_name": "Pedro Armas",
          "dni": "V-18765432",
          "phone": "+584149876543",
          ...
      }
  }
  ```

---

### 2.4. Actualizar Estado o Disponibilidad (`PUT /api/v1/volunteers/{id}`)

Permite modificar las condiciones de disponibilidad o cambiar el estado operativo de un voluntario (valores válidos: `disponible`, `asignado`, `inactivo`). Requiere enviar el token de seguridad provisto al crearse.

* **Headers:** 
  * `Content-Type: application/json`
  * `X-Secure-Token: <secure_token>` (Token provisto en la creación)
* **Cuerpo de la Petición (JSON):**
  ```json
  {
      "status": "asignado",
      "availability_description": "Lunes a Viernes por la mañana"
  }
  ```
* **Respuesta Exitosa (200 OK):**
  ```json
  {
      "success": true,
      "message": "Voluntario actualizado",
      "data": {
          "id": 12,
          "status": "asignado",
          "availability_description": "Lunes a Viernes por la mañana",
          ...
      }
  }
  ```
