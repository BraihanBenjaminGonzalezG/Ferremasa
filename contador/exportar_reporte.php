<?php
// /contador/exportar_reporte.php
session_start();
include __DIR__ . '/../includes/db.php';

// Sólo rol contador
if ($_SESSION['rol'] !== 'contador') {
    header("Location: ../login.php");
    exit;
}

// Cabeceras para descarga como Excel y UTF-8 BOM
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename=reporte_financiero.csv');

// Imprimimos BOM para UTF-8
echo "\xEF\xBB\xBF";

// Abrimos “salida”
$out = fopen('php://output', 'w');

// 1) Calculamos total confirmado
$sqlTotal = "
  SELECT SUM(dp.subtotal) AS total_confirmado
  FROM pagos pay
  JOIN detalle_pedido dp ON dp.pedido_id = pay.pedido_id
  WHERE pay.estado = 'confirmado'
";
$rowTotal = $conn->query($sqlTotal)->fetch_assoc();
$total = $rowTotal['total_confirmado'] ?? 0;

// 2) Escribimos encabezados y datos con ‘;’
fputcsv($out, ['REPORTE FINANCIERO'], ';');
fputcsv($out, ['Generado el', date('Y-m-d H:i:s')], ';');
fputcsv($out, [], ';');

fputcsv($out, ['Total Confirmado', number_format($total, 2, ',', '.')], ';');
fputcsv($out, [], ';');

// 3) Cabeceras del desglose mensual
fputcsv($out, ['Año', 'Mes', 'Total Pagado'], ';');

// 4) Obtenemos el reporte mensual
$sqlMeses = "
  SELECT 
    YEAR(pay.fecha) AS anio, 
    MONTH(pay.fecha) AS mes, 
    SUM(dp.subtotal) AS total
  FROM pagos pay
  JOIN detalle_pedido dp ON dp.pedido_id = pay.pedido_id
  WHERE pay.estado = 'confirmado'
  GROUP BY anio, mes
  ORDER BY anio DESC, mes DESC
";
$result = $conn->query($sqlMeses);

// 5) Volcamos cada fila con ‘;’
while ($r = $result->fetch_assoc()) {
    // Formateamos total con coma decimal
    $valor = number_format($r['total'], 2, ',', '.');
    fputcsv($out, [$r['anio'], $r['mes'], $valor], ';');
}

fclose($out);
exit;
