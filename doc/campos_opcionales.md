# Guía de Campos Opcionales y Nullables (Requeridos vs. Vacíos)

Este documento detalla cuáles campos pueden ser enviados como `null` o ser omitidos en las peticiones del frontend, y cuáles son estrictamente obligatorios (`required`). 

---

## 1. Resumen Rápido por Recurso (Creación - POST)

| Recurso / Endpoint | Campo | ¿Omitible / Nullable? | Comportamiento si es nulo / omitido |
| :--- | :--- | :---: | :--- |
| **Ofertas de Ayuda** (`/aid-offers`) | `user_id` | Sí | Se asume como oferta anónima (Fase I). |
| | Todos los demás campos | No | La API falla con `422 Unprocessable Entity`. |
| **Centros de Acopio** (`/collection-centers`) | `user_id` | Sí | No se vincula a ningún usuario gestor inicial. |
| | Todos los demás campos | No | Requeridos para geolocalización y contacto. |
| **Incidentes** (`/incidents`) | `reporter_name` | Sí | Se autoguarda en la base de datos como `"Anónimo"`. |
| | `severity` | Sí | Toma el valor por defecto `"moderado"`. |
| | `description`, `latitude`, `longitude`, `address_description` | No | Requeridos para ubicar y describir el siniestro. |
| **Ayuda Internacional** (`/international-aid`) | `description` | Sí | Se almacena como `null`. |
| | `country_name`, `iso_code`, `aid_type` | No | El `iso_code` debe tener exactamente 3 caracteres. |
| **Hospitales** (`/hospitals`) | `type` | Sí | Se autoguarda por defecto como `"hospital_publico"`. |
| | `status` | Sí | Se autoguarda por defecto como `"operativo"`. |
| | `contact_phone` | Sí | Se almacena como `null`. |
| | `image` (Archivo) | Sí | No se sube nada a Cloudflare R2; `image_url` queda nulo. |
| | `name`, `latitude`, `longitude`, `address_description` | No | Requeridos. El nombre o coordenadas duplicadas dan `409`. |
| **Pacientes** (`/patients`) | `dni` | Sí | Paciente ingresado sin identificación (indocumentado). |
| | `first_name` | Sí | Paciente inconsciente o no identificado. |
| | `last_name` | Sí | Paciente inconsciente o no identificado. |
| | `approximate_age` | Sí | Se almacena como `null`. |
| | `physical_description` | Sí | Se almacena como `null` (se recomienda enviar descripción si no hay nombre). |
| | `medical_status` | Sí | Toma el valor por defecto `"estable"`. |
| | `photo` (Archivo) | Sí | No se genera url de foto; `photo_url` queda nulo. |
| | `arrival_date` | Sí | Si se omite, toma la fecha y hora actuales del servidor. |
| | `hospital_id` | No | **Único campo obligatorio**. El paciente debe estar en un hospital. |
| **Registro de Usuario** (`/auth/register`) | `email` | Sí | Se almacena como `null` (opcional en Fase I). |
| | `name`, `dni`, `phone`, `password` | No | Requeridos. DNI y teléfono deben cumplir regex y ser únicos. |

---

## 2. Detalle de Lógica y Validaciones en el Servidor

### 2.1. Pacientes (`POST /patients`)
El registro de pacientes es el recurso más flexible debido al contexto de desastre:
* **Caso Indocumentado/Inconsciente:** El frontend puede enviar únicamente `{ "hospital_id": 3, "physical_description": "Hombre joven con franela roja..." }`. La API lo aceptará con éxito (`201 Created`).
* **Regla de Conflictos (409):**
  * Si envías `dni`, debe ser único en toda la tabla.
  * Si **no** envías `dni` pero sí `first_name` y `last_name`, la combinación nombre+apellido debe ser única **dentro del hospital especificado** para evitar registros duplicados accidentales en la misma sala de emergencias.

### 2.2. Incidentes (`POST /incidents`)
* **Nombre de Reportante:** Si el usuario prefiere anonimato o el frontend no envía el campo `reporter_name`, la API asigna automáticamente el string `"Anónimo"`.

### 2.3. Hospitales (`POST /hospitals`)
* **Imagen del Centro:** El campo `image` es opcional en la petición de tipo `multipart/form-data`. Si no se adjunta, el servidor no interactúa con Cloudflare R2 y almacena el registro con `image_url` en `null`.

### 2.4. Ofertas y Centros (`PUT /aid-offers/{id}` y `PUT /collection-centers/{id}`)
* **Campos Prohibidos en Modificación:**
  * Al hacer un `PUT` de actualización, los campos `dni` y `phone` están marcados como `prohibited`.
  * Esto significa que **deben ser omitidos por completo** de la petición. Si se envían (incluso con sus valores originales o como `null`), el validador de Laravel rechazará la petición con un error `422 Unprocessable Entity`.
