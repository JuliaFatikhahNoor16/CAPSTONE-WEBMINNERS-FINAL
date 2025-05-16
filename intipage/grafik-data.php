<?php
include '../config/koneksi.php';

$query = mysqli_query($connection, "
    SELECT 
        MONTH(Date) AS bulan,
        SUM(Quantity) AS quantity
    FROM dashboard
    WHERE MONTH(Date) BETWEEN 1 AND 3
    GROUP BY MONTH(Date)
    ORDER BY MONTH(Date) ASC
");

$bulanIndo = [
    1 => "Januari", 2 => "Februari", 3 => "Maret",
    4 => "April", 5 => "Mei", 6 => "Juni",
    7 => "Juli", 8 => "Agustus", 9 => "September",
    10 => "Oktober", 11 => "November", 12 => "Desember"
];

$data = [];

while ($row = mysqli_fetch_assoc($query)) {
    $data[] = [
        'tanggal' => $bulanIndo[(int)$row['bulan']],
        'quantity' => (int)$row['quantity']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
