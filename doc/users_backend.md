# Documentación del Backend: Gestión de Usuarios (Admin)

Este módulo expone una serie de endpoints protegidos para que los administradores, superadministradores y coordinadores puedan gestionar los usuarios registrados en el sistema (listar, editar datos, modificar roles y eliminar).

## Endpoints

Todos los endpoints están prefijados con `/api/v1` y requieren autenticación mediante JWT (`Authorization: Bearer <token>`).

### 1. Listar Usuarios
- **Ruta:** `GET /users`
- **Autenticación:** Requerida (JWT)
- **Permisos:** `superadmin`, `admin`, `coordinator`
- **Descripción:** Obtiene una lista de todos los usuarios registrados. Los usuarios se ordenan por fecha de creación de forma descendente (los más nuevos primero).
- **Parámetros de consulta (Opcionales):**
  - `role` (string): Filtra los usuarios por un rol específico (ej. `?role=volunteer`).
  - `search` (string): Busca coincidencias parciales en nombre, email o cédula (DNI) (ej. `?search=maria`).
- **Respuesta (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Maria Garcia",
      "dni": "V-12345678",
      "phone": "+584121234567",
      "email": "maria@example.com",
      "role": "volunteer",
      "is_verified": true,
      "created_at": "2026-06-27T18:00:00Z",
      "updated_at": "2026-06-27T18:00:00Z"
    }
  ]
}
```

### 2. Obtener Usuario por ID
- **Ruta:** `GET /users/{id}`
- **Autenticación:** Requerida (JWT)
- **Permisos:** `superadmin`, `admin`, `coordinator`
- **Descripción:** Obtiene los datos detallados de un usuario específico.
- **Respuesta (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Maria Garcia",
    "dni": "V-12345678",
    ...
  }
}
```

### 3. Actualizar Usuario
- **Ruta:** `PUT /users/{id}`
- **Autenticación:** Requerida (JWT)
- **Permisos:** `superadmin`, `admin`, `coordinator`
- **Descripción:** Actualiza los datos de un usuario, incluyendo su rol. Todos los campos son opcionales (`sometimes`), por lo que puedes enviar solo lo que necesitas modificar.
- **Cuerpo de la Petición (JSON):**
```json
{
  "name": "Maria C. Garcia",
  "role": "admin",
  "is_verified": true
}
```
- **Campos Aceptados:**
  - `name` (string, max:150)
  - `dni` (string, formato: `V-12345678`)
  - `phone` (string, formato de Venezuela)
  - `email` (string, email válido)
  - `password` (string, opcional, mínimo 6 caracteres)
  - `role` (string, valores: `superadmin`, `admin`, `coordinator`, `engineer`, `volunteer`, `user`)
  - `is_verified` (boolean)
- **Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Usuario actualizado exitosamente",
  "data": {
    "id": 1,
    "name": "Maria C. Garcia",
    "role": "admin",
    ...
  }
}
```
- **Respuesta de Error de Validación (422 Unprocessable Entity):**
Se devuelve si se intenta asignar un correo o DNI que ya existe, o si el rol no es válido.

### 4. Eliminar Usuario
- **Ruta:** `DELETE /users/{id}`
- **Autenticación:** Requerida (JWT)
- **Permisos:** `superadmin`, `admin`, `coordinator`
- **Descripción:** Elimina a un usuario del sistema. Un administrador no puede eliminarse a sí mismo.
- **Respuesta (200 OK):**
```json
{
  "success": true,
  "message": "Usuario eliminado exitosamente"
}
```

## Notas para el Agente Frontend
1. Para construir la vista en React, crea una tabla o lista iterando sobre `GET /api/v1/users`.
2. Puedes incluir una barra de búsqueda que haga peticiones pasando el query parameter `?search=...`.
3. Al hacer clic en "Editar", debes abrir un modal o página que consuma `PUT /api/v1/users/{id}` permitiendo modificar el rol del usuario utilizando un componente `<select>` o menú desplegable con las opciones permitidas.
4. Recuerda incluir el Bearer token almacenado en el `AuthContext` o `authApi` en todas las peticiones a estas rutas.
