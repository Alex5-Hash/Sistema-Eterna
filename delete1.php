<?php 
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$nivel = $_SESSION['nivel'];

if ($nivel < 1 || $nivel > 3) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "192005";
$dbname = "activos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $sql = "SELECT * FROM nica WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        
        $sql_insert = "INSERT INTO papelera (categoria, Codigo, Activo, Serie, Ubicacion, Fecha, Pais) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sssssss", $row['Categoria'], $row['Codigo'], $row['Activo'], $row['Serie'], $row['Ubicacion'], $row['Fecha'], $row['Pais']);
        $stmt_insert->execute();

     
        $sql_delete = "DELETE FROM nica WHERE id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);
        $stmt_delete->execute();

        header("Location: nica.php?msg=Registro eliminado y movido a papelera");
    } else {
        header("Location: nica.php?msg=Registro no encontrado");
    }
} else {
    header("Location: nica.php?msg=ID no especificado");
}

$conn->close();
