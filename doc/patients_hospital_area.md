# Actualización del Backend: Área de Hospitalización en Pacientes

Se ha agregado un nuevo campo a la tabla de pacientes para especificar en qué área del hospital se encuentra internado o siendo atendido (por ejemplo: Trauma Shock, Neumonía, Pediatría, etc.).

## Cambios en la API

El endpoint `POST /api/v1/patients` (Crear) y el `PUT /api/v1/patients/{id}` (Actualizar) ahora aceptan el campo `hospital_area`.

### Nuevo Campo
- **`hospital_area`** (String, opcional, max: 150 caracteres)
  - Representa el área, pabellón o departamento donde se encuentra el paciente.
  - Ejemplos válidos: "Trauma Shock", "Pediatría", "UCI", "Sala de Espera", "Neumonía".

### Ejemplo de Petición (Crear Paciente)
```json
{
  "hospital_id": 1,
  "first_name": "Juan",
  "last_name": "Pérez",
  "medical_status": "observacion",
  "hospital_area": "Trauma Shock"
}
```

Al utilizar `FormData` (por si se suben fotos), simplemente hacer un `formData.append('hospital_area', 'Trauma Shock')`.

### Estructura de Respuesta
Cuando consultas los pacientes (`GET /api/v1/patients/search`), el objeto de cada paciente ahora devolverá este nuevo campo:
```json
{
  "id": 12,
  "hospital_id": 1,
  "first_name": "Juan",
  "last_name": "Pérez",
  "medical_status": "observacion",
  "hospital_area": "Trauma Shock",
  "created_at": "...",
  ...
}
```

## Notas para el Agente Frontend
1. **Modificar el Modelo TypeScript:**
   Abre el archivo `src/api/patients.ts` y agrega `hospital_area?: string;` a la interfaz `Patient`.
2. **Actualizar el Formulario (`PatientsPage.tsx`):**
   - Agrega un nuevo estado o campo al `form` existente para `hospital_area`.
   - Coloca un input de texto en la vista del modal de registro/edición, quizás debajo de "Hospital / Centro de Salud" o en la misma fila del estado de salud, solicitando el área (Ej. "Área / Departamento").
   - Asegúrate de incluir `formData.append('hospital_area', form.hospital_area)` al momento de hacer el submit.
3. **Mostrar en la UI:**
   En la tarjeta del paciente (`PatientCard.tsx` y en el detalle ampliado de `PatientsPage.tsx`), puedes mostrar esta información si existe. Por ejemplo, al lado o debajo del nombre del hospital agregar un pequeño badge o texto que diga: "Área: Trauma Shock".
