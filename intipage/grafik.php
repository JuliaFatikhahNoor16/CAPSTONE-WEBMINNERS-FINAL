<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/koneksi.php';

$query = "SELECT * FROM bundling";
$result = mysqli_query($connection, $query);

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Grafik Penjualan Erthree Coffee Space</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
                <h1>Lihat Grafik Data</h1>
                <p>Halo, <?= htmlspecialchars($username); ?>! </p>
            </div>
            <div class="dashboard-header-user">
                <img src="../img/etmin.png" alt="Admin Photo" class="dashboard-admin-avatar">
            </div>
        </header>
        <!-- Grafik 1: Penjualan Harian -->
        <div class="chart-card">
            <h2>Trend Penjualan Harian Jan-Mar</h2>
            <div class="chart-container" id="grafikTriwulan-container">
                <canvas id="grafikTriwulan"></canvas>
                <div class="chart-x-axis-label">Hari ke-</div>
            </div>
        </div>
        <!-- Grafik 2: Pie Kategori Produk -->
        <div class="chart-card chart-small">
            <h2>Distribusi Kategori Produk</h2>
            <div class="chart-container" id="pieKategoriProduk-container">
                <canvas id="pieKategoriProduk"></canvas>
            </div>
        </div>

        <!-- Grafik 3: Produk dengan Penjualan Tertinggi dan Terendah-->
        <div class="chart-container">
            <div class="chart-card">
                <h2>Menu dengan Penjualan Tertinggi</h2>
                <div class="chart-wrapper">
                    <canvas id="produkTerlaris"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h2>Menu dengan Penjualan Terendah</h2>
                <div class="chart-wrapper">
                    <canvas id="grafikKurangLaris"></canvas>
                </div>
            </div>
        </div>

        <script>
            // Grafik 1: Penjualan Harian
            fetch('grafik-penjualan-bulanan.php')
                .then(response => response.json())
                .then(data => {
                    const jan = data.filter(d => d.Month === 1);
                    const feb = data.filter(d => d.Month === 2);
                    const mar = data.filter(d => d.Month === 3);
                    const maxHari = Math.max(jan.length, feb.length, mar.length);
                    const labels = Array.from({
                        length: maxHari
                    }, (_, i) => `Hari ke-${i + 1}`);
                    const ctx = document.getElementById('grafikTriwulan').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                    label: 'Januari',
                                    data: jan.map(d => d.Total),
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    tension: 0.3,
                                    fill: false
                                },
                                {
                                    label: 'Februari',
                                    data: feb.map(d => d.Total),
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    tension: 0.3,
                                    fill: false
                                },
                                {
                                    label: 'Maret',
                                    data: mar.map(d => d.Total),
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    tension: 0.3,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    callbacks: {
                                        title: function(items) {
                                            return items[0].dataset.label + ' - ' + items[0].label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Hari ke-'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Total Penjualan (Rp)'
                                    }
                                }
                            }
                        }
                    });
                });

            // Grafik 2: Pie Kategori Produk
            async function initPieKategoriProduk() {
                try {
                    const res = await fetch('grafik-kategori-produk.php');
                    const data = await res.json();
                    const total = data.reduce((sum, d) => sum + d.Total, 0);
                    new Chart(document.getElementById("pieKategoriProduk"), {
                        type: 'pie',
                        data: {
                            labels: data.map(d => d.Category),
                            datasets: [{
                                data: data.map(d => d.Total),
                                backgroundColor: ['#4e73df', '#1cc88a', '#cc3300 ', '#f6c23e', '#e74a3b', '#858796']
                            }]
                        },
                        options: {
                            responsive: false,
                            maintainAspectRatio: false,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Komposisi Kategori Produk (%)'
                                },
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => {
                                            const val = ctx.raw;
                                            const percent = ((val / total) * 100).toFixed(1);
                                            return `${percent}% (Rp ${val.toLocaleString('id-ID')})`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Gagal memuat pie chart:', error);
                }
            }

            initPieKategoriProduk();

            // Tambahkan pengecekan data sebelum membuat grafik
            async function initChart(canvasId, title, endpoint, color) {
                try {
                    const response = await fetch(`grafik-produk.php?type=${endpoint}`);
                    const data = await response.json();

                    console.log(`Data ${endpoint}:`, data); // Debugging

                    if (!data || data.length === 0 || data.every(item => item.value === 0)) {
                        console.error(`Data tidak valid untuk ${canvasId}`);
                        document.getElementById(canvasId).style.display = 'none';
                        return;
                    }

                    const ctx = document.getElementById(canvasId).getContext('2d');

                    // Hapus chart sebelumnya jika ada
                    if (window[canvasId + 'Chart']) {
                        window[canvasId + 'Chart'].destroy();
                    }

                    window[canvasId + 'Chart'] = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(d => d.product),
                            datasets: [{
                                label: 'Jumlah Penjualan',
                                data: data.map(d => d.value),
                                backgroundColor: color,
                                borderColor: color.replace('0.6', '1'),
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            indexAxis: 'y',
                            plugins: {
                                title: {
                                    display: true,
                                    text: title
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx => `${ctx.raw} item terjual`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah Penjualan'
                                    }
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error(`Error ${canvasId}:`, error);
                }
            }

            // Inisialisasi kedua grafik saat halaman dimuat
            window.addEventListener('DOMContentLoaded', () => {
                initChart('produkTerlaris', '10 Produk dengan Penjualan Tertinggi', 'highest', 'rgba(32, 201, 151, 0.6)');
                initChart('grafikKurangLaris', '10 Produk dengan Penjualan Terendah', 'lowest', 'rgba(231, 74, 59, 0.6)');
            });
        </script>
        </script>
</body>

</html>