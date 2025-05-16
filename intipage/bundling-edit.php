<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

$id = (int) $_GET['id'];
$query = "SELECT * FROM bundling WHERE id = $id";
$result = mysqli_query($connection, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data tidak ditemukan!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($connection, $_POST['nama']);
    $harga = (int) $_POST['harga'];
    $deskripsi = mysqli_real_escape_string($connection, $_POST['deskripsi']);

    $gambarBaru = $_FILES['gambar']['name'];
    $gambarLama = $data['gambar'];
    $uploadDir = '../uploads/';
    $uploadPath = $uploadDir . basename($gambarBaru);

    if (!empty($gambarBaru)) {
        // Hapus gambar lama
        $gambarLamaPath = $uploadDir . $gambarLama;
        if (file_exists($gambarLamaPath)) {
            unlink($gambarLamaPath);
        }

        // Upload gambar baru
        move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadPath);
        $gambarFinal = $gambarBaru;
    } else {
        $gambarFinal = $gambarLama;
    }

    $queryUpdate = "UPDATE bundling SET nama = '$nama', harga = $harga, deskripsi = '$deskripsi', gambar = '$gambarFinal' WHERE id = $id";
    mysqli_query($connection, $queryUpdate);
    header("Location: bundling.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Bundling - Erthree Coffee</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="dashboard-page">

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<div class="dashboard-main-content">
    <!-- Header -->
    <header class="dashboard-header">
        <div class="dashboard-header-info">
            <h1>Edit Bundling</h1>
            <p>Perbarui informasi bundling di bawah ini.</p>
        </div>
    </header>

    <!-- Form Edit -->
    <section class="dashboard-menu-content"> <!-- gunakan class seragam -->
        <form action="" method="POST" enctype="multipart/form-data" class="form-menu">
            <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required>
            <input type="number" name="harga" value="<?= $data['harga'] ?>" required>
            <textarea name="deskripsi" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>

            <p>Gambar saat ini:</p>
            <img src="../uploads/<?= htmlspecialchars($data['gambar']); ?>" width="100"><br><br>
            <input type="file" name="gambar" accept="image/*">
            
            <button type="submit" class="btn-primary"><i class="fa fa-save"></i> Simpan Perubahan</button>
        </form>
    </section>
</div>

</body>
</html>
