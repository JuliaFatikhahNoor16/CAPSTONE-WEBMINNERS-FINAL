<?php
require '../config/koneksi.php';

$csv = '../uploads/data_erthree (1).csv';

// Hentikan jika file tidak ditemukan
if (!file_exists($csv)) {
    die("File CSV tidak ditemukan.");
}

// Bersihkan tabel menu
mysqli_query($connection, "TRUNCATE TABLE menu");

// Baca file CSV
$file = fopen($csv, 'r');
$header = fgetcsv($file); // skip baris pertama

while (($row = fgetcsv($file)) !== false) {
    list($nama, $harga, $deskripsi, $kategori, $terjual, $gambar) = $row;

    $nama = mysqli_real_escape_string($connection, $nama);
    $deskripsi = mysqli_real_escape_string($connection, $deskripsi);
    $kategori = mysqli_real_escape_string($connection, $kategori);
    $gambar = mysqli_real_escape_string($connection, $gambar);
    $harga = (int)$harga;
    $terjual = (int)$terjual;

    $query = "INSERT INTO menu (nama, harga, deskripsi, gambar, kategori, terjual)
              VALUES ('$nama', $harga, '$deskripsi', '$gambar', '$kategori', $terjual)";
    mysqli_query($connection, $query);
}

fclose($file);

echo "✅ Data menu berhasil diimpor ulang ke database `erthree_coffee`.";
?>
