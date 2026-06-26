# Documento de Contexto y Arquitectura Backend (API)
## Plataforma de Coordinación Humanitaria (Venezuela)

## 1. Propósito del Proyecto (Visión Backend)
Desarrollo de una **API RESTful** robusta, escalable y segura, diseñada para ser el motor de procesamiento de datos en la respuesta a emergencias. El backend se encarga exclusivamente de la lógica de negocio, validación estricta de identidades, almacenamiento geoespacial y distribución de eventos en tiempo real, operando de forma completamente agnóstica al frontend (desacoplada).

## 2. Estrategia de la API por Fases

### Fase I: Endpoints Públicos de Emergencia (Zero-Login)
* **Objetivo:** Recolección rápida de datos con la menor fricción posible.
* **Mecanismo de Autorización Local:** Los endpoints de creación (`POST /api/v1/aid-offers`, `POST /api/v1/collection-centers`) generan un `secure_token` (UUIDv4) en la respuesta JSON. El cliente debe enviar este token en el header (ej. `X-Secure-Token`) para peticiones `PUT` o `DELETE` sobre el recurso creado.
* **Seguridad API:**
  - Implementación intensiva de `ThrottleRequests` (Rate Limiting) por IP para mitigar ataques DoS.
  - Middlewares de saneamiento de datos para evitar inyección SQL o XSS en las descripciones.

### Fase II: Endpoints Autenticados y Consolidación
* **Autenticación:** Implementación de Laravel Sanctum o JWT (Tymon JWTAuth) para la emisión de Bearer Tokens (`POST /api/v1/auth/login`).
* **Lógica de Migración (Observer):** Al registrar un usuario exitosamente (`POST /api/v1/auth/register`), el backend ejecuta un proceso asíncrono para vincular los registros de la Fase I (huérfanos) al nuevo `user_id`, cruzando la información del DNI y número de teléfono.

## 3. Stack Tecnológico del Backend
* **Framework:** Laravel 13 (Modo API estricto).
* **Base de Datos:** PostgreSQL.
  * Uso de tipos de datos `NUMERIC(10,8)` para coordenadas.
  * Índices combinados y espaciales para optimizar consultas de proximidad.
* **Gestión de Colas (Background Jobs):** Redis junto con Laravel Horizon. Vital para el procesamiento de imágenes, envío de notificaciones o recálculo de métricas de los centros de acopio sin bloquear la respuesta de la API.
* **WebSockets / Tiempo Real:** Laravel Reverb. Los endpoints emitirán eventos (`broadcast`) cuando un centro de acopio cambie de estado o se reporte un incidente crítico, permitiendo a los clientes actualizar sus interfaces al instante.

## 4. Estructura Principal de Recursos (Endpoints Base)
* `/api/v1/aid-offers` (Ofertas de Ayuda)
* `/api/v1/collection-centers` (Centros de Acopio y sus insumos)
* `/api/v1/incidents` (Reportes de derrumbes/afectaciones)
* `/api/v1/international-aid` (Trazabilidad de ayuda internacional)

## 5. Reglas Críticas de Negocio en la API
* **Form Requests (Validación Estricta):**
  * DNI: Regla regex obligatoria `/(V|E|J)-\d{5,9}/`.
  * Teléfonos: Regla regex obligatoria `/^\+58(412|414|424|416|426)\d{7}$/`.
* **Inmutabilidad:** Una vez un recurso es creado en Fase I, los campos `dni` y `phone` no pueden actualizarse vía `PUT/PATCH`. El intento de modificación debe retornar un `422 Unprocessable Entity` o `403 Forbidden`.
* **Respuestas Estandarizadas:** Todas las salidas deben seguir un formato JSON uniforme, por ejemplo:
  ```json
  {
    "success": true,
    "message": "Operación exitosa",
    "data": { ... }
  }
  ```
