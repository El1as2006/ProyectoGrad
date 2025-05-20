<?php
require "../conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['name']);
    $gmail_institucional = trim($_POST['email']);
    $contraseña = trim($_POST['pass']);
    $rol = trim($_POST['user_type']);

    if (empty($nombre) || empty($gmail_institucional) || empty($contraseña) || empty($rol)) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Todos los campos son obligatorios y no deben contener solo espacios en blanco.'
                });
              </script>";
    } else {
        $contraseña_encriptada = password_hash($contraseña, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO usuarios (name, email, pass, user_type) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $gmail_institucional, $contraseña_encriptada, $rol);

        if ($stmt->execute()) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Nuevo registro creado exitosamente'
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error: " . $stmt->error . "'
                    });
                  </script>";
        }

        $stmt->close();
        $conn->close();
    }
} else {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Formulario no enviado correctamente.'
            });
          </script>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <form action="registro.php" method="POST">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="gmail_institucional">Correo Institucional:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="contraseña">Contraseña:</label><br>
        <input type="password" id="pass" name="pass" required><br><br>

        <label for="rol">Rol:</label><br>
        <select name="user_type" id="user_type" required>
            <option value="estudiante">Estudiante</option>
            <option value="docente">Docente</option>
            <option value="admin">Administrador</option>
            <option value="super_admin">Super Administrador</option>
        </select><br><br>

        <input type="submit" value="Registrarse">
    </form>
</body>
</html>
