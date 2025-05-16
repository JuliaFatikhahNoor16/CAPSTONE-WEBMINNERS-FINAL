<?php
$connection = new mysqli('localhost', 'root', '', 'erthree_coffee');

// Cek koneksi
if ($connection->connect_error) {
    die("Koneksi gagal: " . $connection->connect_error);
}
?>
