<?php
require 'conexion.php';
session_start();

$id_usuario_actual = 1;

$conn->exec("INSERT IGNORE INTO USUARIOS (ID, nombre, email, contrasena, direccion, telefono)
VALUES (1, 'Cliente Demo', 'cliente@demo.com', 'hash_demo', 'Direccion Demo 123', '999999999')");

$conn->exec("INSERT INTO JUGUETES (nombre, descripcion, edad, precio, cantidad_inventario)
SELECT * FROM (
    SELECT 'Figura de Accion', 'Coleccion clasica', 6, 12990.00, 20
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM JUGUETES WHERE nombre = 'Figura de Accion')");

$conn->exec("INSERT INTO JUGUETES (nombre, descripcion, edad, precio, cantidad_inventario)
SELECT * FROM (
    SELECT 'Rompecabezas 3D', 'Desafio creativo', 8, 9490.00, 15
) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM JUGUETES WHERE nombre = 'Rompecabezas 3D')");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['registrar_carrito'])) {
        $id_juguete = (int)$_POST['id_juguete'];
        $cantidad = (int)$_POST['cantidad'];

        if ($cantidad > 0) {
            $stmtPrecio = $conn->prepare("SELECT precio FROM JUGUETES WHERE ID = ?");
            $stmtPrecio->execute([$id_juguete]);
            $juguete = $stmtPrecio->fetch(PDO::FETCH_ASSOC);

            if ($juguete) {
                $montoTotal = $juguete['precio'] * $cantidad;

                $stmtInsert = $conn->prepare("INSERT INTO CARRITO (ID_usuario, ID_producto, cantidad, monto_total) VALUES (?, ?, ?, ?)");
                $stmtInsert->execute([$id_usuario_actual, $id_juguete, $cantidad, $montoTotal]);
            }
        }
    }

    if (isset($_POST['modificar_registro'])) {
        $idCarrito = (int)$_POST['id_registro_carrito'];
        $cantidadNueva = (int)$_POST['cantidad_nueva'];

        if ($cantidadNueva > 0) {
            $stmtConsulta = $conn->prepare("SELECT C.ID_producto, J.precio FROM CARRITO C JOIN JUGUETES J ON C.ID_producto = J.ID WHERE C.ID = ? AND C.ID_usuario = ?");
            $stmtConsulta->execute([$idCarrito, $id_usuario_actual]);
            $filaActual = $stmtConsulta->fetch(PDO::FETCH_ASSOC);

            if ($filaActual) {
                $montoActualizado = $filaActual['precio'] * $cantidadNueva;
                $stmtUpdate = $conn->prepare("UPDATE CARRITO SET cantidad = ?, monto_total = ? WHERE ID = ? AND ID_usuario = ?");
                $stmtUpdate->execute([$cantidadNueva, $montoActualizado, $idCarrito, $id_usuario_actual]);
            }
        }
    }

    if (isset($_POST['eliminar_registro'])) {
        $idCarrito = (int)$_POST['id_registro_carrito'];
        $stmtDel = $conn->prepare("DELETE FROM CARRITO WHERE ID = ? AND ID_usuario = ?");
        $stmtDel->execute([$idCarrito, $id_usuario_actual]);
    }
}

$stmtJuguetes = $conn->query("SELECT ID, nombre, precio FROM JUGUETES ORDER BY nombre ASC");
$juguetes = $stmtJuguetes->fetchAll(PDO::FETCH_ASSOC);

$stmtLista = $conn->prepare("SELECT C.ID, J.nombre, C.cantidad, C.monto_total FROM CARRITO C JOIN JUGUETES J ON C.ID_producto = J.ID WHERE C.ID_usuario = ? ORDER BY C.ID DESC");
$stmtLista->execute([$id_usuario_actual]);
$registros_carrito = $stmtLista->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito en Base de Datos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">Realizar Pedido en Linea</h3>
            <a href="index.html" class="btn btn-outline-secondary">Volver al inicio</a>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form action="" method="POST" id="formPedido" class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Juguete</label>
                        <select class="form-select" name="id_juguete" required>
                            <?php foreach ($juguetes as $item): ?>
                                <option value="<?php echo $item['ID']; ?>">
                                    <?php echo $item['nombre']; ?> - $<?php echo $item['precio']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cantidad</label>
                        <input class="form-control" type="number" name="cantidad" id="cantPedido" min="1" value="1" required>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" type="submit" name="registrar_carrito">Confirmar y Calcular Total</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="h5">Pedidos Activos en Base de Datos</h3>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Monto Total</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($registros_carrito) > 0): ?>
                                <?php foreach ($registros_carrito as $fila): ?>
                                    <tr>
                                        <td><?php echo $fila['nombre']; ?></td>
                                        <td>
                                            <form action="" method="POST" class="d-flex gap-2">
                                                <input type="hidden" name="id_registro_carrito" value="<?php echo $fila['ID']; ?>">
                                                <input class="form-control form-control-sm" type="number" name="cantidad_nueva" min="1" value="<?php echo $fila['cantidad']; ?>" style="width: 85px;">
                                                <button class="btn btn-sm btn-warning" type="submit" name="modificar_registro">Modificar</button>
                                            </form>
                                        </td>
                                        <td>$<?php echo number_format((float)$fila['monto_total'], 2, ',', '.'); ?></td>
                                        <td>
                                            <form action="" method="POST">
                                                <input type="hidden" name="id_registro_carrito" value="<?php echo $fila['ID']; ?>">
                                                <button class="btn btn-sm btn-danger" type="submit" name="eliminar_registro">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No hay pedidos cargados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
    document.getElementById('formPedido').addEventListener('submit', function (e) {
        const cant = Number(document.getElementById('cantPedido').value);
        if (cant <= 0 || Number.isNaN(cant)) {
            alert('La cantidad debe ser mayor a cero.');
            e.preventDefault();
        }
    });
    </script>
</body>
</html>
