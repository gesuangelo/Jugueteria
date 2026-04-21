<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $entidad = $_POST['tipo_entidad'] ?? '';

    if ($entidad === 'usuario') {
        $nombre = htmlspecialchars($_POST['nombre'] ?? '');
        $email = htmlspecialchars($_POST['email'] ?? '');
        $passwordHash = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);
        $direccion = htmlspecialchars($_POST['direccion'] ?? '');
        $telefono = htmlspecialchars($_POST['telefono'] ?? '');

        $mensaje = "Datos de usuario $nombre recibidos para procesar.";
    } elseif ($entidad === 'juguete') {
        $nombreJuguete = htmlspecialchars($_POST['nombre_juguete'] ?? '');
        $descripcion = htmlspecialchars($_POST['descripcion'] ?? '');
        $edad = (int)($_POST['edad'] ?? 0);
        $precio = (float)($_POST['precio'] ?? 0);
        $inventario = (int)($_POST['inventario'] ?? 0);

        $mensaje = "Datos del juguete $nombreJuguete recibidos para procesar.";
    } else {
        $mensaje = "No se recibio una entidad valida.";
    }
} else {
    $mensaje = "No se recibieron datos por metodo POST.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Procesamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h4">Procesamiento de Formularios</h1>
                <p class="alert alert-info mb-3"><?php echo $mensaje; ?></p>
                <p class="mb-1"><strong>Este script recupera datos con</strong> <code>$_POST</code>.</p>
                <p class="mb-0"><strong>Hash generado de contrasena:</strong> <code><?php echo $passwordHash ?? 'No aplica'; ?></code></p>
                <a href="formularios_registro.html" class="btn btn-primary mt-3">Volver a formularios</a>
            </div>
        </div>
    </main>
</body>
</html>
