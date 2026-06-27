# Guía Técnica - Módulo de Pacientes (Registro y Búsqueda)

El módulo de pacientes es la pieza más crítica de la plataforma. Está diseñado para registrar e identificar personas ingresadas en centros de salud y carpas de campaña durante la emergencia, facilitando la búsqueda y reunificación por parte de sus familiares.

---

## 1. Reglas de Negocio y Flexibilidad

Dada la naturaleza del desastre y la urgencia en la recolección de datos, la API aplica las siguientes directrices:

1. **Flexibilidad Extrema de Datos:** 
   * **Ningún campo de identificación personal es obligatorio**. Un paciente puede ser registrado de manera 100% anónima si se encuentra inconsciente o indocumentado.
   * El único campo estrictamente requerido es `hospital_id`.
2. **Duplicados y Conflictos (HTTP 409):**
   * **Por DNI:** Si se suministra un `dni`, no puede estar repetido en el sistema. Si ya existe, retorna `409 Conflict`.
   * **Por Nombre en el mismo Hospital:** Si no hay DNI pero se envían `first_name` y `last_name`, el sistema valida que no exista ya otro paciente con el mismo nombre y apellido *en ese mismo centro de salud* para evitar duplicidades accidentales.
3. **Fecha de Llegada Retroactiva (`arrival_date`):**
   * Por defecto, si el campo se omite, el sistema guarda la fecha y hora actuales.
   * Sin embargo, el frontend puede enviar una fecha y hora específicas (ej. si el paciente ingresó hace 12 horas pero recién se puede digitalizar su ficha).

---

## 2. Referencia de Endpoints

### 2.1. Registrar Paciente (`POST /api/v1/patients`)

Crea la ficha de un paciente ingresado. Admite la subida de una fotografía almacenada en Cloudflare R2.

* **Headers:** `Content-Type: multipart/form-data`
* **Cuerpo de la Petición (FormData):**

| Clave | Tipo | ¿Obligatorio? | Descripción / Reglas |
| :--- | :--- | :---: | :--- |
| `hospital_id` | Integer | **Sí** | ID del hospital donde se encuentra. Debe existir en la BD. |
| `dni` | String | No | Documento de identidad del paciente. Máx 20 caracteres. |
| `first_name` | String | No | Nombre(s) del paciente. Máx 100 caracteres. |
| `last_name` | String | No | Apellido(s) del paciente. Máx 100 caracteres. |
| `approximate_age` | Integer | No | Edad aproximada. |
| `physical_description` | String | No | Rasgos físicos (color de piel, tatuajes, vestimenta, anteojos). |
| `medical_status` | String | No | Estado de salud. Valores válidos: `estable`, `critico`, `observacion`, `dado_de_alta`, `fallecido`, `trasladado`. *(Por defecto: `estable`)* |
| `photo` | File (Imagen) | No | Fotografía del paciente (JPG, PNG, WEBP). Tamaño máx: 5MB (5120 KB). |
| `arrival_date` | String (DateTime) | No | Fecha/hora de ingreso del paciente. Formato: `YYYY-MM-DD HH:MM:SS` o ISO 8601. |

* **Respuesta Exitosa (201 Created):**
  ```json
  {
      "success": true,
      "message": "Paciente registrado exitosamente",
      "data": {
          "id": 8,
          "hospital_id": 3,
          "dni": "V-15894123",
          "first_name": "Luis Alberto",
          "last_name": "González",
          "approximate_age": 42,
          "physical_description": "Cicatriz en ceja izquierda, vestía camisa azul.",
          "medical_status": "estable",
          "photo_url": "https://r2.juntosvnzl.org/patients/luis_gonzalez.jpg",
          "arrival_date": "2026-06-25 14:30:00",
          "created_at": "2026-06-26T17:00:00.000000Z",
          "updated_at": "2026-06-26T17:00:00.000000Z"
      }
  }
  ```

* **Respuestas de Error:**
  * **409 Conflict (DNI Duplicado):**
    `{"success": false, "message": "Ya existe un paciente registrado con ese DNI."}`
  * **409 Conflict (Nombre Duplicado en mismo Hospital):**
    `{"success": false, "message": "Ya existe un paciente con ese nombre en este hospital."}`
  * **422 Unprocessable Entity:** Si el `hospital_id` no existe, la edad no es entera, el archivo no es una imagen válida o excede los 5MB.

---

### 2.2. Buscar Pacientes (`GET /api/v1/patients/search`)

Permite a los familiares buscar pacientes por DNI, por nombre/apellido, o por rasgos físicos. **Retorna resultados paginados.**

