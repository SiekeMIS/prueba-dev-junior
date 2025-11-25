# Nombre del Proyecto

🔹 **Descripción breve** 
El proyecto es un sistema web para la gestión de bodegas, que permite crear, visualizar, editar y eliminar registros de forma simple y segura. La aplicación muestra información clave como código, nombre, dirección, dotación, encargado, fecha de creación y estado (activada/desactivada).

## Tecnologías utilizadas para el Backend
- PHP PHP 7.x/8.x
- PDO (PostgreSQL)

## Tecnologías utilizadas para el Frontend
- HTML5
- CSS puro (estilos personalizados)
- JavaScript Vanilla (validaciones y confirmaciones)
- SVG para iconos

## Requisitos previos para el Backend
- PHP 7.0 o superior
- PostgreSQL 9.5+
- Extensiones PHP: pdo_pgsql, mbstring

## ## Requisitos previos para el Frontend
- Navegador web moderno (Chrome, Firefox, brave, Edge)
- JavaScript habilitado

## Instalación y Configuración de la Base de Datos
1. Ingresar a PgAdmin 4 de Postgres.
2. Crear la base de datos
3. Ingresar Bodega.backup con restore a la base de datos creada.

## Instalación y Configuración del Backend
1. Clona o descarga los archivos del proyecto
2. Configura la conexión a la base de datos:
   - Edita el archivo db.php con tus credenciales.

## Backend (PHP)
- index.php - Listado principal con filtros
- crear_bodega.php - Formulario de creación de bodegas
- editar_bodega.php - Edición con gestión de encargados
- eliminar_bodega.php - Eliminación con confirmación
- db.php - Conexión a base de datos PostgreSQL

## Frontend
- css/estilos.css - Estilos minimalistas y responsive
- js/app.js - Validaciones y confirmaciones

## Configuración del Frontend
1. No requiere instalación adicional
2. Los archivos CSS y JS están incluidos en el proyecto

## Funcionalidades
- CRUD Completo: Creación, lectura, actualización y eliminación de bodegas
- Gestión de Encargados: Asignación múltiple de encargados por bodega
- Filtros Avanzados: Por estado (Activada/Desactivada) y rango de fechas
- Validación de Formularios: Frontend y backend (código 5 chars, nombre 100 chars)
- Confirmaciones de Eliminación: Diálogos de confirmación con JavaScript

## Estructura del Proyecto
bodegas_app
├── bd/
│   ├── Bodega.backup
│   ├── Modelo de datos
│   ├── README
│   └── Relaciones
├── css/
│   └── estilos.css
├── js/
│   └── app.js
├── crear_bodega.php
├── db.php
├── Documentacion
├── editar_bodega.php
├── eliminar_bodega.php
├── index.php
└── README

## Modelo de Datos
- Bodega: código (5 chars), nombre (100 chars), dirección, dotación, estado, fecha_creación
- Encargado: RUN, nombre, apellidos, dirección, teléfono
- Relación: Muchos a muchos (bodega_encargado)

# Contribuciones
- Haz un fork del repositorio.
- Link repo: https://github.com/SiekeMIS/prueba-dev-junior.git
- Crea una rama con tu nueva feature (git checkout -b feature/nueva-feature).
- Haz commit de tus cambios (git commit -am 'Añade nueva feature').
- Haz push a la rama (git push origin feature/nueva-feature).
- Abre un Pull Request.

# Futuras Mejoras
- Sistema de autenticación de usuarios
- Roles y permisos (administrador, encargado, visualizador)
- Reportes y estadísticas de bodegas
- Búsqueda avanzada por múltiples criterios
- Historial de movimientos de bodegas
- Integración con mapas para ubicación geográfica
- Notificaciones por email
- API REST para integración con otros sistemas
- Panel administrativo con dashboard
- Dockerización del proyecto