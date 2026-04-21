<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = 1001;
    $_SESSION['usuario_nombre'] = "Cliente Verificado";
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['agregar_juguete'])) {
        $_SESSION['carrito'][] = [
            "id" => $_POST['id_juguete'],
            "nombre" => $_POST['nombre'],
            "cantidad" => max(1, (int)$_POST['cantidad'])
        ];
    }

    if (isset($_POST['actualizar_item'])) {
        $indice = (int)$_POST['indice_item'];
        $cantidadNueva = max(1, (int)$_POST['cantidad_nueva']);
        if (isset($_SESSION['carrito'][$indice])) {
            $_SESSION['carrito'][$indice]['cantidad'] = $cantidadNueva;
        }
    }

    if (isset($_POST['eliminar_item'])) {
        $indice = (int)$_POST['indice_item'];
        if (isset($_SESSION['carrito'][$indice])) {
            unset($_SESSION['carrito'][$indice]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        }
    }
}

if (isset($_GET['salir'])) {
    session_unset();
    session_destroy();
    header("Location: carrito_sesiones.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Sesiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h4 mb-0">Panel de Usuario: <?php echo $_SESSION['usuario_nombre']; ?></h2>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="index.html">Inicio</a>
                <a class="btn btn-outline-danger" href="?salir=1">Cerrar Sesion</a>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h3 class="h5">Catalogo Basico</h3>
                <form method="POST" action="" class="row g-2 align-items-end">
                    <input type="hidden" name="id_juguete" value="50">
                    <input type="hidden" name="nombre" value="Figura de Accion">
                    <div class="col-md-4">
                        <label class="form-label">Producto</label>
                        <input class="form-control" value="Figura de Accion" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cantidad</label>
                        <input class="form-control" type="number" name="cantidad" value="1" min="1">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" type="submit" name="agregar_juguete">Anadir al Carrito</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="h5">Estado Actual del Carrito</h3>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID Producto</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($_SESSION['carrito']) > 0): ?>
                                <?php foreach ($_SESSION['carrito'] as $i => $item): ?>
                                    <tr>
                                        <td><?php echo $item['id']; ?></td>
                                        <td><?php echo $item['nombre']; ?></td>
                                        <td><?php echo $item['cantidad']; ?></td>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <form method="POST" class="d-flex gap-2">
                                                    <input type="hidden" name="indice_item" value="<?php echo $i; ?>">
                                                    <input class="form-control form-control-sm" type="number" name="cantidad_nueva" min="1" value="<?php echo $item['cantidad']; ?>" style="width: 80px;">
                                                    <button class="btn btn-sm btn-warning" type="submit" name="actualizar_item">Modificar</button>
                                                </form>
                                                <form method="POST">
                                                    <input type="hidden" name="indice_item" value="<?php echo $i; ?>">
                                                    <button class="btn btn-sm btn-danger" type="submit" name="eliminar_item">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No hay productos en el carrito.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
