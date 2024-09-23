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
    die("Conexion fallida: " . $conn->connect_error);
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$offset = ($page - 1) * $limit; 

$filter_sql = '';
if (isset($_GET['filter']) && !empty($_GET['filter'])) {
    $filter = $conn->real_escape_string($_GET['filter']);
    $filter_type = isset($_GET['filter_type']) ? $conn->real_escape_string($_GET['filter_type']) : 'Codigo';
    $filter_sql = " WHERE $filter_type LIKE '%$filter%'";
}

$total_result = $conn->query("SELECT COUNT(*) as total FROM eterna" . $filter_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit); 

$sql = "SELECT * FROM eterna" . $filter_sql . " LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Activos</title>
    <link rel="stylesheet" href="zambo.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .header-container {
            width: 100%;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            padding: 10px 0;
            display: flex;
            flex-direction: column;
        }
        .header-content {
            text-align: center;
            padding: 10px;
        }
        nav {
            margin: 0;
            display: flex;
            justify-content: flex-start;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        nav li {
            display: inline-block;
            position: relative;
        }
        nav a {
            display: block;
            padding: 15px 20px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        nav a:hover {
            background-color: #CD5328;
            color: white;
        }
        .submenu {
            display: none;
            position: absolute;
            background-color: #ecf0f1;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 10;
            min-width: 160px;
        }
        .submenu li {
            display: block;
            padding: 10px;
        }
        .submenu li a {
            color: #333;
            text-decoration: none;
        }
        .submenu li a:hover {
            background-color: #bdc3c7;
        }
        nav li:hover .submenu {
            display: block; 
        }
        .records-per-page {
            margin-left: auto;
            padding: 10px;
        }
        .assets-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 180px;
            margin-bottom: 20px;
        }
        .assets-table th, .assets-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .assets-table th {
            background-color: #CD5328;
            color: white;
        }
        .btn {
            padding: 6px 12px;
            margin: 4px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn-edit {
            background-color: #76c7c0;
        }
        .btn-edit:hover {
            background-color: red;
        }
        .btn-delete {
            background-color: #6495ED;
        }
        .btn-delete:hover {
            background-color: #f44336;
        }
        .btn-view {
            background-color: #2196F3;
        }
        .pagination {
            margin: 20px 0;
            text-align: center;
        }
        .pagination a {
            margin: 0 10px; 
            padding: 8px 12px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            display: inline-block;
        }
        .pagination a:hover {
            background-color: #1976D2;
        }
        .pagination .active {
            background-color: #CD5328;
            pointer-events: none;
        }
        @media (max-width: 600px) {
            nav ul {
                flex-direction: column;
            }
            nav li {
                display: block;
            }
            .assets-table th, .assets-table td {
                font-size: 12px;
                padding: 8px;
            }
            .pagination a {
                font-size: 12px;
                padding: 5px;
            }
        }
        .filter-container {
            margin-top: 20px;
            padding: 20px;
            text-align: center;
            background-color: white;
            border-radius: 0px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .filter-container label {
            font-weight: bold;
            margin-right: 10px;
            color: #333;
        }
        .filter-container select,
        .filter-container input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
            width: 200px;
        }
        .filter-container button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            background-color: #2196F3;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .filter-container button:hover {
            background-color: #1976D2;
        }
        .filter-container .reset-button {
            background-color: #f44336; 
        }
        .filter-container .reset-button:hover {
            background-color: #d32f2f;
        }
        footer {
            background-color: #F0F0F0;
            color: #333;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .footer-column {
            flex: 1;
            padding: 10px;
        }
        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .footer-column ul {
            list-style: none;
            padding: 0;
        }
        .footer-column ul li {
            margin: 5px 0;
        }
        .footer-column ul li a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer-column ul li a:hover {
            color: #CD5328;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <div class="header-content">
            <img src="eterna.png" alt="Descripcion de la imagen" style="max-width: 100%; height: auto;">
            <h1>Equipo de Computo Eterna</h1>
        </div>
        <div class="records-per-page">
            <form method="GET" action="">
                <label for="limit">Registros por pagina:</label>
                <select name="limit" id="limit" onchange="this.form.submit()">
                    <option value="50" <?php if ($limit == 50) echo 'selected'; ?>>50</option>
                    <option value="100" <?php if ($limit == 100) echo 'selected'; ?>>100</option>
                    <option value="200" <?php if ($limit == 200) echo 'selected'; ?>>200</option>
                </select>
                <input type="hidden" name="page" value="<?php echo $page; ?>">
            </form>
        </div>
        <nav>
            <ul>
                <?php if ($nivel == 1): ?>
                    <li></li>
                <?php endif; ?>
                <li>
                    <a href="index.php">
                        <img src="casa.png" alt="inicio" style="width: 20px; vertical-align: middle;"> Inicio
                    </a>
                </li>
                <li>
                    <a href="index.php">
                        <img src="activo.png" alt="Activos" style="width: 20px; vertical-align: middle;"> Activos
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="add.php">
                                <img src="agg.png" alt="agregar" style="width: 20px; vertical-align: middle;"> Nuevo
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <img src="r.png" alt="Reportes" style="width: 20px; vertical-align: middle;"> Reportes
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="pa.php">
                                <img src="pdf.png" alt="Nuevo" style="width: 20px; vertical-align: middle;"> PDF
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#">
                        <img src="cn.png" alt="Configuracion" style="width: 20px; vertical-align: middle;"> Configuracion
                    </a>
                    <ul class="submenu">
                        <?php if ($nivel == 1): ?>
                            <li>
                                <a href="usuario.php">
                                    <img src="people.png" alt="Usuarios" style="width: 20px; vertical-align: middle;"> Usuarios
                                </a>
                            </li>
                            <li>
                                <a href="agregar.php">
                                    <img src="a.png" alt="Nuevo" style="width: 20px; vertical-align: middle;"> Nuevo
                                </a>
                            </li>
                            <li>
                                <a href="papelera.php">
                                    <img src="papel.png" alt="Papelera" style="width: 20px; vertical-align: middle;"> Papelera
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li>
                    <a href="login.php">
                        <img src="salir.png" alt="Salir" style="width: 20px; vertical-align: middle;"> Salir
                    </a>
                </li>
            </ul>
        </nav>
        <div class="filter-container">
            <form method="GET" action="">
                <input type="hidden" name="page" value="<?php echo $page; ?>">
                <input type="hidden" name="limit" value="<?php echo $limit; ?>">
                <label for="filter">Filtrar por:</label>
                <select name="filter_type">
                    <option value="Codigo">Codigo</option>
                    <option value="Categoria">Categoria</option>
                    <option value="Fecha">Fecha</option>
                    <option value="Pais">Pais</option>
                    <option value="Ubicacion">Ubicacion</option>
                    <option value="Serie">Serie</option>
                </select>
                <input type="text" name="filter" id="filter" placeholder="Ingrese su busqueda" value="<?php echo isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : ''; ?>">
                <button type="submit">Buscar</button>
                <button type="button" class="reset-button" onclick="window.location.href='?page=<?php echo $page; ?>&limit=<?php echo $limit; ?>'">Borrar Filtro</button>
            </form>
        </div>
    </div>
  
    <br>
    <br>
    <br><br><br><br><br><br><br><br><br>

    <table class="assets-table">
        <thead>
            <tr>
                <th class="Categoria-column">Categoria</th>
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
                            <td>{$row['Categoria']}</td>
                            <td>{$row['Codigo']}</td>
                            <td>{$row['Activo']}</td>
                            <td>{$row['Serie']}</td>
                            <td>{$row['Ubicacion']}</td>
                            <td>{$row['Fecha']}</td>
                            <td>{$row['Pais']}</td>
                            <td>";
                    if ($nivel == 1) {
                        echo "<a href='edit.php?id={$row['id']}' class='btn btn-edit'>Modificar</a>
                              <a href='delete.php?id={$row['id']}' class='btn btn-delete'>Eliminar</a>";
                    } elseif ($nivel == 2) {
                        echo "<a href='edit.php?id={$row['id']}' class='btn btn-edit'>Modificar</a>";
                    } elseif ($nivel == 3) {
                        echo "<a href='index.php?id={$row['id']}' class='btn btn-view'>Ver</a>";
                    }
                    echo "</td></tr>";
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
            <a href="?page=<?php echo $total_pages; ?>&limit=<?php echo $limit; ?>">Ultimo</a>
        <?php endif; ?>
    </div>
    <footer>
    <div class="footer-container" style="display: flex; align-items: center; justify-content: space-between;">
        <div class="footer-logo" style="flex: 0 0 auto;">
            <img src="eterna.png" alt="Logo Eterna" style="height: 100%; max-height: 100px;">
        </div>
        <div class="footer-columns" style="flex: 1; display: flex; justify-content: space-between;">
            <div class="footer-column">
                <h3>Productos</h3>
                <ul>
                    <li><a href="index.php">Activos</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Reportes</h3>
                <ul>
                    <li><a href="pa.php">Generar PDF</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Configuracion</h3>
                <ul>
                    <li><a href="usuario.php">Usuarios</a></li>
                    <li><a href="agregar.php">Nuevo Usuario</a></li>
                    <li><a href="papelera.php">Papelera</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Activo</h3>
                <ul>
                    <li><a href="add.php">Agregar Activos</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>


</body>
</html>
