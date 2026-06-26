# Documentación Técnica de la API - Plataforma de Coordinación Humanitaria (Venezuela)

Esta documentación está diseñada para servir como referencia técnica definitiva para el desarrollo del frontend en **React**. Detalla todos los contratos de datos, validaciones estrictas, mecanismos de seguridad y respuestas del servidor.

---

## 1. Información General y Estándares de la API

* **Base URL:** `/api/v1`
* **Formato de Respuesta Común:** Todas las respuestas de la API (tanto exitosas como con error) siguen una estructura JSON consistente:

### Estructura de Respuesta Exitosa (HTTP 200/201)
```json
{
    "success": true,
    "message": "Mensaje descriptivo del resultado de la operación",
    "data": { ... } // Objeto o Array con la información solicitada
}
```

### Estructura de Respuesta Exitosa Paginada (Laravel LengthAwarePaginator)
Para endpoints que devuelven listas extensas, la clave `data` contendrá la envoltura del paginador de Laravel:
```json
{
    "success": true,
    "message": "Mensaje descriptivo",
    "data": {
        "current_page": 1,
        "data": [ ... ], // Array de objetos de la página actual
        "first_page_url": "http://localhost/api/v1/recurso?page=1",
        "from": 1,
        "last_page": 3,
        "last_page_url": "http://localhost/api/v1/recurso?page=3",
        "links": [ ... ],
        "next_page_url": "http://localhost/api/v1/recurso?page=2",
        "path": "http://localhost/api/v1/recurso",
        "per_page": 15,
        "prev_page_url": null,
        "to": 15,
        "total": 45
    }
}
```

### Estructura de Respuesta de Validación (HTTP 422 Unprocessable Entity)
Generada automáticamente por Laravel al fallar las reglas de validación de los Form Requests:
```json
{
    "success": false,
    "message": "Error de validación", // Opcional, dependiendo del endpoint
    "errors": {
        "campo_invalido": [
            "El campo campo_invalido es obligatorio.",
            "El formato del campo campo_invalido es inválido."
        ]
    }
}
```

### Estructura de Respuestas de Error Comunes (HTTP 401 / 403 / 404 / 409)
```json
{
    "success": false,
    "message": "Descripción detallada del error (ej. 'Credenciales inválidas', 'Recurso no encontrado')"
}
```

---

## 2. Mecanismos de Autorización

El backend maneja dos esquemas de autorización independientes según la fase y el recurso:

### A. Autorización Local por Recurso (`X-Secure-Token`)
* **Uso:** Permite a usuarios anónimos (sin cuenta) crear recursos (como Ofertas de Ayuda o Centros de Acopio) y poder modificarlos o eliminarlos posteriormente desde el mismo navegador.
* **Flujo:** 
  1. El frontend realiza un `POST` para crear el recurso.
  2. La API responde con un código `201 Created` y añade en el cuerpo (`data`) un campo `secure_token` (UUIDv4).
  3. El frontend **debe persistir** este token (ej. en `localStorage` o `cookies` del navegador) asociado al ID del recurso.
  4. Para cualquier petición posterior de tipo `PUT` o `DELETE` sobre ese recurso específico, el frontend debe incluir el token en los headers:
     `X-Secure-Token: <UUIDv4_guardado>`
  5. Si el token no coincide o falta en la cabecera, la API responderá con `403 Forbidden`.

### B. Autenticación Global por Token JWT (`Authorization: Bearer <token>`)
* **Uso:** Para endpoints administrativos o de usuarios registrados (Fase II).
* **Flujo:**
  1. El usuario inicia sesión vía `POST /auth/login`.
  2. La API retorna un token JWT en `data.token`.
  3. El frontend debe adjuntar este token en todas las peticiones protegidas usando el header:
     `Authorization: Bearer <token_jwt>`

---

## 3. Catálogo de Endpoints

### 3.1. Ofertas de Ayuda (`/aid-offers`)

Maneja las donaciones y ofertas de transporte, comida, ropa, salud y vivienda.

#### Listar Ofertas de Ayuda
* **URL:** `GET /api/v1/aid-offers`
* **Auth:** No requiere.
* **Query Parameters:**
  * `page` (opcional, entero): Número de página (por defecto `1`).
  * `per_page` (opcional, entero): Elementos por página (por defecto `15`).
