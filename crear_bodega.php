<?php
require_once 'db.php';

// Si el formulario viene enviado por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo    = $_POST['codigo'] ?? '';
    $nombre    = $_POST['nombre'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $dotacion  = $_POST['dotacion'] ?? 0;
    $estado    = $_POST['estado'] ?? 'Activada';

    // Validaciones básicas (puedes agregar más)
    if (empty($codigo) || empty($nombre) || empty($direccion)) {
        $error = "Todos los campos son obligatorios.";
    } else {

        try {
            $pdo = getConnection();

            $sql = "INSERT INTO bodega (codigo, nombre, direccion, dotacion, estado)
                    VALUES (:codigo, :nombre, :direccion, :dotacion, :estado)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':codigo'    => $codigo,
                ':nombre'    => $nombre,
                ':direccion' => $direccion,
                ':dotacion'  => $dotacion,
                ':estado'    => $estado
            ]);

            // Redirige al listado
            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $error = "Error al guardar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Bodega</title>
</head>
<body>
    <h1>Crear nueva Bodega</h1>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Código:</label><br>
        <input type="text" name="codigo" required><br><br>

        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Dirección:</label><br>
        <input type="text" name="direccion" required><br><br>

        <label>Dotación:</label><br>
        <input type="number" name="dotacion" min="0" value="0" required><br><br>

        <label>Estado:</label><br>
        <select name="estado">
            <option value="Activada">Activada</option>
            <option value="Desactivada">Desactivada</option>
        </select><br><br>

        <button type="submit">Crear</button>
    </form>

    <br>
    <a href="index.php">Volver al listado</a>
</body>
</html>
