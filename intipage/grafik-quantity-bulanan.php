<?php
include '../config/koneksi.php';

// Ambil data quantity bulanan (Januari-Maret)
$query = mysqli_query($connection, "SELECT 
    MONTH(Date) as Month, 
    SUM(Quantity) as Quantity_Monthly 
    FROM dashboard 
    WHERE MONTH(Date) BETWEEN 1 AND 3
    GROUP BY MONTH(Date) 
    ORDER BY MONTH(Date) ASC");

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