* **Respuesta Exitosa (200 OK):**
  ```json
  {
      "success": true,
      "message": "Ofertas obtenidas correctamente",
      "data": {
          "current_page": 1,
          "data": [
              {
                  "id": 1,
                  "user_id": null,
                  "provider_name": "Juan Perez",
                  "dni": "V-12345678",
                  "phone": "+584141234567",
                  "title": "Donación de ropa de invierno",
                  "description": "5 cajas de ropa limpia.",
                  "category": "ropa",
                  "latitude": 10.48059300,
                  "longitude": -66.90360600,
                  "address_description": "Plaza Venezuela, frente a la fuente.",
                  "secure_token": "a1b2c3d4-e5f6-7a8b-9c0d-1e2f3a4b5c6d",
                  "status": "disponible",
                  "created_at": "2026-06-26T17:00:00.000000Z",
                  "updated_at": "2026-06-26T17:00:00.000000Z"
              }
          ],
          "first_page_url": "http://localhost/api/v1/aid-offers?page=1",
          "from": 1,
          "last_page": 1,
          "last_page_url": "http://localhost/api/v1/aid-offers?page=1",
          "links": [],
          "next_page_url": null,
          "path": "http://localhost/api/v1/aid-offers",
          "per_page": 15,
          "prev_page_url": null,
          "to": 1,
          "total": 1
      }
  }
  ```

#### Obtener Detalle de Oferta
* **URL:** `GET /api/v1/aid-offers/{id}`
* **Auth:** No requiere.
* **Respuestas:**
  * **200 OK:** Retorna el objeto de la oferta bajo la clave `data`.
  * **404 Not Found:** `{"success": false, "message": "Oferta no encontrada"}`

#### Crear Oferta de Ayuda
* **URL:** `POST /api/v1/aid-offers`
* **Auth:** No requiere.
* **Headers:** `Content-Type: application/json`
* **Cuerpo (JSON):**
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
* **Reglas de Validación Estrictas:**
  * `provider_name`: Requerido, string, max 150 caracteres.
  * `dni`: Requerido, string, debe cumplir regex: `/(V|E|J)-\d{5,9}/` (Ej: V-12345678).
  * `phone`: Requerido, string, formato internacional venezolano regex: `/^\+58(412|414|424|416|426)\d{7}$/` (Ej: +584141234567).
  * `title`: Requerido, string, max 150 caracteres.
  * `description`: Requerido, string.
  * `category`: Requerido, string, valores válidos: `comida`, `ropa`, `vivienda`, `salud`, `transporte`, `otros`.
  * `latitude` / `longitude`: Requeridos, numéricos.
  * `address_description`: Requerido, string.
* **Respuesta Exitosa (201 Created):** Retorna la oferta creada con su ID y el `secure_token` único generado por el backend.

#### Modificar Oferta de Ayuda
* **URL:** `PUT /api/v1/aid-offers/{id}`
* **Auth:** **Requerido** -> Header `X-Secure-Token: <secure_token>`
* **Cuerpo (JSON):** Se envían únicamente los campos a modificar.
  ```json
  {
      "title": "Donación de ropa de invierno (Actualizado)",
      "status": "reservado"
  }
  ```
* **Reglas Especiales e Inmutabilidad:**
  * `dni` y `phone` son **inmutables**. Si se envían en el body, la API fallará con un error `422 Unprocessable Entity` indicando que su modificación está prohibida.
  * `status`: Valores válidos: `disponible`, `reservado`, `entregado`, `expirado`.
* **Respuestas:**
  * **200 OK:** Retorna el objeto actualizado en `data`.
  * **403 Forbidden:** Si el header `X-Secure-Token` falta o es incorrecto.
  * **404 Not Found:** Si la oferta no existe.

#### Eliminar Oferta de Ayuda
* **URL:** `DELETE /api/v1/aid-offers/{id}`
* **Auth:** **Requerido** -> Header `X-Secure-Token: <secure_token>`
* **Respuestas:**
  * **200 OK:** `{"success": true, "message": "Oferta eliminada exitosamente"}`
  * **403 Forbidden:** Token incorrecto o faltante.
  * **404 Not Found:** Recurso no existente.

---

### 3.2. Centros de Acopio (`/collection-centers`)

Lugares físicos donde se reciben y distribuyen insumos humanitarios.

