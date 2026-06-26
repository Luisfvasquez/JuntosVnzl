+-------------------+          +--------------------+
|   countries_aid   |          |       users        |
+-------------------+          +--------------------+
| (1)
|
| (0..N)
+-------------------+          +--------------------+          +--------------------+
|    incidents      |          |     aid_offers     |          | collection_centers |
+-------------------+          +--------------------+          +--------------------+
| (1)
|
| (0..N)
+--------------------+
|   center_supplies  |
+--------------------+

---

## 3. Estructura Física de la Base de Datos (PostgreSQL DDL)

A continuación, se detalla el script SQL estructurado para PostgreSQL. Se incorporan restricciones estrictas (`CHECK`), claves únicas complejas e índices óptimos para búsquedas masivas.

```sql
-- Habilitar extensión para generación de UUIDs si es requerido
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- 1. TABLA: Países de Ayuda Internacional
CREATE TABLE countries_aid (
    id BIGSERIAL PRIMARY KEY,
    country_name VARCHAR(100) NOT NULL,
    iso_code VARCHAR(3) NOT NULL UNIQUE, -- Ej: CO, ES, US
    aid_type VARCHAR(100) NOT NULL,      -- Ej: Rescate, Insumos Médicos, Financiero
    description TEXT,
    status VARCHAR(20) DEFAULT 'en_camino' NOT NULL,
    registered_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_country_status CHECK (status IN ('en_camino', 'entregado', 'en_frontera'))
);

-- 2. TABLA: Usuarios (Preparada para Fase II)
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,     -- Formato estricto: V-XXXXXXXX o E-XXXXXXXX
    phone VARCHAR(20) NOT NULL UNIQUE,   -- Formato internacional: +584XXXXXXXXX
    email VARCHAR(150) NULL UNIQUE,
    password VARCHAR(255) NULL,          -- Nullable en Fase I si se pre-registran perfiles administrativos
    role VARCHAR(30) DEFAULT 'volunteer' NOT NULL,
    is_verified BOOLEAN DEFAULT FALSE NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_user_role CHECK (role IN ('superadmin', 'coordinator', 'volunteer', 'organization'))
);

-- 3. TABLA: Ofertas de Ayuda (Marketplace Humanitario)
CREATE TABLE aid_offers (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL, -- Null en Fase I (Anónimo)
    provider_name VARCHAR(150) NOT NULL, -- Nombre de la persona u organización que ofrece
    dni VARCHAR(20) NOT NULL,            -- Obligatorio aunque sea anónimo
    phone VARCHAR(20) NOT NULL,          -- Obligatorio para contacto directo de beneficiarios
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,       -- Ej: comida, ropa, vivienda, salud, transporte
    latitude NUMERIC(10, 8) NOT NULL,
    longitude NUMERIC(11, 8) NOT NULL,
    address_description TEXT NOT NULL,
    secure_token UUID DEFAULT uuid_generate_v4() NOT NULL UNIQUE, -- Token para edición sin login
    status VARCHAR(20) DEFAULT 'disponible' NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_offer_category CHECK (category IN ('comida', 'ropa', 'vivienda', 'salud', 'transporte', 'otros')),
    CONSTRAINT chk_offer_status CHECK (status IN ('disponible', 'reservado', 'entregado', 'expirado'))
);

-- 4. TABLA: Centros de Acopio
CREATE TABLE collection_centers (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL, -- Vinculación posterior
    center_name VARCHAR(150) NOT NULL,
    manager_name VARCHAR(150) NOT NULL,
    dni VARCHAR(20) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    latitude NUMERIC(10, 8) NOT NULL,
    longitude NUMERIC(11, 8) NOT NULL,
    address_description TEXT NOT NULL,
    status VARCHAR(30) DEFAULT 'recibiendo_insumos' NOT NULL,
    secure_token UUID DEFAULT uuid_generate_v4() NOT NULL UNIQUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_center_status CHECK (status IN ('recibiendo_insumos', 'capacidad_maxima', 'distribuyendo', 'en_traslado', 'inactivo'))
);

-- 5. TABLA: Insumos de Centros de Acopio
CREATE TABLE center_supplies (
    id BIGSERIAL PRIMARY KEY,
    collection_center_id BIGINT NOT NULL REFERENCES collection_centers(id) ON DELETE CASCADE,
    supply_name VARCHAR(100) NOT NULL, -- Ej: Agua potable, Enlatados, Gasas
    quantity NUMERIC(12, 2) NOT NULL DEFAULT 0.00,
    unit_of_measurement VARCHAR(20) NOT NULL, -- Ej: kg, litros, unidades, cajas
    priority_level VARCHAR(20) DEFAULT 'estable' NOT NULL,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_supply_priority CHECK (priority_level IN ('critico', 'requerido', 'estable', 'excedente'))
);

-- 6. TABLA: Incidentes e Infraestructura Afectada (Derrumbes, Vías Colapsadas)
CREATE TABLE incidents (
    id BIGSERIAL PRIMARY KEY,
    reporter_name VARCHAR(150) DEFAULT 'Anónimo' NOT NULL,
    description TEXT NOT NULL,
    latitude NUMERIC(10, 8) NOT NULL,
    longitude NUMERIC(11, 8) NOT NULL,
    address_description TEXT NOT NULL,
    severity VARCHAR(20) DEFAULT 'moderado' NOT NULL,
    aid_status VARCHAR(40) DEFAULT 'sin_ayuda' NOT NULL,
    aid_type_received TEXT NULL, -- Descripción de lo que ha llegado (ej: "Solo ayuda civil (médicos)")
    verified_by_community_count INT DEFAULT 1 NOT NULL, -- Incrementable públicamente para validar veracidad
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_incident_severity CHECK (severity IN ('leve', 'moderado', 'critico', 'catastrofico')),
    CONSTRAINT chk_incident_aid_status CHECK (aid_status IN ('sin_ayuda', 'ayuda_civil_en_sitio', 'ayuda_oficial_en_sitio', 'asistencia_completa'))
);

-- INDICES DE OPTIMIZACIÓN GEOESPACIAL Y BÚSQUEDA
CREATE INDEX idx_aid_offers_coords ON aid_offers (latitude, longitude);
CREATE INDEX idx_collection_centers_coords ON collection_centers (latitude, longitude);
CREATE INDEX idx_incidents_coords ON incidents (latitude, longitude);
CREATE INDEX idx_aid_offers_lookup ON aid_offers (dni, phone);
CREATE INDEX idx_collection_centers_lookup ON collection_centers (dni, phone);