* **Headers:** `Content-Type: application/json`
* **Query Parameters (Buscador):**
  * `dni` (string, opcional): Búsqueda por coincidencia exacta de DNI.
  * `name` (string, opcional): Coincidencia parcial insensible a mayúsculas/minúsculas (`ILIKE`) en nombre o apellido.
  * `description` (string, opcional): Coincidencia parcial en la descripción física.
  * `page` (entero, opcional): Número de página (por defecto `1`).
  * `per_page` (entero, opcional): Elementos por página (por defecto `15`).

* **Estructura del Objeto Hospital Anidado:**
  Para evitar peticiones extra al frontend, cada paciente en la lista de resultados viene con la relación **`hospital`** eagerloadeada de forma automática.
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
                  "physical_description": "Tatuaje de estrella en el antebrazo derecho.",
                  "medical_status": "estable",
                  "photo_url": "https://r2.juntosvnzl.org/patients/carlos_mendoza.jpg",
                  "arrival_date": "2026-06-26 10:15:00",
                  "created_at": "2026-06-26T17:00:00.000000Z",
                  "updated_at": "2026-06-26T17:00:00.000000Z",
                  "hospital": {
                      "id": 3,
                      "name": "Hospital Universitario de Caracas",
                      "type": "hospital_publico",
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

---

## 3. Ejemplo de Integración en React (Axios)

### A. Registro de Paciente (FormData)
```javascript
import axios from 'axios';

async function registerPatient(patientData, photoFile) {
    const formData = new FormData();
    formData.append('hospital_id', patientData.hospitalId);
    
    if (patientData.dni) formData.append('dni', patientData.dni);
    if (patientData.firstName) formData.append('first_name', patientData.firstName);
    if (patientData.lastName) formData.append('last_name', patientData.lastName);
    if (patientData.approximateAge) formData.append('approximate_age', patientData.approximateAge);
    if (patientData.physicalDescription) formData.append('physical_description', patientData.physicalDescription);
    if (patientData.medicalStatus) formData.append('medical_status', patientData.medicalStatus);
    if (patientData.arrivalDate) formData.append('arrival_date', patientData.arrivalDate); // Ej: "2026-06-25 18:00:00"
    
    if (photoFile) {
        formData.append('photo', photoFile); // Objeto File del input file
    }

    try {
        const response = await axios.post('/api/v1/patients', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        return response.data;
    } catch (error) {
        if (error.response) {
            if (error.response.status === 409) {
                // Manejar duplicado (DNI o Nombre)
                alert(error.response.data.message);
            } else if (error.response.status === 422) {
                // Errores de validación
                console.error('Errores de validación:', error.response.data.errors);
            }
        }
        throw error;
    }
}
```

### B. Búsqueda de Pacientes Paginada (Buscador)
```javascript
import React, { useState, useEffect } from 'react';
import axios from 'axios';

function PatientSearch() {
    const [searchTerm, setSearchTerm] = useState('');
    const [searchType, setSearchType] = useState('name'); // 'name', 'dni', 'description'
    const [patients, setPatients] = useState([]);
    const [pagination, setPagination] = useState({ current_page: 1, last_page: 1 });
    const [page, setPage] = useState(1);

    const handleSearch = async () => {
        try {
            // Construir query params dinámicos
            const params = {
                page,
                per_page: 10
            };
            if (searchTerm) {
                params[searchType] = searchTerm;
            }

            const response = await axios.get('/api/v1/patients/search', { params });
            const paginator = response.data.data;
            
            setPatients(paginator.data); // El array de pacientes
            setPagination({
                current_page: paginator.current_page,
                last_page: paginator.last_page
            });
        } catch (error) {
            console.error("Error al buscar pacientes", error);
        }
    };

    useEffect(() => {
        handleSearch();
    }, [page]); // Recargar al cambiar de página

    return (
        <div>
            {/* UI del buscador */}
            <input 
                type="text" 
                value={searchTerm} 
                onChange={(e) => setSearchTerm(e.target.value)} 
                placeholder="Buscar..."
            />
            <select value={searchType} onChange={(e) => setSearchType(e.target.value)}>
                <option value="name">Nombre</option>
                <option value="dni">DNI</option>
                <option value="description">Descripción Física</option>
            </select>
            <button onClick={() => { setPage(1); handleSearch(); }}>Buscar</button>

            {/* Listado de resultados */}
            <ul>
                {patients.map(patient => (
                    <li key={patient.id}>
                        {patient.first_name || "Desconocido"} {patient.last_name || ""} 
                        - Estado: {patient.medical_status} 
                        - Ubicación: {patient.hospital?.name || "Sin Hospital"}
                    </li>
                ))}
            </ul>

            {/* Controles de paginación */}
            <button disabled={page === 1} onClick={() => setPage(page - 1)}>Anterior</button>
            <span>Página {pagination.current_page} de {pagination.last_page}</span>
            <button disabled={page === pagination.last_page} onClick={() => setPage(page + 1)}>Siguiente</button>
        </div>
    );
}
```
