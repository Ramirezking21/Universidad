<!-- index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h2>Registro de Ventas</h2>
                <form action="procesar.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">DNI</label>
                        <input type="text" class="form-control" name="dni" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <input type="text" class="form-control" name="producto" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio Unitario</label>
                        <input type="number" step="0.01" class="form-control" name="precio_unitario" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" class="form-control" name="cantidad" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar Venta</button>
                </form>
            </div>
            <div class="col-md-6">
                <h2>Ventas Registradas</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Producto</th>
                            <th>P.U.</th>
                            <th>Cant.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include 'conexion.php';
                        $sql = "SELECT * FROM ventas";
                        $result = $conn->query($sql);
                        
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['nombre'] . "</td>";
                            echo "<td>" . $row['dni'] . "</td>";
                            echo "<td>" . $row['producto'] . "</td>";
                            echo "<td>" . $row['precio_unitario'] . "</td>";
                            echo "<td>" . $row['cantidad'] . "</td>";
                            echo "<td>" . ($row['precio_unitario'] * $row['cantidad']) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <a href="generar_pdf.php" class="btn btn-success">Generar PDF</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// conexion.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bd_tienda";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// procesar.php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $dni = $_POST['dni'];
    $producto = $_POST['producto'];
    $precio_unitario = $_POST['precio_unitario'];
    $cantidad = $_POST['cantidad'];
    
    $sql = "INSERT INTO ventas (nombre, dni, producto, precio_unitario, cantidad)
            VALUES ('$nombre', '$dni', '$producto', $precio_unitario, $cantidad)";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// generar_pdf.php
require('fpdf/fpdf.php');
include 'conexion.php';

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial','B',15);
        $this->Cell(0,10,'Reporte de Ventas',0,1,'C');
        $this->Ln(10);
        
        $this->SetFont('Arial','B',12);
        $this->Cell(40,10,'Nombre',1);
        $this->Cell(25,10,'DNI',1);
        $this->Cell(40,10,'Producto',1);
        $this->Cell(30,10,'P.U.',1);
        $this->Cell(20,10,'Cant.',1);
        $this->Cell(30,10,'Total',1);
        $this->Ln();
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

$sql = "SELECT * FROM ventas";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $pdf->Cell(40,10,$row['nombre'],1);
    $pdf->Cell(25,10,$row['dni'],1);
    $pdf->Cell(40,10,$row['producto'],1);
    $pdf->Cell(30,10,$row['precio_unitario'],1);
    $pdf->Cell(20,10,$row['cantidad'],1);
    $pdf->Cell(30,10,$row['precio_unitario'] * $row['cantidad'],1);
    $pdf->Ln();
}

$pdf->Output();
?>