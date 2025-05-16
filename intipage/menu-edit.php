<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

if (!isset($_GET['id'])) {
    die("ID menu tidak ditemukan!");
}

$id = (int)$_GET['id'];
$query = "SELECT * FROM daftar_menu WHERE id = $id";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query error: " . mysqli_error($connection));
}

$menu = mysqli_fetch_assoc($result);

if (!$menu || !isset($menu['nama_menu'], $menu['harga'], $menu['deskripsi'])) {
    die("Data menu tidak ditemukan atau tidak lengkap.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ganti nama_menu jadi nama
    $nama = mysqli_real_escape_string($connection, $_POST['nama']);
    $harga = (int)$_POST['harga'];
    $deskripsi = mysqli_real_escape_string($connection, $_POST['deskripsi']);

    if (!empty($_FILES['gambar']['name'])) {
        $gambar = $_FILES['gambar']['name'];
        $tmp = $_FILES['gambar']['tmp_name'];
        $uploadDir = '../uploads/';
        $uploadPath = $uploadDir . basename($gambar);

        if (move_uploaded_file($tmp, $uploadPath)) {
            if (!empty($menu['gambar']) && file_exists($uploadDir . $menu['gambar'])) {
                unlink($uploadDir . $menu['gambar']);
            }
            $queryUpdate = "UPDATE daftar_menu SET nama_menu='$nama', harga=$harga, deskripsi='$deskripsi', gambar='$gambar' WHERE id=$id";
        } else {
            echo "<script>alert('Gagal upload gambar');</script>";
        }
    } else {
        $queryUpdate = "UPDATE daftar_menu SET nama_menu='$nama', harga=$harga, deskripsi='$deskripsi' WHERE id=$id";
    }

    if (isset($queryUpdate) && mysqli_query($connection, $queryUpdate)) {
        header('Location: menu.php');
        exit;
    }
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu - Erthree Coffee</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="dashboard-page">

<?php include 'sidebar.php'; ?>

<div class="dashboard-main-content">
    <header class="dashboard-header">
        <div class="dashboard-header-info">
            <h1>Edit Menu</h1>
            <p>Halo, <?= htmlspecialchars($username); ?>! Ubah data menu berikut.</p>
        </div>
        <div class="dashboard-header-user">
            <img src="../img/etmin.png" alt="Admin Photo" class="dashboard-admin-avatar">
        </div>
    </header>

    <section class="dashboard-menu-content">
        <h2>Form Edit Menu</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="form-menu">

            <p><strong>Nama Menu:</strong></p>
            <input type="text" name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? $menu['nama_menu']) ?>" required>

            <p><strong>Harga:</strong></p>
            <input type="number" name="harga" value="<?= htmlspecialchars($_POST['harga'] ?? $menu['harga']) ?>" required>

            <p><strong>Deskripsi:</strong></p>
            <textarea name="deskripsi" required><?= htmlspecialchars($_POST['deskripsi'] ?? $menu['deskripsi']) ?></textarea>

            <p><strong>Gambar Saat Ini:</strong></p>
            <?php if (!empty($menu['gambar']) && file_exists('../uploads/' . $menu['gambar'])): ?>
                <img src="../uploads/<?= htmlspecialchars($menu['gambar']); ?>" width="100" alt="Gambar Menu">
            <?php else: ?>
                <p><em>Tidak ada gambar.</em></p>
            <?php endif; ?>

            <p><strong>Ganti Gambar:</strong></p>
            <input type="file" name="gambar" accept="image/*">

            <br><br>
            <button type="submit" class="btn-primary"><i class="fa fa-save"></i> Update Menu</button>
        </form>
    </section>
</div>

</body>
</html>
