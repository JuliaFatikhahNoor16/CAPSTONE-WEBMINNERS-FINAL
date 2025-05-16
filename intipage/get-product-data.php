<?php
// Turn off PHP error reporting that would break JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

try {
    // Include database connection
    include '../config/koneksi.php';

    if (!isset($_GET['product'])) {
        echo json_encode(['error' => 'Parameter product diperlukan']);
        exit;
    }

    $productName = $_GET['product'];

    // Check if connection is valid
    if (!$connection || mysqli_connect_errno()) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Periksa struktur tabel untuk melihat kolom yang tersedia
    $checkColumnsQuery = "SHOW COLUMNS FROM dashboard";
    $columnsResult = mysqli_query($connection, $checkColumnsQuery);

    if (!$columnsResult) {
        throw new Exception("Failed to check table structure: " . mysqli_error($connection));
    }

    $columns = [];
    while ($row = mysqli_fetch_assoc($columnsResult)) {
        $columns[] = $row['Field'];
    }

    // Bangun query berdasarkan ketersediaan kolom
    $orderByClause = "";
    if (in_array('created_at', $columns)) {
        $orderByClause = "ORDER BY created_at DESC";
    } elseif (in_array('date', $columns)) {
        $orderByClause = "ORDER BY date DESC";
    } elseif (in_array('Month', $columns) && in_array('Year', $columns)) {
        $orderByClause = "ORDER BY Year DESC, Month DESC";
    }

    // Query untuk mendapatkan data produk terbaru
    $query = "SELECT 
        Product_Name,
        AVG(Product_Price) as Product_Price,
        AVG(Quantity) as Quantity,
        AVG(Total) as Total,
        SUM(Quantity) as Quantity_Monthly
        FROM dashboard
        WHERE Product_Name = ?
        GROUP BY Product_Name
        $orderByClause
        LIMIT 1";

    $stmt = mysqli_prepare($connection, $query);
    if (!$stmt) {
        throw new Exception("Query preparation failed: " . mysqli_error($connection));
    }

    mysqli_stmt_bind_param($stmt, "s", $productName);
    $executed = mysqli_stmt_execute($stmt);

    if (!$executed) {
        throw new Exception("Query execution failed: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Tambahkan nilai-nilai untuk API
        // Gunakan produk name string -> CRC32 hashing utk mendapatkan kode produk konsisten
        // $row['Product_Code'] = abs(crc32($row['Product_Name']) % 1000); // Produk name -> numeric code
        $row['Month'] = (int)date('n'); // Bulan saat ini (numerik)
        $row['Day'] = (int)date('j');   // Hari saat ini
        $row['Year'] = (int)date('Y');  // Tahun saat ini

        // Convert numeric strings to actual numbers
        foreach ($row as $key => $value) {
            if (is_numeric($value)) {
                $row[$key] = $value + 0; // Convert to number
            }
        }
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Produk tidak ditemukan']);
    }
} catch (Exception $e) {
    // Return error as JSON
    echo json_encode(['error' => $e->getMessage()]);
}