#### Listar Centros de Acopio
* **URL:** `GET /api/v1/collection-centers`
* **Auth:** No requiere.
* **Query Parameters (Opcionales - Omitir para mapas):**
  * `page` (opcional, entero): Número de página.
  * `per_page` (opcional, entero): Elementos por página (por defecto `15`).
* **Nota de Comportamiento:** Si se omiten `page` y `per_page`, la respuesta `data` será un Array plano con todos los centros (óptimo para renderizar marcadores en mapas). Si se incluye alguno, la respuesta `data` adoptará la estructura del paginador de Laravel.
* **Detalle Importante para React:** Este endpoint **eager loadea** la relación de insumos (`supplies`). Cada centro devuelto incluirá un array con sus insumos actuales.
* **Respuesta Exitosa (200 OK - Sin paginación activa):**
  ```json
  {
      "success": true,
      "message": "Centros de acopio obtenidos",
      "data": [
          {
              "id": 1,
              "user_id": null,
              "center_name": "Colegio San José",
              "manager_name": "Maria Lopez",
              "dni": "V-87654321",
              "phone": "+584127654321",
              "latitude": 10.49000000,
              "longitude": -66.89000000,
              "address_description": "Entrada principal.",
              "status": "recibiendo_insumos",
              "secure_token": "d8a1c93a-8b1e-450f-a392-411a2f643e2e",
              "created_at": "2026-06-26T17:00:00.000000Z",
              "updated_at": "2026-06-26T17:00:00.000000Z",
              "supplies": [
                  {
                      "id": 5,
                      "collection_center_id": 1,
                      "supply_name": "Agua embotellada",
                      "quantity": "150.00",
                      "unit_of_measurement": "litros",
                      "priority_level": "critico"
                  }
              ]
          }
      ]
  }
  ```

#### Obtener Detalle de un Centro
* **URL:** `GET /api/v1/collection-centers/{id}`
* **Auth:** No requiere.
* **Respuestas:**
  * **200 OK:** Retorna el centro con su relación `supplies` anidada en `data`.
  * **404 Not Found:** `{"success": false, "message": "Centro no encontrado"}`

#### Registrar Centro de Acopio
* **URL:** `POST /api/v1/collection-centers`
* **Auth:** No requiere.
* **Cuerpo (JSON):**
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
* **Validaciones:**
  * Mismas expresiones regulares de DNI (`/(V|E|J)-\d{5,9}/`) y teléfono (`/^\+58(412|414|424|416|426)\d{7}$/`).
  * Todos los campos del cuerpo son requeridos.
* **Respuesta Exitosa (201 Created):** Retorna el centro registrado e incluye su `secure_token` único.

#### Modificar Centro de Acopio
* **URL:** `PUT /api/v1/collection-centers/{id}`
* **Auth:** **Requerido** -> Header `X-Secure-Token: <secure_token>`
* **Cuerpo (JSON):**
  ```json
  {
      "center_name": "Colegio San José - Gimnasio",
      "status": "capacidad_maxima"
  }
  ```
* **Reglas Especiales e Inmutabilidad:**
  * `dni` y `phone` son **inmutables** y están prohibidos (`prohibited`). Si se envían, la API retornará `422 Unprocessable Entity` con un mensaje personalizado.
  * `status`: Valores enums válidos: `recibiendo_insumos`, `capacidad_maxima`, `distribuyendo`, `en_traslado`, `inactivo`.
* **Respuestas:**
  * **200 OK:** Retorna el objeto del centro actualizado.
  * **403 Forbidden:** Token incorrecto o faltante.
  * **404 Not Found:** Centro no existente.

#### Eliminar Centro de Acopio
* **URL:** `DELETE /api/v1/collection-centers/{id}`
* **Auth:** **Requerido** -> Header `X-Secure-Token: <secure_token>`
* **Respuestas:**
  * **200 OK:** `{"success": true, "message": "Centro eliminado exitosamente"}`
  * **403 Forbidden:** Token incorrecto o faltante.

---

### 3.3. Incidentes e Infraestructura Afectada (`/incidents`)

Reportes de daños estructurales, colapsos viales o emergencias climáticas.

#### Listar Incidentes
* **URL:** `GET /api/v1/incidents`
* **Auth:** No requiere.
* **Query Parameters (Opcionales - Omitir para mapas):**
  * `page` (opcional, entero): Número de página.
  * `per_page` (opcional, entero): Elementos por página (por defecto `15`).
