<?php
session_start(); 
$servername = "localhost";
$username = "root";
$password = "192005";
$dbname = "activos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    
    $sql = "SELECT id, usuario, nombre, clave, nivel FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        die("Usuario no encontrado.");
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = $_POST['usuario'];
        $nombre = $_POST['nombre'];
        $clave = $_POST['clave'];
        $nivel = intval($_POST['nivel']);
        
        
        $updateSql = "UPDATE usuarios SET usuario = ?, nombre = ?, clave = ?, nivel = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssii", $usuario, $nombre, $clave, $nivel, $id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo "Usuario actualizado con éxito.";
            header("Location: usuario.php"); 
        } else {
            echo "Error al actualizar el usuario.";
        }
    }
} else {
    die("ID de usuario no proporcionado.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
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
            color: blue;
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
            border: 1px solid #ccc; 
            padding: 10px; 
            border-radius: 4px; 
            font-size: 16px; 
            border: 1px solid #007BFF;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: blue;
            font-weight: bold;
            text-align: left; 
        }
        input,
        button {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            text-align: left; 
        }
        button {
            background-color: #FF6600;
            color: white;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        button:hover {
            background-color: #E65C00;
        }
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            text-align: left; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Usuario</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($user['usuario']); ?>" required>
            </div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
            </div>
            <div class="form-group">
                <label for="clave">Contraseña:</label>
                <input type="password" id="clave" name="clave" value="<?php echo htmlspecialchars($user['clave']); ?>">
                <small>Deja en blanco si no deseas cambiar la contraseña.</small>
            </div>
            <div class="form-group">
                <label for="nivel">Nivel:</label>
                <input type="number" id="nivel" name="nivel" value="<?php echo htmlspecialchars($user['nivel']); ?>" min="1" max="3" required>
            </div>
            <div class="form-group">
                <button type="submit">Actualizar</button>
            </div>
        </form>
    </div>
    </body>
    </html>
