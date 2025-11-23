<?php
require_once 'db.php';

$pdo = getConnection();

// Validar ID
if (!isset($_GET['id'])) {
    die("ID no especificado.");
}

$id = intval($_GET['id']);

// Si se confirma la eliminación (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $sql = "DELETE FROM bodega WHERE id_bodega = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        $error = "Error al eliminar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Bodega</title>
</head>
<body>

<h1>Eliminar Bodega</h1>

<?php if (isset($error)): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<p>¿Estás segura/o de que quieres eliminar esta bodega?</p>

<form method="POST">
    <button type="submit">Sí, eliminar</button>
</form>

<br>
<a href="index.php">Cancelar y volver</a>

</body>
</html>
