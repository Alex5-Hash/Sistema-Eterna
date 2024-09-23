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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];
    
   
    $sql = "SELECT usuario, clave, nivel FROM usuarios WHERE usuario = ? AND clave = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuario, $clave);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nivel'] = $user_data['nivel'];
        
        // Redirige según el nivel del usuario
        if ($user_data['nivel'] == 1) {
            header("Location: index.php");
        } elseif ($user_data['nivel'] == 2) {
            header("Location: index.php"); 
        } elseif ($user_data['nivel'] == 3) {
            header("Location: index.php"); 
        }
        exit();
    } else {
        
        $usuario_sql = "SELECT * FROM usuarios WHERE usuario = ?";
        $stmt = $conn->prepare($usuario_sql);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $usuario_result = $stmt->get_result();
        
        if ($usuario_result->num_rows > 0) {
            $_SESSION['error_message'] = "Contraseña incorrecta.";
            $_SESSION['error_field'] = "clave";
        } else {
            $_SESSION['error_message'] = "Usuario incorrecto.";
            $_SESSION['error_field'] = "usuario";
        }
        header("Location: ".$_SERVER['PHP_SELF']); 
        exit();
    }
}

$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
$error_field = isset($_SESSION['error_field']) ? $_SESSION['error_field'] : '';
unset($_SESSION['error_message']); 
unset($_SESSION['error_field']); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
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
        <h1>Bienvenido a SAE</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" name="usuario" required>
                <span id="usuario-error" class="error-message"></span>
            </div>
            <div class="form-group">
                <label for="clave">Contraseña:</label>
                <input type="password" id="clave" name="clave" required>
                <span id="clave-error" class="error-message"></span>
            </div>
            <div class="form-group">
                <button type="submit">Ingresar</button>
            </div>
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var errorMessage = "<?php echo $error_message; ?>";
                var errorField = "<?php echo $error_field; ?>";
                if (errorMessage) {
                    if (errorField === 'usuario') {
                        document.getElementById('usuario-error').textContent = errorMessage;
                    } else if (errorField === 'clave') {
                        document.getElementById('clave-error').textContent = errorMessage;
                    }
                }
            });
        </script>
    </div>
</body>
</html>

