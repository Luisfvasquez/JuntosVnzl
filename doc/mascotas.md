# Guía Técnica - Módulo de Mascotas Perdidas (Registro y Búsqueda)

El módulo de mascotas perdidas permite a los ciudadanos registrar reportes de mascotas extraviadas o encontradas durante la emergencia, facilitando la reunificación familiar con sus animales domésticos.

---

## 1. Reglas de Negocio

1. **Campos Requeridos de Contacto y Ubicación:**
   * El nombre del reportante (`reporter_name`) y su número de contacto (`contact_phone`) son obligatorios.
   * La descripción del animal (`description`), coordenadas (`latitude`, `longitude`) y descripción de la dirección (`address_description`) son obligatorios para poder geolocalizar el reporte en el mapa.
2. **Generación de Token de Seguridad (`secure_token`):**
   * Al registrar un reporte de mascota perdida, el sistema genera automáticamente un **`secure_token`** (UUID único).
   * Este token es devuelto únicamente en la respuesta de la creación (`201 Created`). El frontend debe almacenarlo localmente para permitir que el reportante edite (ej. cambiar status a "encontrado") o elimine su reporte sin necesidad de loguearse.
3. **Restricción de Modificación (Inmutabilidad):**
   * El número de teléfono (`contact_phone`) no se puede modificar una vez creado el reporte (retorna `422 Unprocessable Entity` si se intenta enviar).

---

## 2. Referencia de Endpoints

### 2.1. Registrar Mascota Perdida (`POST /api/v1/lost-pets`)

Crea un reporte de mascota perdida. Admite la subida de una imagen, la cual se procesa (se redimensiona y convierte a formato WebP al 80% de calidad) y se almacena en Cloudflare R2.

* **Headers:** `Content-Type: multipart/form-data`
* **Cuerpo de la Petición (FormData):**

| Clave | Tipo | ¿Obligatorio? | Descripción / Reglas |
| :--- | :--- | :---: | :--- |
| `reporter_name` | String | **Sí** | Nombre de quien reporta. Máx 150 caracteres. |
| `contact_phone` | String | **Sí** | Teléfono del reportante. Formato: `+58(412/414/424/416/426)XXXXXXX` (12 dígitos). |
| `pet_name` | String | No | Nombre de la mascota. Máx 100 caracteres. |
| `species` | String | No | Especie. Valores: `perro`, `gato`, `otro`. *(Por defecto: `perro`)* |
| `breed` | String | No | Raza de la mascota. Máx 100 caracteres. |
| `description` | String | **Sí** | Rasgos particulares, colores, collar, comportamiento. |
| `latitude` | Numeric | **Sí** | Latitud geográfica de la última vez visto. |
| `longitude` | Numeric | **Sí** | Longitud geográfica de la última vez visto. |
| `address_description` | String | **Sí** | Descripción detallada de la dirección de pérdida. |
| `photo` | File (Imagen) | No | Imagen de la mascota (JPG, PNG, WEBP). Máx 5MB. |

* **Respuesta Exitosa (201 Created):**
  ```json
  {
      "success": true,
      "message": "Mascota perdida registrada exitosamente",
      "data": {
          "id": 5,
          "reporter_name": "Juan Pérez",
          "contact_phone": "+584121234567",
          "pet_name": "Toby",
          "species": "perro",
          "breed": "Poodle",
          "description": "Blanco con collar rojo, asustadizo.",
          "latitude": 10.4806,
          "longitude": -66.9036,
          "address_description": "Cerca de la Plaza Altamira",
          "status": "perdido",
          "photo_url": "https://pub-b02b2aa279b749d1903eccc02e283489.r2.dev/lost_pets/667c2901a1b2c.webp",
          "secure_token": "a0f3d9e8-4b2c-4e1d-8f9a-0b1c2d3e4f5a",
          "created_at": "2026-06-26T21:00:00.000000Z",
          "updated_at": "2026-06-26T21:00:00.000000Z"
      }
  }
  ```

---

### 2.2. Obtener Lista de Mascotas Perdidas (`GET /api/v1/lost-pets`)

Retorna la lista completa de todas las mascotas perdidas para mostrarlas en mapas o listados generales.

* **Headers:** `Content-Type: application/json`
* **Respuesta Exitosa (200 OK):**
  ```json
  {
      "success": true,
      "data": [
          {
              "id": 5,
              "reporter_name": "Juan Pérez",
              "contact_phone": "+584121234567",
              "pet_name": "Toby",
              "species": "perro",
              "breed": "Poodle",
              "description": "Blanco con collar rojo.",
              "latitude": 10.4806,
              "longitude": -66.9036,
              "address_description": "Cerca de la Plaza Altamira",
              "status": "perdido",
              "photo_url": "https://pub-b02b2aa279b749d1903eccc02e283489.r2.dev/lost_pets/667c2901a1b2c.webp",
              "created_at": "2026-06-26T21:00:00.000000Z",
              "updated_at": "2026-06-26T21:00:00.000000Z"
          }
      ]
  }
  ```
  *(Nota: El campo `secure_token` se oculta automáticamente en este listado por seguridad).*

---

### 2.3. Obtener Detalle de Mascota (`GET /api/v1/lost-pets/{id}`)

Obtiene los detalles específicos de un reporte por su ID.

* **Headers:** `Content-Type: application/json`
* **Respuesta Exitosa (200 OK):**
  ```json
  {
      "success": true,
      "data": {
          "id": 5,
          "reporter_name": "Juan Pérez",
          "contact_phone": "+584121234567",
          "pet_name": "Toby",
          "species": "perro",
          "breed": "Poodle",
          ...
      }
  }
  ```

---

### 2.4. Actualizar Estado de Mascota (`PUT /api/v1/lost-pets/{id}`)

Permite modificar el estado de la mascota (por ejemplo, a `encontrado` o `en_refugio`). Requiere enviar el token de seguridad provisto al crear el reporte.

* **Headers:** 
  * `Content-Type: application/json`
  * `X-Secure-Token: <secure_token>` (Token provisto en la creación)
* **Cuerpo de la Petición:**
  ```json
  {
      "status": "encontrado"
  }
  ```
* **Respuesta Exitosa (200 OK):**
  ```json
  {
      "success": true,
      "message": "Mascota actualizada",
      "data": {
          "id": 5,
          "status": "encontrado",
          ...
      }
  }
  ```

---

### 2.5. Eliminar Reporte de Mascota (`DELETE /api/v1/lost-pets/{id}`)

Elimina permanentemente el reporte de mascota perdida. Requiere el token de seguridad.

* **Headers:**
  * `X-Secure-Token: <secure_token>`
* **Respuesta Exitosa (200 OK):**
  ```json
  {
      "success": true,
      "message": "Registro eliminado exitosamente"
  }
  ```
