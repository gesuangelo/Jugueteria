<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "TOYS";

try {
    $conn = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $conn->exec("USE $dbname");

    $conn->exec("CREATE TABLE IF NOT EXISTS USUARIOS (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100),
        email VARCHAR(100) UNIQUE,
        contrasena VARCHAR(255),
        direccion VARCHAR(200),
        telefono VARCHAR(20)
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS JUGUETES (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100),
        descripcion TEXT,
        edad INT,
        precio DECIMAL(10,2),
        cantidad_inventario INT
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS CARRITO (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        ID_usuario INT,
        ID_producto INT,
        cantidad INT,
        monto_total DECIMAL(10,2),
        FOREIGN KEY (ID_usuario) REFERENCES USUARIOS(ID),
        FOREIGN KEY (ID_producto) REFERENCES JUGUETES(ID)
    )");
} catch (PDOException $e) {
    die("Error de base de datos: " . $e->getMessage());
}
?>