* **Nota de Comportamiento:** Si se omiten `page` y `per_page`, la respuesta `data` será un Array plano con todos los incidentes (óptimo para renderizar marcadores en mapas). Si se incluye alguno, la respuesta `data` adoptará la estructura del paginador de Laravel.
* **Respuesta Exitosa (200 OK - Sin paginación activa):** Retorna una lista plana con todos los incidentes registrados para su renderizado en mapa.

#### Obtener Detalle de un Incidente
* **URL:** `GET /api/v1/incidents/{id}`
* **Auth:** No requiere.

#### Reportar Incidente
* **URL:** `POST /api/v1/incidents`
* **Auth:** No requiere.
* **Cuerpo (JSON):**
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
* **Validaciones:**
  * `reporter_name`: Opcional, string, max 150 (se guardará como "Anónimo" si no se envía).
  * `description`: Requerido, string.
  * `latitude` / `longitude`: Requeridos, numéricos.
  * `address_description`: Requerido, string.
  * `severity`: Opcional, string, enums: `leve`, `moderado`, `critico`, `catastrofico`. (Por defecto se asigna `moderado`).

---

### 3.4. Ayuda Internacional (`/international-aid`)

Registro oficial de ayuda proveniente del exterior (países, ONG extranjeras).

#### Listar Ayuda Internacional
* **URL:** `GET /api/v1/international-aid`
* **Auth:** No requiere.
* **Query Parameters:**
  * `page` (opcional, entero): Número de página (por defecto `1`).
  * `per_page` (opcional, entero): Elementos por página (por defecto `15`).
* **Respuesta Exitosa (200 OK):** Retorna el paginador con la forma: `{"success": true, "data": { "current_page": 1, "data": [...], "total": 4 }}`.

#### Registrar Ayuda Internacional
* **URL:** `POST /api/v1/international-aid`
* **Auth:** No requiere (Fase I).
* **Cuerpo (JSON):**
  ```json
  {
      "country_name": "Colombia",
      "iso_code": "COL",
      "aid_type": "Rescate e Insumos",
      "description": "2 equipos de búsqueda y rescate USAR."
  }
  ```
* **Validaciones:**
  * `country_name`: Requerido, string, max 100.
  * `iso_code`: Requerido, string, exactamente de longitud 3 (`size:3`), **debe ser único** en la base de datos (ej. COL, USA, ESP).
  * `aid_type`: Requerido, string, max 100 (ej. "Médica", "Rescate", "Alimentos").
  * `description`: Opcional, string.
* **Respuestas:**
  * **201 Created:** Si el registro fue exitoso.
  * **422 Unprocessable Entity:** Si el `iso_code` no es de 3 letras o ya está registrado (ej. violación de unicidad).

---

### 3.5. Red de Hospitales (`/hospitals`)

Gestión y estado operativo de centros de salud y carpas de campaña en la emergencia.

#### Listar Hospitales
* **URL:** `GET /api/v1/hospitals`
* **Auth:** No requiere.
* **Query Parameters (Opcionales - Omitir para mapas):**
  * `page` (opcional, entero): Número de página.
  * `per_page` (opcional, entero): Elementos por página (por defecto `15`).
* **Nota de Comportamiento:** Si se omiten `page` y `per_page`, la respuesta `data` será un Array plano con todos los hospitales (óptimo para renderizar marcadores en mapas). Si se incluye alguno, la respuesta `data` adoptará la estructura del paginador de Laravel.
* **Respuesta Exitosa (200 OK - Sin paginación activa):** Retorna una lista plana de centros médicos con sus coordenadas, tipo y estado operativo.

#### Registrar Hospital/Centro Médico
* **URL:** `POST /api/v1/hospitals`
* **Auth:** No requiere (Fase I).
* **Headers:** `Content-Type: multipart/form-data` (Requerido para el envío de imágenes).
* **Cuerpo (FormData):**
  * `name`: Requerido, string, max 150.
  * `type`: Opcional, string, enums: `hospital_publico`, `clinica_privada`, `carpa_campana`.
  * `latitude` / `longitude`: Requeridos, numéricos.
  * `address_description`: Requerido, string.
  * `status`: Opcional, string, enums: `operativo`, `colapsado`, `sin_insumos`, `evacuado`.
  * `contact_phone`: Opcional, string, max 20.
  * `image`: Opcional, Archivo de Imagen (JPG, PNG, WEBP), max 5MB (5120 KB).
