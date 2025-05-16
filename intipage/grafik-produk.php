<?php
include '../config/koneksi.php';

$request_type = $_GET['type'] ?? 'highest'; // 'highest' atau 'lowest'

// Validasi parameter
$valid_types = ['highest', 'lowest'];
$request_type = in_array($request_type, $valid_types) ? $request_type : 'highest';

// Tentukan query berdasarkan tipe request
if ($request_type === 'highest') {
    $query = "SELECT Product_Name, SUM(Quantity) as Value 
            FROM dashboard 
            GROUP BY Product_Name 
            ORDER BY Value DESC 
            LIMIT 10";
} else {
    $query = "SELECT Product_Name, SUM(Quantity) as Value 
            FROM dashboard 
            GROUP BY Product_Name 
            ORDER BY Value ASC 
            LIMIT 10";
}

$data = [];
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query error: " . mysqli_error($connection));
}

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'product' => $row['Product_Name'],
        'value' => (float)$row['Value']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
