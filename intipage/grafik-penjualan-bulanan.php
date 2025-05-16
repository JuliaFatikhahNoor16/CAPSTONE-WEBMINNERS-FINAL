<?php
header('Content-Type: application/json');
include '../config/koneksi.php';

try {
    $query = mysqli_query(
        $connection,
        "SELECT 
            Date,
            DAY(Date) as Day,
            MONTH(Date) as Month,
            SUM(Total) as Total
        FROM dashboard
        WHERE MONTH(Date) IN (1, 2, 3)  /* Hanya ambil Jan-Mar */
        GROUP BY Date
        ORDER BY Date ASC"
    );

    if (!$query) {
        throw new Exception("Query error: " . mysqli_error($connection));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = [
            'Date' => $row['Date'],
            'Day' => (int)$row['Day'],
            'Month' => (int)$row['Month'],
            'Total' => (float)$row['Total'] * 1000 // konversi jika data di DB dalam ribuan
        ];
    }

    if (empty($data)) {
        throw new Exception("Tidak ada data penjualan");
    }

    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ]);
}
