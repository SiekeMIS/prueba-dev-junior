<?php
require_once 'db.php';

$pdo = getConnection();

// Inicializar variables de error
$errores = [];
$error   = null;

// Verificar si viene un ID por GET
if (!isset($_GET['id'])) {
    die("ID de bodega no especificado.");
}

$id = (int) $_GET['id'];

// Si viene POST, significa que estamos guardando cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo    = $_POST['codigo'] ?? '';
    $nombre    = $_POST['nombre'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $dotacion  = $_POST['dotacion'] ?? 0;
    $estado    = $_POST['estado'] ?? 'Activada';
    $encargadosSeleccionados = $_POST['encargados'] ?? [];

    // VALIDACIONES PERSONALIZADAS
    if (strlen($codigo) > 5) {
        $errores[] = "El código no puede tener más de 5 caracteres.";
    }

    if (strlen($nombre) > 100) {
        $errores[] = "El nombre no puede tener más de 100 caracteres.";
    }

    if (empty($codigo) || empty($nombre) || empty($direccion)) {
        $errores[] = "Todos los campos obligatorios deben estar completos.";
    }

    // Si no hay errores → ejecutamos UPDATE + transacción
    if (empty($errores)) {
        try {
            // Usamos transacción para que todo quede coherente
            $pdo->beginTransaction();

            // 1) Actualizar la bodega
            $sql = "UPDATE bodega 
                    SET codigo = :codigo,
                        nombre = :nombre,
                        direccion = :direccion,
                        dotacion = :dotacion,
                        estado = :estado
                    WHERE id_bodega = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':codigo'    => $codigo,
                ':nombre'    => $nombre,
                ':direccion' => $direccion,
                ':dotacion'  => $dotacion,
                ':estado'    => $estado,
                ':id'        => $id
            ]);

            // 2) Actualizar encargados asociados a la bodega

            $sqlDelete = "DELETE FROM bodega_encargado WHERE id_bodega = :id_bodega";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute([':id_bodega' => $id]);

            if (!empty($encargadosSeleccionados)) {
                $sqlInsert = "INSERT INTO bodega_encargado (id_bodega, id_encargado)
                              VALUES (:id_bodega, :id_encargado)";
                $stmtInsert = $pdo->prepare($sqlInsert);

                foreach ($encargadosSeleccionados as $idEncargado) {
                    $stmtInsert->execute([
                        ':id_bodega'    => $id,
                        ':id_encargado' => (int)$idEncargado
                    ]);
                }
            }

            $pdo->commit();

            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Error al actualizar: " . $e->getMessage();
        }
    } else {
        // Si hay errores, usamos los valores del POST para que el formulario
        // muestre lo que el usuario intentó guardar
        $bodega = [
            'codigo'    => $codigo,
            'nombre'    => $nombre,
            'direccion' => $direccion,
            'dotacion'  => $dotacion,
            'estado'    => $estado
        ];
    }
}

// Si no existe $bodega todavía (por ejemplo primera vez que se carga por GET), la obtenemos de la BD
if (!isset($bodega)) {
    $sql = "SELECT * FROM bodega WHERE id_bodega = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $bodega = $stmt->fetch();

    if (!$bodega) {
        die("No se encontró la bodega con ID $id.");
    }
}

// Obtener TODOS los encargados
$sqlEnc = "SELECT id_encargado, nombre, primer_apellido 
           FROM encargado
           ORDER BY nombre, primer_apellido";
$stmtEnc = $pdo->query($sqlEnc);
$encargados = $stmtEnc->fetchAll();

// Obtener encargados actualmente asociados a esta bodega
$sqlEncSel = "SELECT id_encargado 
              FROM bodega_encargado
              WHERE id_bodega = :id_bodega";
$stmtEncSel = $pdo->prepare($sqlEncSel);
$stmtEncSel->execute([':id_bodega' => $id]);
$encargadosDeBodega = $stmtEncSel->fetchAll();

// Pasar a array plano de IDs para usar en el form
$idsEncargadosDeBodega = array_column($encargadosDeBodega, 'id_encargado');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Bodega</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="js/app.js" defer></script>
</head>
<body>
<div class="container">
    <h1>Editar Bodega</h1>

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

        <label>Código:</label><br>
        <input type="text" name="codigo"
               maxlength="5"
               value="<?= htmlspecialchars($bodega['codigo']) ?>" required><br><br>

        <label>Nombre:</label><br>
        <input type="text" name="nombre"
               maxlength="100"
               value="<?= htmlspecialchars($bodega['nombre']) ?>" required><br><br>

        <label>Dirección:</label><br>
        <input type="text" name="direccion"
               value="<?= htmlspecialchars($bodega['direccion']) ?>" required><br><br>

        <label>Dotación:</label><br>
        <input type="number" min="0" name="dotacion"
               value="<?= htmlspecialchars($bodega['dotacion']) ?>" required><br><br>

        <label>Estado:</label><br>
        <select name="estado">
            <option value="Activada"    <?= $bodega['estado'] === 'Activada' ? 'selected' : '' ?>>Activada</option>
            <option value="Desactivada" <?= $bodega['estado'] === 'Desactivada' ? 'selected' : '' ?>>Desactivada</option>
        </select><br><br>

        <label>Encargados (puedes seleccionar uno o varios):</label><br>
        <select name="encargados[]" multiple size="5">
            <?php foreach ($encargados as $enc): ?>
                <?php
                    $idEnc = $enc['id_encargado'];
                    $nombreCompleto = $enc['nombre'] . ' ' . $enc['primer_apellido'];
                    $selected = in_array($idEnc, $idsEncargadosDeBodega) ? 'selected' : '';
                ?>
                <option value="<?= $idEnc ?>" <?= $selected ?>>
                    <?= htmlspecialchars($nombreCompleto) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><small>Ctrl+click para seleccionar/deseleccionar múltiples.</small>
        <br><br>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
    </form>

    <a href="index.php" class="back-link">← Volver al listado</a>
</div>
</body>
</html>
