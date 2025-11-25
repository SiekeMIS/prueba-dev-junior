<?php
// db.php - conexión a PostgreSQL con PDO

function getConnection()
{
    $host = 'localhost';
    $port = '5432';
    $dbname = 'empresa_bodegas'; // Nombre de la BD en PostgreSQL
    $user = 'dev_bodegas';       // Usuario de la BD
    $password = 'theluk70';      // La contraseña actual

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión a la base de datos: " . $e->getMessage());
    }
}
?>