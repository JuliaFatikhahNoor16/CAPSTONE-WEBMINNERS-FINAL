<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID tidak valid.";
    exit;
}

$id = (int) $_GET['id'];

// Ambil data menu terlebih dahulu (untuk hapus gambar)
$querySelect = "SELECT gambar FROM daftar_menu WHERE id = $id";
$result = mysqli_query($connection, $querySelect);

if ($row = mysqli_fetch_assoc($result)) {
    $gambar = $row['gambar'];

    // Hapus dari database
    $queryDelete = "DELETE FROM daftar_menu WHERE id = $id";
    if (mysqli_query($connection, $queryDelete)) {
        // Hapus file gambar jika ada
        $gambarPath = '../uploads/' . $gambar;
        if (file_exists($gambarPath)) {
            unlink($gambarPath);
        }

        header("Location: menu.php");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($connection);
    }
} else {
    echo "Menu tidak ditemukan.";
}
?>
