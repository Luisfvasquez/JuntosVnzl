# Documentación del Módulo: Edificios Dañados (Damaged Buildings)

El módulo de Edificios Dañados permite registrar, consultar, actualizar y evaluar de forma técnica infraestructuras que han sufrido daños. Se han implementado nuevas características para incluir verificaciones, imágenes de respaldo (hasta 10) y soporte para dictámenes de ingeniería civil.

## 1. Base de Datos (Migraciones y Tablas)

### 1.1 Tabla `damaged_buildings`
Almacena la ubicación geográfica de la estructura y detalles generales del reporte.
- **Nuevos Campos Agregados**:
  - `image_urls` (JSON, nullable): Arreglo de strings que almacena las URLs de las imágenes (máximo 10) que respaldan el reporte del edificio dañado.
  - `is_verified` (Boolean, default: `false`): Determina si el reporte ha sido verificado como legítimo/revisado.

### 1.2 Tabla `damaged_building_evaluations`
Almacena las evaluaciones técnicas emitidas por ingenieros civiles para un edificio en particular.
- **Campos Principales**:
  - `damaged_building_id` (FK): Relación en cascada con el edificio afectado.
  - `evaluator_name` (String): Nombre del ingeniero o inspector técnico.
  - `evaluator_credential` (String, nullable): Número de licencia / CIV.
  - `structural_damage` (String): Nivel de daño estructural (ej. `severo`, `moderado`, `ninguno`).
  - `habitability_status` (String): Estatus de habitabilidad (ej. `inhabitable`, `restringido`).
  - `collapse_risk` (String): Riesgo inminente de colapso.
  - `recommended_actions` (Text, nullable): Acciones sugeridas (ej. `apuntalamiento temporal`).
  - `comments` (Text, nullable): Notas extendidas del inspector.
  - `is_verifiable` (Boolean, nullable): Indica si el comentario del ingeniero/información anexa está sujeto a poder ser verificable por un tercero.
  - `is_verified` (Boolean, default: `false`): Validación de que la evaluación del ingeniero es legítima.
  - `image_urls` (JSON, nullable): Archivos fotográficos (hasta 10) respaldando el informe, subidos en la misma solicitud.

## 2. Endpoints: Edificios Dañados (Actualizados)

### Crear / Actualizar Edificio Dañado (`/api/v1/damaged-buildings`)
- **Imágenes Soportadas (`POST` / `PUT`)**: Ahora se puede enviar un array de archivos en `images[]` vía `multipart/form-data` (máximo 10 imágenes por request).
- **Procesamiento Óptimo**: Todas las imágenes enviadas son procesadas y convertidas a `.webp` con calidad `80%` vía la librería *Intervention Image* antes de ser enviadas a Cloudflare R2 / S3, evitando llenar el bucket.
- **Validación del Boolean `is_verified`**: Disponible de manera opcional.
- **Actualización de Imágenes (`PUT` / `PATCH`)**: Se agregó soporte para retener, reemplazar o eliminar imágenes existentes. El endpoint acepta un arreglo `retained_image_urls[]` con las URLs exactas que el usuario desea conservar. Las URLs que no vengan en este arreglo se descartarán, y las nuevas imágenes enviadas en `images[]` se agregarán a la lista retenida (respetando el máximo global de 10).

## 3. Endpoints: Evaluaciones de Ingeniería

Estos son los nuevos endpoints creados para el sub-módulo de evaluaciones (bajo el prefijo `/api/v1/damaged-buildings/{id}/evaluations` o similar):

### Obtener Evaluaciones (Público)
- **Método**: `GET`
- **Ruta**: `/damaged-buildings/{buildingId}/evaluations`
- **Retorno**: Todas las evaluaciones técnicas asociadas a un edificio en específico.

### Emitir Evaluación Técnica (Público / Por definir según rol frontend)
- **Método**: `POST`
- **Ruta**: `/damaged-buildings/{buildingId}/evaluations`
- **Payload (`multipart/form-data`)**:
  - Todos los campos técnicos (`structural_damage`, `habitability_status`, etc.) detallados arriba.
  - `images[]` (Opcional): Hasta 10 imágenes para respaldar el informe (convertidas automáticamente a webp).

### Actualizar / Eliminar Evaluación (Protegido por JWT)
- **Métodos**: `PUT` / `DELETE`
- **Ruta**: `/damaged-buildings/evaluations/{evaluationId}`
- **Descripción**: Permite editar o borrar un informe existente. 
  - **Manejo de Imágenes en Update**: Al igual que en el reporte principal, acepta el campo `retained_image_urls[]` para mantener las imágenes existentes que el usuario no desea borrar, además de procesar nuevas fotos enviadas en `images[]`.
  - El límite estricto se mantiene en 10 fotos en total por evaluación.
