<?php
$servername = "localhost";
$username = "root"; 
$password = "192005"; 
$dbname = "activos";

$conn = new mysqli($servername, $username, $password, $dbname); 
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$message = ""; 
$Categoria = $Codigo = $DescripcionActivo = $Serie = $Ubicacion = $Fecha = $Pais = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Categoria = $conn->real_escape_string($_POST['categoria']);
    $Codigo = $conn->real_escape_string($_POST['Codigo']);
    $DescripcionActivo = $conn->real_escape_string($_POST['descripcion']);
    $Serie = $conn->real_escape_string($_POST['serie']);
    $Ubicacion = $conn->real_escape_string($_POST['ubicacion']);
    $Fecha = $conn->real_escape_string($_POST['fecha']);
    $Pais = $conn->real_escape_string($_POST['pais']);


    $check_sql = "SELECT * FROM eterna WHERE Codigo='$Codigo'
                  UNION
                  SELECT * FROM nica WHERE Codigo='$Codigo'";

    $check_result = $conn->query($check_sql);

    if ($check_result && $check_result->num_rows > 0) {
       
        $message = "El código ya ha sido registrado. Por favor, ingrese uno nuevo.";
        $Codigo = ""; 
    } else {
        
        if ($Pais == 'Honduras') {
            $sql = "INSERT INTO eterna (Categoria, Codigo, Activo, Serie, Ubicacion, Fecha, Pais) 
                    VALUES ('$Categoria', '$Codigo', '$DescripcionActivo', '$Serie', '$Ubicacion', '$Fecha', '$Pais')";
        } elseif ($Pais == 'Nicaragua') {
            $sql = "INSERT INTO nica (Categoria, Codigo, Activo, Serie, Ubicacion, Fecha, Pais) 
                    VALUES ('$Categoria', '$Codigo', '$DescripcionActivo', '$Serie', '$Ubicacion', '$Fecha', '$Pais')";
        }

        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error al agregar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de Equipo de Cómputo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center; 
            flex-direction: column; 
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        img {
            width: 200px;
            height: 100px;
            margin-bottom: 20px;
        }

        h1 {
            color: #0d5588;
            font-size: 36px;
            margin: 0;
        }

        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        form h2 {
            color: #0d5588;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        form input, form select {
            padding: 10px;
            margin-bottom: 15px;
            width: calc(100% - 22px);
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        form button {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #0d5588;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 48%; 
            margin: 1%; 
        }

        form button:hover {
            background-color: #094d6c;
        }

        .alert {
            color: red;
            margin: 10px 0; 
        }

        .cancel-button {
            background-color: #f44336; 
        }
    </style>
</head>
<body>
<div class="container">
    <img src="eterna.png" alt="Descripción de la imagen">
    <h1>Equipo de Cómputo Eterna</h1>
</div>

<form action="" method="post">
    <h2>Agregar Nuevo Activo</h2>

    <label for="categoria">Categoría:</label>
    <input type="text" id="categoria" name="categoria" value="<?php echo htmlspecialchars($Categoria); ?>" required>

    <label for="Codigo">Código:</label>
    <input type="text" id="Codigo" name="Codigo" value="<?php echo htmlspecialchars($Codigo); ?>" required>
    <?php if ($message): ?>
        <div class="alert"><?php echo $message; ?></div>
    <?php endif; ?>

    <label for="descripcion">Descripción Activo:</label>
    <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($DescripcionActivo); ?>" required>

    <label for="serie">Serie:</label>
    <input type="text" id="serie" name="serie" value="<?php echo htmlspecialchars($Serie); ?>" required>

    <label for="ubicacion">Ubicación:</label>
    <input type="text" id="ubicacion" name="ubicacion" value="<?php echo htmlspecialchars($Ubicacion); ?>" required>

    <label for="fecha">Fecha:</label>
    <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($Fecha); ?>" required>

    <label for="pais">País:</label>
    <select id="pais" name="pais" required>
        <option value="" disabled <?php echo $Pais ? '' : 'selected'; ?>>Seleccione un país</option>
        <option value="Honduras" <?php echo ($Pais == 'Honduras') ? 'selected' : ''; ?>>Honduras</option>
        <option value="Nicaragua" <?php echo ($Pais == 'Nicaragua') ? 'selected' : ''; ?>>Nicaragua</option>
    </select>

    <div style="display: flex; justify-content: space-between;">
        <button type="submit">Guardar Cambios</button>
        <button type="button" class="cancel-button" onclick="window.location.href='index.php'">Cancelar</button>
    </div>
</form>

</body>
</html>












