<?php
header('Content-Type: application/json');
include '../config/koneksi.php';

try {
    $query = mysqli_query(
        $connection,
        "SELECT Category, SUM(Total) as Total
        FROM dashboard
        GROUP BY Category
        ORDER BY Total DESC"
    );

    if (!$query) {
        throw new Exception("Query error: " . mysqli_error($connection));
    }

    $data = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = [
            'Category' => $row['Category'],
            'Total' => (float)$row['Total'] * 1000 // jika di DB masih ribuan
        ];
    }

    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ]);
}
