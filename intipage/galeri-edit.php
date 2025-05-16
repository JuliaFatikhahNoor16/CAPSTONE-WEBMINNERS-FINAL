<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

$id = (int) $_GET['id'];
$query = "SELECT * FROM galeri WHERE id = $id";
$result = mysqli_query($connection, $query);
$galeri = mysqli_fetch_assoc($result);

if (!$galeri) {
    echo "Konten tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_galeri'])) {
    $judul = mysqli_real_escape_string($connection, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($connection, $_POST['deskripsi']);

    // Cek apakah user upload gambar baru
    if ($_FILES['gambar']['name']) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $uploadDir = '../uploads/';
        $uploadPath = $uploadDir . basename($gambar);

        if (move_uploaded_file($tmp, $uploadPath)) {
            // Hapus gambar lama
            if (file_exists($uploadDir . $galeri['gambar'])) {
                unlink($uploadDir . $galeri['gambar']);
            }

            // Update dengan gambar baru
            $queryUpdate = "UPDATE galeri SET judul='$judul', deskripsi='$deskripsi', gambar='$gambar' WHERE id=$id";
        } else {
            echo "<script>alert('Gagal upload gambar baru.');</script>";
        }
    } else {
        // Update tanpa ganti gambar
        $queryUpdate = "UPDATE galeri SET judul='$judul', deskripsi='$deskripsi' WHERE id=$id";
    }

    if (isset($queryUpdate)) {
        mysqli_query($connection, $queryUpdate);
        header("Location: galeri.php");
        exit;
    }
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Galeri - Erthree Coffee</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="dashboard-page">

<?php include 'sidebar.php'; ?>

<div class="dashboard-main-content">
    <header class="dashboard-header">
        <div class="dashboard-header-info">
            <h1>Edit Konten Galeri</h1>
            <p>Halo, <?= htmlspecialchars($username); ?>! Ubah data konten galeri disini.</p>
        </div>
        <div class="dashboard-header-user">
            <img src="../img/etmin.png" alt="Admin Photo" class="dashboard-admin-avatar">
        </div>
    </header>

    <section class="dashboard-galeri-content">
        <h2>Form Edit Galeri</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="form-galeri">
            <input type="text" name="judul" value="<?= htmlspecialchars($galeri['judul']); ?>" required>
            <textarea name="deskripsi" required><?= htmlspecialchars($galeri['deskripsi']); ?></textarea>
            <p>Foto saat ini:</p>
            <img src="../uploads/<?= $galeri['gambar']; ?>" width="100" style="margin-bottom: 10px;">
            <input type="file" name="gambar" accept="image/*">
            <button type="submit" name="update_galeri" class="btn-primary"><i class="fa fa-save"></i> Update Konten</button>
        </form>
    </section>
</div>

</body>
</html>