* **Manejo de Duplicados (Importante para React):**
  * El backend tiene una regla de unicidad estricta por Nombre **o** por Coordenadas Exactas.
  * Si se intenta registrar un hospital con un nombre ya existente, o uno en la misma latitud/longitud exacta, el servidor responde con **`409 Conflict`**:
    `{"success": false, "message": "El hospital ya se encuentra registrado o hay uno en las mismas coordenadas exactas."}`
* **Respuesta Exitosa (201 Created):** Retorna los datos del hospital incluyendo el campo `image_url` si se subió un archivo (almacenado en Cloudflare R2).

---

### 3.6. Pacientes (`/patients`)

Registro de personas ingresadas en centros médicos de emergencia para facilitar la búsqueda y reunificación familiar.

#### Buscar Pacientes
* **URL:** `GET /api/v1/patients/search`
* **Auth:** No requiere.
* **Detalle para React:** Este endpoint **eager loadea** los datos del hospital asociado. Cada paciente retornado tendrá un objeto `hospital` adentro.
* **Query Parameters (Al menos uno sugerido para el buscador):**
  * `dni` (opcional): Búsqueda exacta por DNI (ej: `V-12345678`).
  * `name` (opcional): Búsqueda parcial (`ILIKE`) en nombre o apellido.
  * `description` (opcional): Búsqueda parcial en la descripción física.
  * `page` (opcional, entero): Número de página (por defecto `1`).
  * `per_page` (opcional, entero): Elementos por página (por defecto `15`).
* **Respuesta Exitosa (200 OK):**
  ```json
  {
      "success": true,
      "data": {
          "current_page": 1,
          "data": [
              {
                  "id": 1,
                  "hospital_id": 3,
                  "dni": "V-12345678",
                  "first_name": "Carlos",
                  "last_name": "Mendoza",
                  "approximate_age": 35,
                  "physical_description": "Tatuaje de estrella en el antebrazo derecho, usa anteojos.",
                  "medical_status": "estable",
                  "photo_url": "https://r2.juntosvnzl.org/patients/carlos_mendoza.jpg",
                  "created_at": "2026-06-26T17:00:00.000000Z",
                  "updated_at": "2026-06-26T17:00:00.000000Z",
                  "hospital": {
                      "id": 3,
                      "name": "Hospital Universitario de Caracas",
                      "latitude": 10.48900000,
                      "longitude": -66.89000000,
                      "status": "operativo"
                  }
              }
          ],
          "first_page_url": "http://localhost/api/v1/patients/search?page=1",
          "from": 1,
          "last_page": 1,
          "last_page_url": "http://localhost/api/v1/patients/search?page=1",
          "links": [],
          "next_page_url": null,
          "path": "http://localhost/api/v1/patients/search",
          "per_page": 15,
          "prev_page_url": null,
          "to": 1,
          "total": 1
      }
  }
  ```

#### Registrar Paciente
* **URL:** `POST /api/v1/patients`
* **Auth:** No requiere.
* **Headers:** `Content-Type: multipart/form-data`
* **Cuerpo (FormData):**
  * `hospital_id`: Requerido, debe existir en la tabla de hospitales.
  * `dni`: Opcional, string, max 20.
  * `first_name` / `last_name`: Opcionales, string, max 100.
  * `approximate_age`: Opcional, entero.
  * `physical_description`: Opcional, string (crucial para personas inconscientes o sin identificación).
  * `medical_status`: Opcional, string, enums: `estable`, `critico`, `observacion`, `dado_de_alta`, `fallecido`, `trasladado`.
  * `photo`: Opcional, Archivo de Imagen (JPG, PNG, WEBP), max 5MB (5120 KB).
* **Manejo de Conflictos y Duplicados:**
  * Si se proporciona `dni`, la API verifica que no haya otro paciente con el mismo DNI. Si ya existe, retorna **`409 Conflict`**:
    `{"success": false, "message": "Ya existe un paciente registrado con ese DNI."}`
  * Si **no** se proporciona DNI, pero se envían `first_name` y `last_name`, el backend valida que no exista otro paciente con el mismo nombre y apellido *en ese mismo hospital*. Si coincide, retorna **`409 Conflict`**:
    `{"success": false, "message": "Ya existe un paciente con ese nombre en este hospital."}`
