<?php

$servername = "localhost";  
$username = "root";   
$password = "192005"; 
$dbname = "activos"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $deleteSql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}


$sql = "SELECT id, usuario, nombre, clave, nivel FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .user-table {
            border-collapse: collapse;
            width: 100%;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        .user-table th, .user-table td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
        }
        .user-table th {
            background-color: #CD5328; 
            color: white;
        }
        .user-table tr:nth-child(even) {
            background-color: #F2F2F2; 
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-edit {
            background-color: #F2F2F2; 
            color: black;
        }
        .btn-delete {
            background-color: #E74C3C; 
            color: white;
        }
        .btn-add {
            background-color: #008CBA; 
            text-decoration: none;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn-add:hover {
            background-color: #005f6b; 
        }
    </style>
</head>
<body>

<center><h1>Datos de Usuarios</h1></center>

<table class="user-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Nivel</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
           
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["usuario"] . "</td>";
                echo "<td>" . $row["nombre"] . "</td>";
                echo "<td>" . $row["nivel"] . "</td>";
                echo "<td>
                        <a href='editar.php?id=" . $row["id"] . "' class='btn btn-edit'>Editar</a>
                        <a href='?delete=" . $row["id"] . "' class='btn btn-delete' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este usuario?\")'>Eliminar</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>0 resultados</td></tr>";
        }

        // Cerrar conexión
        $conn->close();
        ?>
    </tbody>
</table>
<div class="volver">
                <a href="index.php">Volver</a>
            </div>
</body>
</html>



