<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Ambil data gambar
    $query = "SELECT gambar FROM bundling WHERE id = $id";
    $result = mysqli_query($connection, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data) {
        $gambarPath = '../uploads/' . $data['gambar'];
        if (file_exists($gambarPath)) {
            unlink($gambarPath);
        }

        // Hapus data dari database
        $queryDelete = "DELETE FROM bundling WHERE id = $id";
        mysqli_query($connection, $queryDelete);
    }
}

header("Location: bundling.php");
exit;
