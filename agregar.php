<?php

$servername = "localhost";
$username = "root";
$password = "192005";
$dbname = "activos";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $nombre = $_POST['nombre'];
    $clave = $_POST['clave'];
    $nivel = intval($_POST['nivel']);

    $insertSql = "INSERT INTO usuarios (usuario, nombre, clave, nivel) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("sssi", $usuario, $nombre, $clave, $nivel);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: usuario.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <style>
  
        body {
            font-family: Arial, sans-serif;
            background-color: #F0F0F0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        h1 {
            text-align: center;
            color: black;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            border: 1px solid #007BFF;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: left;
        }
        .form-group {
            width: 100%;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }
        input {
            background-color: #F0F0F0;
            border: 1px solid #007BFF;
            padding: 10px;
            border-radius: 4px;
            font-size: 16px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: blue;
            font-weight: bold;
            text-align: left;
        }
        button {
            background-color: #FF6600;
            color: white;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            padding: 15px;
            border: none;
            border-radius: 5px;
            width: 100%;
        }
        button:hover {
            background-color: #E65C00;
        }
        .volver {
            text-align: center;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Agregar Nuevo Usuario</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label> 
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="clave">Clave:</label>
                <input type="password" id="clave" name="clave" required>
            </div>
            <div class="form-group">
                <label for="nivel">Nivel:</label>
                <input type="number" id="nivel" name="nivel" required>
            </div>
            <div class="form-group">
                <button type="submit">Agregar Usuario</button>
            </div>
            <div class="volver">
                <a href="index.php">Volver</a>
            </div>
        </form>
    </div>
</body>
</html>

