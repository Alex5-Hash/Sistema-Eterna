<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$nivel = $_SESSION['nivel'];

if ($nivel != 1 && $nivel != 2 && $nivel != 3) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $servername = "localhost";
    $username = "root";
    $password = "192005";
    $dbname = "activos";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    
    $sql = "SELECT * FROM eterna WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        
        $papeleraSql = "INSERT INTO papelera (Categoria, Codigo, `Activo`, Serie, Ubicacion, Fecha, Pais)
                        VALUES ('{$row['Categoria']}', '{$row['Codigo']}', '{$row['Activo']}', '{$row['Serie']}', '{$row['Ubicacion']}', '{$row['Fecha']}', '{$row['Pais']}')";

        if ($conn->query($papeleraSql) === TRUE) {
            
            $deleteSql = "DELETE FROM eterna WHERE id = $id";
            $conn->query($deleteSql);
            header("Location: index.php"); 
        } else {
            echo "Error al mover a papelera: " . $conn->error;
        }
    } else {
        echo "Registro no encontrado.";
    }

    $conn->close();
} else {
    echo "ID no especificado.";
}
?>


