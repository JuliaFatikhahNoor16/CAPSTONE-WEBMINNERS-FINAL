<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Erthree Coffee</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="dashboard-page">

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="dashboard-main-content">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="dashboard-header-info">
                <h1>Welcome, <?= htmlspecialchars($username); ?>!</h1>
                <p>Selamat datang di dashboard admin Erthree Coffee.</p>
            </div>
            <div class="dashboard-header-user">
                <img src="../img/etmin.png" alt="Admin Photo" class="dashboard-admin-avatar">
            </div>
        </header>

        <!-- Dashboard Stats -->
        <section class="dashboard-stats">
            <div class="dashboard-stats-box">
                <h3>Total Produk</h3>
                <p>63</p>
            </div>
            <div class="dashboard-stats-box">
                <h3>Total Pesanan</h3>
                <p>276.623</p>
            </div>
            <div class="dashboard-stats-box">
                <h3>Total Pendapatan</h3>
                <p>Rp. 215,938,000</p>
            </div>
        </section>

        <!-- Grafik Penjualan Quantity -->
        <section class="card mt-4 p-3">
            <h5 class="mb-3">Grafik Penjualan (Quantity Per Bulan)</h5>
            <canvas id="grafikQuantity" height="150"></canvas>
        </section>
    </div>

    <!-- Script untuk render grafik -->
    <script>
        fetch("grafik-data.php")
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.tanggal);
                const values = data.map(item => item.quantity);

                const ctx = document.getElementById("grafikQuantity").getContext("2d");

                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Jumlah Barang Terjual",
                            data: values,
                            backgroundColor: "rgba(255, 159, 64, 0.5)",
                            borderColor: "rgba(255, 159, 64, 1)",
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.raw + ' pcs';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Kuantitas (pcs)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Bulan'
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error("Gagal memuat data:", error));
    </script>

</body>
</html>
