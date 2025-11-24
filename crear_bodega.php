<?php
require_once 'db.php';

$errores = [];
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo    = $_POST['codigo'] ?? '';
    $nombre    = $_POST['nombre'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $dotacion  = $_POST['dotacion'] ?? 0;
    $estado    = $_POST['estado'] ?? 'Activada';

    // Validaciones personalizadas
    $errores = [];

    if (strlen($codigo) > 5) {
        $errores[] = "El código no puede tener más de 5 caracteres.";
    }

    if (strlen($nombre) > 100) {
        $errores[] = "El nombre no puede tener más de 100 caracteres.";
    }

    if (empty($codigo) || empty($nombre) || empty($direccion)) {
        $errores[] = "Todos los campos obligatorios deben estar completos.";
    }

    if (!empty($errores)) {
        // No insertamos; sólo mostramos errores
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

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $error = "Error al guardar la bodega.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Bodega</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/app.js" defer></script>
</head>
<body>
<div class="container">
    <h1>Crear nueva Bodega</h1>

    <?php if (!empty($errores)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errores as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error-box">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="crud-form">
        

    <form method="POST">
        <label>Código:</label><br>
        <input type="text" name="codigo" maxlength="5" required><br><br>

        <label>Nombre:</label><br>
        <input type="text" name="nombre" maxlength="100" required><br><br>

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

    <a href="index.php" class="back-link">← Volver al listado</a>
</div>
<script src="js/app.js"></script>
</body>

</html>
