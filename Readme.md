## Métodos de Instalación

## Para Desarrollo
```bash
# 1. Navegar al directorio del proyecto
cd bodegas_app

# 2. Iniciar servidor de desarrollo PHP
php -S localhost:8000

# 3. Abrir en navegador: http://localhost:8000

## 1. Configuración de Base de Datos
```bash
# Opción A: Usando PgAdmin 4 (GUI - Recomendado para principiantes)
1. Abrir PgAdmin 4
2. Crear nueva base de datos: `empresa_bodegas`
3. Clic derecho sobre la base de datos → Restore...
4. Seleccionar archivo: `bodega.backup`
5. Ejecutar restore