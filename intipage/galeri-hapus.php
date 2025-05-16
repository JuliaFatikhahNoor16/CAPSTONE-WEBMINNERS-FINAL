<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Ambil nama file gambar terlebih dahulu
    $querySelect = "SELECT gambar FROM galeri WHERE id = $id";
    $result = mysqli_query($connection, $querySelect);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $gambarPath = '../uploads/' . $data['gambar'];

        // Hapus gambar dari folder
        if (file_exists($gambarPath)) {
            unlink($gambarPath);
        }

        // Hapus data dari database
        $queryDelete = "DELETE FROM galeri WHERE id = $id";
        mysqli_query($connection, $queryDelete);
    }
}

header("Location: galeri.php");
exit;
