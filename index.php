<?php
require_once 'db.php';

$pdo = getConnection();

// Traer bodegas para probar la conexión
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


$stmt = $pdo->query($sql);
$bodegas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Bodegas - Prueba Técnica</title>
</head>
<body>
    <h1>Listado de Bodegas (test de conexión)</h1>

        <a href="crear_bodega.php">Crear nueva bodega</a>
    <br><br>

    <?php if (empty($bodegas)): ?>
        <p>No hay bodegas registradas.</p>
    <?php else: ?>
        
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Dotación</th>
                    <th>Estado</th>
                    <th>Fecha creación</th>
                    <th>Encargado(s)</th>
                    <th>Acciones</th>
                </tr>
            </thead>
                    <tbody>
                    <?php foreach ($bodegas as $bodega): ?>
                        <tr>
                            <td><?= $bodega['id_bodega'] ?></td>
                            <td><?= $bodega['codigo'] ?></td>
                            <td><?= $bodega['nombre'] ?></td>
                            <td><?= $bodega['direccion'] ?></td>
                            <td><?= $bodega['dotacion'] ?></td>
                            <td><?= $bodega['estado'] ?></td>
                            <td><?= $bodega['fecha_creacion'] ?></td>
                            <td><?= htmlspecialchars($bodega['encargados']) ?></td>
                            <td>
                                <a href="editar_bodega.php?id=<?= $bodega['id_bodega'] ?>">Editar</a> |
                                <a href="eliminar_bodega.php?id=<?= $bodega['id_bodega'] ?>"
                                onclick="return confirm('¿Seguro que quieres eliminar esta bodega?')">
                                Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

        </table>
    <?php endif; ?>
</body>
</html>
