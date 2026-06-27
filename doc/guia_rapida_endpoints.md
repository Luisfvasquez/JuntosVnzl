# Guía Rápida - Referencia de Endpoints y Filtros de la API

Esta es una hoja de ruta resumida con todos los endpoints disponibles en el backend `/api/v1`, su propósito y los filtros (query parameters) admitidos.

---

## 1. Módulo: Ofertas de Ayuda (`/aid-offers`)
Gestión de donaciones y ofrecimientos de voluntarios (comida, ropa, transporte, salud, etc.).

| Método | Endpoint | Propósito | Headers | Filtros (Query Params) |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/aid-offers` | Listar todas las ofertas de ayuda de forma paginada. | Ninguno | `page` (entero): Nro de página.<br>`per_page` (entero): Cantidad por página (default 15). |
| **GET** | `/aid-offers/{id}` | Obtener el detalle de una oferta de ayuda específica. | Ninguno | Ninguno |
| **POST** | `/aid-offers` | Crear una nueva oferta de ayuda de forma anónima (Fase I). | `Content-Type: application/json` | Ninguno |
| **PUT** | `/aid-offers/{id}` | Actualizar campos de una oferta (DNI y teléfono son inmutables). | `X-Secure-Token` (requerido) | Ninguno |
| **DELETE** | `/aid-offers/{id}` | Eliminar una oferta de ayuda física. | `X-Secure-Token` (requerido) | Ninguno |

---

## 2. Módulo: Centros de Acopio (`/collection-centers`)
Puntos físicos de recolección, almacenamiento y distribución de insumos en la emergencia.

| Método | Endpoint | Propósito | Headers | Filtros (Query Params) |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/collection-centers` | Listar centros. Retorna insumos (`supplies`) eagerloadeados. | Ninguno | `page` (opcional, entero): Activa paginación.<br>`per_page` (opcional, entero): Cantidad por página.<br>*Nota: Omitir filtros para obtener array plano (para mapas).* |
| **GET** | `/collection-centers/{id}` | Obtener detalle de un centro y sus insumos. | Ninguno | Ninguno |
| **POST** | `/collection-centers` | Registrar un centro de acopio de forma anónima (Fase I). | `Content-Type: application/json` | Ninguno |
| **PUT** | `/collection-centers/{id}` | Actualizar un centro de acopio (DNI y teléfono inmutables). | `X-Secure-Token` (requerido) | Ninguno |
| **DELETE** | `/collection-centers/{id}` | Eliminar un centro de acopio físico. | `X-Secure-Token` (requerido) | Ninguno |

---

## 3. Módulo: Incidentes e Infraestructura (`/incidents`)
Reporte vecinal y oficial de derrumbes, vías colapsadas e inundaciones en tiempo real.

| Método | Endpoint | Propósito | Headers | Filtros (Query Params) |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/incidents` | Listar todos los incidentes registrados en el mapa. | Ninguno | `page` (opcional, entero): Activa paginación.<br>`per_page` (opcional, entero): Cantidad por página.<br>*Nota: Omitir filtros para obtener array plano (para mapas).* |
| **GET** | `/incidents/{id}` | Obtener la información detallada de un incidente. | Ninguno | Ninguno |
| **POST** | `/incidents` | Reportar un nuevo incidente (permite reporte anónimo). | `Content-Type: application/json` | Ninguno |

---

## 4. Módulo: Ayuda Internacional (`/international-aid`)
Trazabilidad de cargamentos y delegaciones de asistencia extranjera.

| Método | Endpoint | Propósito | Headers | Filtros (Query Params) |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/international-aid` | Listar cargamentos internacionales registrados (paginado). | Ninguno | `page` (entero): Nro de página.<br>`per_page` (entero): Cantidad por página (default 15). |
| **POST** | `/international-aid` | Registrar el ingreso de ayuda extranjera (Fase I). | `Content-Type: application/json` | Ninguno |

---

## 5. Módulo: Salud y Hospitales (`/hospitals`)
Centros asistenciales operativos, saturados o carpas sanitarias instaladas.

| Método | Endpoint | Propósito | Headers | Filtros (Query Params) |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/hospitals` | Listar hospitales activos y su nivel operativo. | Ninguno | `page` (opcional, entero): Activa paginación.<br>`per_page` (opcional, entero): Cantidad por página.<br>*Nota: Omitir filtros para obtener array plano (para mapas).* |
| **POST** | `/hospitals` | Registrar un centro de salud o carpa médica con foto (R2). | `Content-Type: multipart/form-data` | Ninguno |

---

## 6. Módulo: Pacientes y Búsqueda (`/patients`)
Censo de personas en centros de salud para la reunificación de familiares extraviados.

| Método | Endpoint | Propósito | Headers | Filtros (Query Params) |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/patients/search` | Buscar pacientes. Eager loadea la relación de `hospital`. | Ninguno | **Filtros de búsqueda (al menos uno sugerido):**<br>`dni` (string): Búsqueda exacta de DNI.<br>`name` (string): Parcial (`ILIKE`) en nombre o apellido.<br>`description` (string): Parcial en descripción física.<br>**Paginación (Por defecto activa):**<br>`page` (entero): Nro de página.<br>`per_page` (entero): Cantidad por página (default 15). |
| **POST** | `/patients` | Registrar el ingreso de un paciente (identificado o anónimo) con foto (R2). | `Content-Type: multipart/form-data` | Ninguno |

---

## 7. Módulo: Autenticación (`/auth` - Fase II)
Registro e inicio de sesión seguro para coordinadores de zona y administradores de la red.

| Método | Endpoint | Propósito | Headers | Filtros (Query Params) |
| :--- | :--- | :--- | :--- | :--- |
| **POST** | `/auth/register` | Registrar un nuevo usuario (voluntario por defecto) con DNI único. | `Content-Type: application/json` | Ninguno |
| **POST** | `/auth/login` | Iniciar sesión y obtener el Bearer Token (JWT). | `Content-Type: application/json` | Ninguno |