* **Respuesta Exitosa (201 Created):** Retorna el paciente creado con su `photo_url` de Cloudflare R2 si aplica.

---

### 3.7. Módulo de Autenticación (`/auth` - Fase II)

Maneja el registro e inicio de sesión de coordinadores y voluntarios.

#### Registrar Usuario Administrativo/Voluntario
* **URL:** `POST /api/v1/auth/register`
* **Auth:** No requiere (público).
* **Cuerpo (JSON):**
  ```json
  {
      "name": "Maria Alejandra",
      "dni": "V-12345678",
      "phone": "+584121234567",
      "email": "maria@juntosvnzl.org",
      "password": "mi_clave_segura_123"
  }
  ```
* **Validaciones:**
  * `name`: Requerido, string, max 150.
  * `dni`: Requerido, regex `/(V|E|J)-\d{5,9}/`, **debe ser único** en la tabla `users`.
  * `phone`: Requerido, regex `/^\+58(412|414|424|416|426)\d{7}$/`, **debe ser único** en `users`.
  * `email`: Opcional (nullable), email válido, max 150, **debe ser único** en `users`.
  * `password`: Requerido, string, mínimo 6 caracteres.
* **Respuesta Exitosa (201 Created):**
  ```json
  {
      "success": true,
      "message": "Usuario registrado exitosamente",
      "data": {
          "user": {
              "id": 4,
              "name": "Maria Alejandra",
              "dni": "V-12345678",
              "phone": "+584121234567",
              "email": "maria@juntosvnzl.org",
              "role": "volunteer",
              "created_at": "2026-06-26T17:00:00.000000Z",
              "updated_at": "2026-06-26T17:00:00.000000Z"
          },
          "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjQs..." // Token JWT emitido
      }
  }
  ```

#### Inicio de Sesión
* **URL:** `POST /api/v1/auth/login`
* **Auth:** No requiere.
* **Cuerpo (JSON):**
  ```json
  {
      "dni": "V-12345678",
      "password": "mi_clave_segura_123"
  }
  ```
* **Respuestas:**
  * **200 OK:** Retorna el token JWT y los datos del usuario.
    ```json
    {
        "success": true,
        "message": "Login exitoso",
        "data": {
            "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
            "user": {
                "id": 4,
                "name": "Maria Alejandra",
                "dni": "V-12345678",
                "phone": "+584121234567",
                "email": "maria@juntosvnzl.org",
                "role": "volunteer"
            }
        }
    }
    ```
  * **401 Unauthorized:** Si las credenciales fallan:
    `{"success": false, "message": "Credenciales inválidas"}`

---

## 4. Guía de Integración Rápida para React

1. **Subida de Archivos (Imágenes/Fotos):**
   * En endpoints como `/hospitals` y `/patients`, no envíes un JSON con el archivo codificado en Base64. Envía un objeto `FormData`.
   * Ejemplo de Axios en React:
     ```javascript
     const formData = new FormData();
     formData.append('name', name);
     formData.append('latitude', lat);
     formData.append('longitude', lng);
     formData.append('address_description', addr);
     if (selectedFile) {
         formData.append('image', selectedFile); // Tipo File obtenido de un input type="file"
     }

     const response = await axios.post('/api/v1/hospitals', formData, {
         headers: {
             'Content-Type': 'multipart/form-data'
         }
     });
     ```
2. **Gestión de tokens locales (`X-Secure-Token`):**
   * Al recibir una respuesta exitosa al crear una oferta o centro de acopio, guardá el token de esta manera:
     ```javascript
     localStorage.setItem(`secure_token_offer_${data.id}`, data.secure_token);
     ```
   * Cuando el usuario quiera editar esa misma oferta, recuperá el token e inyectalo en el header:
     ```javascript
     const token = localStorage.getItem(`secure_token_offer_${id}`);
     await axios.put(`/api/v1/aid-offers/${id}`, payload, {
         headers: { 'X-Secure-Token': token }
     });
     ```
3. **Manejo de validaciones regex:**
   * Validá en el frontend con las mismas expresiones regulares para evitar peticiones fallidas innecesarias:
     * **DNI:** `const dniRegex = /^(V|E|J)-\d{5,9}$/;`
     * **Teléfono:** `const phoneRegex = /^\+58(412|414|424|416|426)\d{7}$/;`
