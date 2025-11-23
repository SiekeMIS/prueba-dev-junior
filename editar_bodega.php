<?php
require_once 'db.php';

$pdo = getConnection();

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

    // Esto viene como array (porque el select será multiple)
    $encargadosSeleccionados = $_POST['encargados'] ?? []; // puede ser []

    if (empty($codigo) || empty($nombre) || empty($direccion)) {
        $error = "Todos los campos son obligatorios.";
    } else {
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

            // Borrar relaciones anteriores
            $sqlDelete = "DELETE FROM bodega_encargado WHERE id_bodega = :id_bodega";
            $stmtDelete = $pdo->prepare($sqlDelete);
            $stmtDelete->execute([':id_bodega' => $id]);

            // Insertar nuevas relaciones (si hay encargados seleccionados)
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
    }
}

// Obtener datos actuales de la bodega
$sql = "SELECT * FROM bodega WHERE id_bodega = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$bodega = $stmt->fetch();

if (!$bodega) {
    die("No se encontró la bodega con ID $id.");
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
</head>
<body>
    <h1>Editar Bodega</h1>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Código:</label><br>
        <input type="text" name="codigo" value="<?= htmlspecialchars($bodega['codigo']) ?>" required><br><br>

        <label>Nombre:</label><br>
        <input type="text" name="nombre" value="<?= htmlspecialchars($bodega['nombre']) ?>" required><br><br>

        <label>Dirección:</label><br>
        <input type="text" name="direccion" value="<?= htmlspecialchars($bodega['direccion']) ?>" required><br><br>

        <label>Dotación:</label><br>
        <input type="number" min="0" name="dotacion" value="<?= htmlspecialchars($bodega['dotacion']) ?>" required><br><br>

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

        <button type="submit">Guardar cambios</button>
    </form>

    <br>
    <a href="index.php">Volver</a>

</body>
</html>
