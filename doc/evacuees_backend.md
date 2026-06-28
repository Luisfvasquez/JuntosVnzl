# Módulo de Damnificados (Evacuees) - Backend API

Se ha creado la base de datos y la API para el manejo de damnificados/refugiados. Este módulo está diseñado para registrar personas afectadas por el desastre que necesitan ayuda, refugio o reunificación familiar, asumiendo que **muchos de los datos pueden no estar disponibles inicialmente**, por lo que casi todos los campos son opcionales (`nullable`), excepto el nombre.

## Esquema del Modelo (Campos Disponibles)
Todos los campos (excepto `first_name`) pueden ser omitidos o enviados como `null`:

- `first_name` (String, Obligatorio)
- `last_name` (String)
- `dni` (String) - Cédula o pasaporte.
- `age` (Entero)
- `gender` (String) - `masculino`, `femenino`, `otro`
- `current_location` (String) - Texto libre indicando dónde se encuentra (Ej. "Refugio Santa Ana", "Casa de familiar").
- `latitude` (Decimal) - Latitud para mostrar en un mapa.
- `longitude` (Decimal) - Longitud para mostrar en un mapa.
- `origin_address` (String) - Dirección original de donde fue evacuado.
- `special_conditions` (Texto) - Condiciones especiales (ej. "Embarazada", "Adulto mayor").
- `minor_medical_conditions` (Texto) - Alergias o condiciones crónicas que requieren atención pero no hospitalización (ej. "Hipertensión").
- `immediate_needs` (Texto) - Qué necesita con urgencia (ej. "Ropa talla M", "Fórmula infantil").
- `dependents_count` (Entero) - Cuántos familiares/personas dependen de él/ella en el refugio.
- `looking_for` (Texto) - Nombres de familiares que está buscando o de los que se separó.
- `status` (String) - `en_refugio`, `reubicado`, `desaparecido`, `fallecido`.
- `photo` (Archivo/Imagen) - Foto de la persona para facilitar la búsqueda.

## Endpoints

Las rutas requieren el token de autenticación (Bearer Token) ya que están bajo `auth:api`.

### 1. Listar / Buscar Damnificados
**`GET /api/v1/evacuees/search`**

**Filtros aceptados (query params):**
- `name`: Busca coincidencias en nombre o apellido.
- `dni`: Busca por DNI exacto.
- `status`: Filtra por estado (ej. `en_refugio`).
- `current_location`: Busca coincidencias en la ubicación.
- `per_page`: Paginación (por defecto 15).

### 2. Ver Detalle de un Damnificado
**`GET /api/v1/evacuees/{id}`**

Devuelve el objeto completo del damnificado.

### 3. Crear un Damnificado (con foto)
**`POST /api/v1/evacuees`**

Dado que incluye la subida de foto, debes usar `multipart/form-data` (FormData en el frontend). Todos los campos listados arriba se pueden enviar, pero solo `first_name` es estrictamente requerido.

### 4. Actualizar un Damnificado (con foto)
**`PUT /api/v1/evacuees/{id}`**

Igual que el POST. Al enviar archivos (fotos) a través de Axios usando `FormData`, recuerda que el navegador/PHP no procesan bien los archivos por PUT, por lo que **DEBES** hacer una petición POST y enviar un campo oculto llamado `_method` con valor `PUT`:

```typescript
formData.append('_method', 'PUT');
// enviar por axios.post('/api/v1/evacuees/' + id, formData)
```

### 5. Eliminar un Damnificado
**`DELETE /api/v1/evacuees/{id}`**

*(Nota: Solo los usuarios con rol `superadmin` tienen permiso para eliminar registros en este endpoint).*

---

### Tareas para el Agente Frontend:
1. Crear el servicio de API `src/api/evacuees.ts`.
2. Crear la vista `EvacueesPage.tsx` con una tabla o grid de tarjetas para mostrar los damnificados.
3. Crear el formulario de registro/edición. Considerar un diseño que incluya un selector de mapa (para poblar lat/lng y `current_location` si es posible).
4. No exigir al usuario rellenar todos los campos del formulario, configurar el Yup / validación para que solo `first_name` sea requerido.
