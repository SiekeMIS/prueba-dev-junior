<?php
require_once 'db.php';

$pdo = getConnection();

// 1. Validación de ID
if (!isset($_GET['id'])) {
    die("ID no especificado.");
}

$id = intval($_GET['id']);

// 2. Obtener datos de la bodega para mostrar en pantalla
$sql = "SELECT codigo, nombre 
        FROM bodega
        WHERE id_bodega = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$bodega = $stmt->fetch();

if (!$bodega) {
    die("La bodega con ID $id no existe.");
}

// 3. Procesar eliminación si viene POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sqlDelete = "DELETE FROM bodega WHERE id_bodega = :id";
        $stmtDel = $pdo->prepare($sqlDelete);
        $stmtDel->execute([':id' => $id]);

        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        $error = "Error al eliminar la bodega: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Bodega</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/app.js" defer></script>
</head>
<body>

<div class="container">

    <h1>Eliminar Bodega</h1>

    <?php if (!empty($error)): ?>
        <div class="error-box">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <p>
        ¿Estás segura/o de que deseas eliminar la siguiente bodega?
    </p>

    <div class="error-box" style="background:#fff3cd; border-color:#ffecb5; color:#664d03;">
        <strong>Código:</strong> <?= htmlspecialchars($bodega['codigo']) ?><br>
        <strong>Nombre:</strong> <?= htmlspecialchars($bodega['nombre']) ?><br>
    </div>

    <p style="margin-top:15px;">
        Esta acción <strong>no se puede deshacer</strong>.
    </p>

    <form method="POST" class="crud-form">
        <button type="submit" class="btn btn-danger">Sí, eliminar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>

</div>
</body>
</html>
