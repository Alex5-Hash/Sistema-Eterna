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

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 200; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $limit; 

$total_result = $conn->query("SELECT COUNT(*) as total FROM papelera");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit); 

$sql = "SELECT * FROM papelera LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $conn->query("DELETE FROM papelera WHERE id = $delete_id");
    header("Location: papelera.php");
    exit();
}

if (isset($_GET['restore_id'])) {
    $restore_id = (int)$_GET['restore_id'];
    $result = $conn->query("SELECT * FROM papelera WHERE id = $restore_id");
    $row = $result->fetch_assoc();

    if ($row) {
        $Categoria = $row['categoria'];
        $Codigo = $row['Codigo'];
        $DescripcionActivo = $row['Activo'];
        $Serie = $row['Serie'];
        $Ubicacion = $row['Ubicacion'];
        $Fecha = $row['Fecha'];
        $Pais = $row['Pais'];

        // Verificamos el país para decidir a qué tabla insertar
        if ($Pais === 'HN') {
            $insert_sql = "INSERT INTO eterna (Categoria, Codigo, Activo, Serie, Ubicacion, Fecha, Pais) 
                           VALUES ('$Categoria', '$Codigo', '$DescripcionActivo', '$Serie', '$Ubicacion', '$Fecha', '$Pais')";
        } elseif ($Pais === 'Nicaragua') {
            $insert_sql = "INSERT INTO nica (Categoria, Codigo, Activo, Serie, Ubicacion, Fecha, Pais) 
                           VALUES ('$Categoria', '$Codigo', '$DescripcionActivo', '$Serie', '$Ubicacion', '$Fecha', '$Pais')";
        } else {
            // Si no es ni HN ni Nicaragua, puedes manejarlo como desees
            // Por ejemplo, puedes omitir la inserción o guardar en una tabla diferente
            header("Location: papelera.php");
            exit();
        }

        $conn->query($insert_sql);
        $conn->query("DELETE FROM papelera WHERE id = $restore_id");
        header("Location: papelera.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelera de Activos</title>
    <link rel="stylesheet" href="zambo.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .header-container {
            padding: 20px;
            text-align: center;
        }
        .assets-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .assets-table th, .assets-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .btn {
            padding: 6px 12px;
            margin: 4px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn-restore {
            background-color: #76c7c0;
        }
        .btn-restore:hover {
            background-color: #4CAF50;
        }
        .btn-delete {
            background-color: #ff6b6b;
        }
        .btn-delete:hover {
            background-color: #f44336;
        }
        .btn-back {
            background-color: #0078D4;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-back:hover {
            background-color: #005a9e;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <h1>Papelera de Activos</h1>
    </div>

    <table class="assets-table">
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Codigo</th>
                <th>Descripcion Activo</th>
                <th>Serie</th>
                <th>Ubicacion</th>
                <th>Fecha</th>
                <th>Pais</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['categoria']}</td>
                            <td>{$row['Codigo']}</td>
                            <td>{$row['Activo']}</td>
                            <td>{$row['Serie']}</td>
                            <td>{$row['Ubicacion']}</td>
                            <td>{$row['Fecha']}</td>
                            <td>{$row['Pais']}</td>
                            <td>
                                <a href='?restore_id={$row['id']}' class='btn btn-restore'>Restaurar</a>
                                <a href='?delete_id={$row['id']}' class='btn btn-delete'>Eliminar Definitivamente</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No se encontraron registros</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1&limit=<?php echo $limit; ?>">Primero</a>
            <a href="?page=<?php echo $page - 1; ?>&limit=<?php echo $limit; ?>">Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>" class="<?php if ($i == $page) echo 'active'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&limit=<?php echo $limit; ?>">Siguiente</a>
            <a href="?page=<?php echo $total_pages; ?>&limit=<?php echo $limit; ?>">Último</a>
        <?php endif; ?>
    </div>

    <a href="index.php" class="btn-back">Regresar</a>
    
</body>
</html>








