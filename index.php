<?php
require_once 'db.php';

$pdo = getConnection();

// 1) Leer filtros desde GET
$fechaInicio = $_GET['fecha_inicio'] ?? '';
$fechaFin    = $_GET['fecha_fin'] ?? '';
$estado      = $_GET['estado'] ?? 'todos'; // valor por defecto

$conditions = [];
$params     = [];

// 2) Filtros de fecha
if ($fechaInicio !== '' && $fechaFin !== '') {
    $conditions[] = "DATE(b.fecha_creacion) BETWEEN :fecha_inicio AND :fecha_fin";
    $params[':fecha_inicio'] = $fechaInicio;
    $params[':fecha_fin']    = $fechaFin;
} elseif ($fechaInicio !== '') {
    $conditions[] = "DATE(b.fecha_creacion) >= :fecha_inicio";
    $params[':fecha_inicio'] = $fechaInicio;
} elseif ($fechaFin !== '') {
    $conditions[] = "DATE(b.fecha_creacion) <= :fecha_fin";
    $params[':fecha_fin'] = $fechaFin;
}

// 3) Filtro de estado (Activada / Desactivada / todos)
if ($estado === 'Activada' || $estado === 'Desactivada') {
    $conditions[] = "b.estado = :estado";
    $params[':estado'] = $estado;
}

// 4) Construir el WHERE final
$where = '';
if (!empty($conditions)) {
    $where = 'WHERE ' . implode(' AND ', $conditions);
}

// 5) Consulta principal: Obtener bodegas con sus encargados
// Usamos LEFT JOIN para incluir bodegas sin encargados asignados
$sql = "
    SELECT 
        b.id_bodega,
        b.codigo,
        b.nombre,
        b.direccion,
        b.dotacion,
        b.estado,
        b.fecha_creacion,
        COALESCE(
            STRING_AGG(e.nombre || ' ' || e.primer_apellido, ' / '),
            'Sin encargado'
        ) AS encargados
    FROM bodega b
    LEFT JOIN bodega_encargado be ON be.id_bodega = b.id_bodega
    LEFT JOIN encargado e ON e.id_encargado = be.id_encargado
    $where
    GROUP BY 
        b.id_bodega,
        b.codigo,
        b.nombre,
        b.direccion,
        b.dotacion,
        b.estado,
        b.fecha_creacion
    ORDER BY b.id_bodega;
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params); // Parámetros: fechas y estado
$bodegas = $stmt->fetchAll(); // Obtener todos los resultados como array

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Bodegas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<div class="container">
    <h1>Listado de Bodegas</h1>
    <div class="top-actions">
        <div>
            <a href="crear_bodega.php" class="btn btn-primary"> + Crear nueva bodega</a>
        </div>
    </div>

    <form method="GET" action="index.php" class="filter-form">
        <div>
            <label>Fecha inicio:</label>
            <input type="date" name="fecha_inicio" value="<?= htmlspecialchars($fechaInicio) ?>">
        </div>

        <div>
            <label>Fecha fin:</label>
            <input type="date" name="fecha_fin" value="<?= htmlspecialchars($fechaFin) ?>">
        </div>

        <div>
            <label>Estado:</label>
            <select name="estado">
                <option value="todos"       <?= $estado === 'todos' ? 'selected' : '' ?>>Todos</option>
                <option value="Activada"    <?= $estado === 'Activada' ? 'selected' : '' ?>>Activada</option>
                <option value="Desactivada" <?= $estado === 'Desactivada' ? 'selected' : '' ?>>Desactivada</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="index.php" class="btn btn-ghost">Limpiar filtro</a>
        </div>
    </form>

    <?php if (empty($bodegas)): ?>
        <p>No hay bodegas registradas para el filtro aplicado.</p>
    <?php else: ?>
        <!-- Tabla de bodegas - Se muestra cuando hay resultados -->
        <table>
            <thead>
                <tr class="colorsletra">
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Dotación</th>
                    <th>Estado</th>
                    <th>Fecha creación</th>
                    <th>Encargado(s)</th>
                    <th>Acciones</th> <!-- Columnas para operaciones CRUD -->
                </tr>
            </thead>
            <tbody>
            <?php foreach ($bodegas as $bodega): ?>
                <!-- Fila por cada bodega - Iteración sobre resultados de BD -->
                <tr>
                    <td><?= htmlspecialchars($bodega['codigo']) ?></td>
                    <td><?= htmlspecialchars($bodega['nombre']) ?></td>
                    <td><?= htmlspecialchars($bodega['direccion']) ?></td>
                    <td><?= htmlspecialchars($bodega['dotacion']) ?></td>
                    <td><?= htmlspecialchars($bodega['estado']) ?></td>
                    <td><?= htmlspecialchars($bodega['fecha_creacion']) ?></td>
                    <td><?= htmlspecialchars($bodega['encargados']) ?></td> <!-- Encargados concatenados -->
                    <td>
                        <!-- Columna de acciones: Editar y Eliminar -->
                        <div class="action-group">
                            <a href="editar_bodega.php?id=<?= $bodega['id_bodega'] ?>" class="btn btn-secondary" aria-label="Editar <?= htmlspecialchars($bodega['nombre']) ?>">
                            <!-- Icono SVG de basura - Acción destructiva -->
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21l3-1 11-11a2.5 2.5 0 0 0 0-3.5 2.5 2.5 0 0 0-3.5 0L5.5 16 4 19z"/></svg>
                            Editar
                            </a>
                            <!-- Botón Eliminar - Con confirmación JavaScript -->
                            <a href="eliminar_bodega.php?id=<?= $bodega['id_bodega'] ?>" class="btn btn-danger"
                            onclick="return confirmarEliminacion('<?= htmlspecialchars($bodega['nombre']) ?>');" aria-label="Eliminar <?= htmlspecialchars($bodega['nombre']) ?>">
                            <!-- basura SVG -->
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
                            Eliminar
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="js/app.js"></script>

</body>
</html>
