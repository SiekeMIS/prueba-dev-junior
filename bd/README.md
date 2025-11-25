# Modelo de Datos - Sistema de Gestión de Bodegas

## Esquema de Base de Datos

### Tablas

## **bodega**
- `id_bodega` SERIAL PRIMARY KEY
- `codigo` VARCHAR(5) NOT NULL UNIQUE
- `nombre` VARCHAR(100) NOT NULL  
- `direccion` VARCHAR(255) NOT NULL
- `dotacion` INTEGER NOT NULL CHECK (dotacion >= 0)
- `estado` VARCHAR(15) NOT NULL CHECK (estado IN ('Activada', 'Desactivada'))
- `fecha_creacion` TIMESTAMP DEFAULT NOW()

# **encargado**
- `id_encargado` SERIAL PRIMARY KEY
- `run` VARCHAR(12) NOT NULL UNIQUE
- `nombre` VARCHAR(50) NOT NULL
- `primer_apellido` VARCHAR(50) NOT NULL
- `segundo_apellido` VARCHAR(50)
- `direccion` VARCHAR(255)
- `telefono` VARCHAR(20)

# **bodega_encargado**
- `id_bodega` INTEGER REFERENCES bodega(id_bodega) ON DELETE CASCADE
- `id_encargado` INTEGER REFERENCES encargado(id_encargado) ON DELETE RESTRICT
- PRIMARY KEY (id_bodega, id_encargado)

## Relaciones
- **Bodega ↔ Encargado**: Relación Muchos a Muchos (N:N)
-  Una bodega puede tener múltiples encargados
-  Un encargado puede estar en múltiples bodegas