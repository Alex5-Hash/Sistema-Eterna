?php
$servername = "localhost";
$username = "root"; 
$password = "192005"; 
$dbname = "activos";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo "ID no válido.";
    $conn->close();
    exit();
}

$sql = "DELETE FROM eterna WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: usuario.php");
    exit();
} else {
    echo "Error al eliminar: " . $conn->error;
}

$conn->close();
?>