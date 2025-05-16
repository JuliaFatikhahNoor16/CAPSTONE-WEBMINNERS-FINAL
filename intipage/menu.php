<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';
$username = $_SESSION['username'];

// Daftar nama menu best seller berdasarkan data penjualan
$bestSellerMenus = [
    'signature erthree coffee',
    'americano',
    'butterscoth latte',
    'dark chocolate',
    'lychee tea',
    'cookies n cream',
    'rice beef bulgogi',
    'pempek',
    'mix platter'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_menu'])) {
    $nama     = mysqli_real_escape_string($connection, $_POST['nama']);
    $harga    = (int) $_POST['harga'];
    $kategori = mysqli_real_escape_string($connection, $_POST['kategori']);

    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];

    $uploadDir = '../uploads/';
    $uploadPath = $uploadDir . basename($gambar);

    if (move_uploaded_file($tmp, $uploadPath)) {
        $queryInsert = "INSERT INTO daftar_menu (nama_menu, harga, kategori, gambar) 
                        VALUES ('$nama', $harga, '$kategori', '$gambar')";
        if (!mysqli_query($connection, $queryInsert)) {
            die("Insert Error: " . mysqli_error($connection));
        }
        header("Location: menu.php");
        exit;
    } else {
        echo "<script>alert('Gagal upload gambar');</script>";
    }
}

// Ambil semua data menu
$query = "SELECT * FROM daftar_menu ORDER BY kategori ASC, nama_menu ASC";
$allMenus = mysqli_query($connection, $query);

// Pisahkan best seller dan non-best seller
$bestSellerData = [];
$otherMenuData  = [];

while ($row = mysqli_fetch_assoc($allMenus)) {
    if (in_array(strtolower($row['nama_menu']), array_map('strtolower', $bestSellerMenus))) {
        $bestSellerData[] = $row;
    } else {
        $otherMenuData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Menu - Erthree Coffee</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: 'Segoe UI', sans-serif;
            font-size: 15px;
        }
        .dashboard-table thead {
            background-color: #2c3e50;
            color: white;
        }
        .dashboard-table th, .dashboard-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: middle;
        }
        .dashboard-table img {
            width: 60px;
            height: auto;
            border-radius: 5px;
        }
        .dashboard-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .dashboard-table tr:hover {
            background-color: #f1f1f1;
        }
        .btn-warning, .btn-danger {
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 14px;
            margin-right: 5px;
            display: inline-block;
        }
        .btn-warning {
            background-color: #f1c40f;
            color: #333;
        }
        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
        }
        .form-menu input, .form-menu select {
            display: block;
            margin-bottom: 10px;
            padding: 10px;
            width: 100%;
            max-width: 400px;
        }
        .form-menu button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
        .best-seller {
            color: gold;
            margin-left: 5px;
        }
    </style>
</head>
<body class="dashboard-page">

<?php include 'sidebar.php'; ?>

<div class="dashboard-main-content">
    <header class="dashboard-header">
        <div class="dashboard-header-info">
            <h1>Kelola Konten Menu</h1>
            <p>Halo, <?= htmlspecialchars($username); ?>! Tambah atau ubah menu di sini.</p>
        </div>
        <div class="dashboard-header-user">
            <img src="../img/etmin.png" alt="Admin Photo" class="dashboard-admin-avatar">
        </div>
    </header>

    <section class="dashboard-menu-content">
        <h2>Tambah Menu Baru</h2>
        <form action="" method="POST" enctype="multipart/form-data" class="form-menu">
            <input type="text" name="nama" placeholder="Nama Menu" required>
            <input type="number" name="harga" placeholder="Harga (angka saja)" required>
            <select name="kategori" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="kopi">Kopi</option>
                <option value="non-kopi">Non-Kopi</option>
                <option value="tea">Tea</option>
                <option value="makanan ringan">Makanan Ringan</option>
                <option value="makanan berat">Makanan Berat</option>
            </select>
            <input type="file" name="gambar" accept="image/*" required>
            <button type="submit" name="tambah_menu" class="btn-primary">
                <i class="fa fa-save"></i> Simpan Menu
            </button>
        </form>
        <hr>

        <h2>Daftar Menu</h2>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $combinedMenus = array_merge($bestSellerData, $otherMenuData);
                foreach ($combinedMenus as $row) :
                    $gambarPath = !empty($row['gambar']) ? "../uploads/" . $row['gambar'] : "../img/default.png";
                    $namaMenu = htmlspecialchars($row['nama_menu']);
                    $isBestSeller = in_array(strtolower($row['nama_menu']), array_map('strtolower', $bestSellerMenus));
                ?>
                    <tr>
                        <td><img src="<?= $gambarPath ?>" alt="Foto Menu"></td>
                        <td>
                            <?= $namaMenu ?>
                            <?php if ($isBestSeller): ?>
                                <span class="best-seller" title="Best Seller">&#9733;</span>
                            <?php endif; ?>
                        </td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td><?= ucfirst($row['kategori']); ?></td>
                        <td>
                            <a href="menu-edit.php?id=<?= $row['id']; ?>" class="btn-warning"><i class="fa fa-edit"></i></a>
                            <a href="menu-hapus.php?id=<?= $row['id']; ?>" class="btn-danger" onclick="return confirm('Hapus menu ini?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

</body>
</html>