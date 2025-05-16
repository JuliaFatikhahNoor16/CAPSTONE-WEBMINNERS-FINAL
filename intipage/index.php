<?php
require_once '../config/koneksi.php';

// Query best seller menu
$menu_query = "SELECT * FROM daftar_menu WHERE LOWER(nama_menu) IN (
    'signature erthree coffee',
    'americano',
    'butterscoth latte',
    'dark chocolate',
    'lychee tea',
    'cookies n cream',
    'rice beef bulgogi',
    'pempek',
    'mix platter'
)";
$menu_result = mysqli_query($connection, $menu_query);

// Query galeri
$queryGaleri = "SELECT * FROM galeri ORDER BY id DESC";
$resultGaleri = mysqli_query($connection, $queryGaleri);

// Query bundling - Mengubah query bundling untuk menampilkan data dari database
$queryBundling = "SELECT * FROM bundling ORDER BY id DESC";
$resultBundling = mysqli_query($connection, $queryBundling);

// Daftar best seller
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Erthree Coffee Space</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .section-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #062241;
            margin-bottom: 30px;
        }

        .menu-container, .bundling-container, .galeri-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .menu-item, .bundling-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            width: 280px;
            text-align: center;
            padding: 20px 15px;
            transition: transform 0.3s ease;
        }

        .menu-item:hover, .bundling-card:hover {
            transform: translateY(-5px);
        }

        .menu-item img, .bundling-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .menu-item h3, .bundling-card h4 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #062241;
        }

        .menu-item p, .bundling-card p {
            margin: 8px 0 0;
            font-weight: bold;
            color: #f1c40f;
        }

        .maps-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 1000px;
            margin: 0 auto 30px;
            text-align: center;
            padding: 20px;
        }

        .maps-card iframe {
            width: 100%;
            height: 300px;
            border-radius: 12px;
            margin-bottom: 15px;
            border: none;
        }

        .maps-card p {
            font-weight: bold;
            color: #062241;
            font-size: 15px;
            margin: 0;
        }

        .galeri-item img {
            width: 100%;
            max-width: 250px;
            height: auto;
            border-radius: 10px;
        }

        /* ULASAN SLIDER - WIDENED */
        .ulasan-slider {
            display: flex;
            overflow: hidden;
            scroll-behavior: smooth;
            width: 100%;
            max-width: 1200px; /* Increased from 900px */
            margin: 0 auto;
            gap: 20px;
        }

        .ulasan-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            flex: 0 0 350px; /* Increased from 280px */
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .ulasan-card:hover {
            transform: translateY(-5px);
        }

        .ulasan-photo {
            width: 120px; /* Increased from 100px */
            height: 120px; /* Increased from 100px */
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 15px;
            border: 4px solid #f1c40f;
        }

        .ulasan-content h4 {
            margin: 0 0 8px;
            color: #062241;
            font-size: 20px; /* Increased from default */
        }

        .stars {
            color: #f1c40f;
            margin-bottom: 12px;
            font-size: 22px; /* Increased from 18px */
        }

        .ulasan-content p {
            font-style: italic;
            color: #555;
            font-size: 16px; /* Increased from default */
            line-height: 1.5;
        }
        
        /* New menu category styling */
        .kategori {
            display: inline-block;
            background-color: #f0f0f0;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 12px;
            margin: 5px 0;
            color: #555;
        }
        
        /* Styling untuk badge bundling */
        .badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #f1c40f;
            color: #062241;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 12px;
        }
        
        .badge.special {
            background-color: #e74c3c;
            color: white;
        }
        
        /* Styling untuk deskripsi bundling */
        .description {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
        
        /* Menambahkan position relative agar badge bisa diposisikan */
        .bundling-card {
            position: relative;
        }
        
        /* Styling untuk subtitle */
        .bundling-subtitle, .bestseller-subtitle {
            text-align: center;
            margin-bottom: 25px;
            color: #555;
        }
    </style>
</head>
<body>

<!-- HEADER -->
<header>
    <div class="container header-flex">
        <div class="logo-title">
            <img src="../img/logo.png" alt="Logo Erthree" class="logo">
            <span class="brand">Erthree Coffee Space</span>
        </div>
        <nav class="nav-icon">
            <div class="nav-item"><a href="index.php"><i class="fas fa-home"></i><span>Home</span></a></div>
            <div class="nav-item"><a href="#"><i class="fas fa-shopping-cart"></i><span>Cart</span></a></div>
            <div class="nav-item"><a href="login.php"><i class="fas fa-user-cog"></i><span>Admin</span></a></div>
        </nav>
    </div>
</header>

<!-- PROFILE -->
<section class="profile" style="background-image: url('../img/bg-about.jpg');">
    <div class="container">
        <h2>Tentang Kami</h2>
        <p>
            Selamat datang di <strong>Erthree Coffee Space</strong> — Terletak di sudut tenang Loa Bakung, Samarinda, Erthree Coffee hadir sebagai hidden gem bagi para pencinta ketenangan dan kenikmatan rasa. Dengan konsep rumah yang hangat dan nyaman, kami ingin menciptakan ruang yang terasa akrab—tempat semua orang bisa merasa pulang.

            Kami menyajikan pilihan kopi, teh, dan berbagai minuman lainnya yang diracik dengan sepenuh hati. Dari seduhan klasik hingga kreasi khas, setiap cangkir membawa cerita dan kehangatan. Lebih dari sekadar tempat ngopi, Erthree Coffee adalah tempat untuk berbagi tawa, obrolan, dan momen berharga.

            Kami percaya bahwa pelayanan adalah bagian dari pengalaman. Itu sebabnya para barista kami selalu siap menyambut dengan senyum dan keramahan, menjadikan setiap kunjungan lebih dari sekadar menikmati minuman—tapi juga merasakan kedekatan.

            Selamat datang di Erthree Coffee. Rumah kecil penuh rasa.
        </p>
        </p>
    </div>
</section>

<!-- MENU BUNDLING - Mengubah bagian ini untuk mengambil data dari database -->
<section class="bundling-section" id="bundling">
    <div class="container">
        <h2 class="section-title">Menu Bundling Spesial</h2>
        <p class="bundling-subtitle">Nikmati penawaran spesial dengan harga lebih hemat dan porsi lebih lengkap!</p>
        
        <div class="bundling-container">
            <?php 
            // Mengecek apakah query bundling berhasil
            if ($resultBundling && mysqli_num_rows($resultBundling) > 0) {
                // Loop untuk menampilkan data bundling dari database
                while ($row = mysqli_fetch_assoc($resultBundling)) :
            ?>
                <div class="bundling-card">
                    <?php if (!empty($row['label'])) : ?>
                        <span class="badge <?= $row['label'] === 'Best Seller' ? 'special' : ''; ?>"><?= htmlspecialchars($row['label']); ?></span>
                    <?php endif; ?>
                    
                    <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama']); ?>">
                    <h4><?= htmlspecialchars($row['nama']); ?></h4>
                    <p>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                    <div class="description"><?= htmlspecialchars($row['deskripsi']); ?></div>
                </div>
            <?php 
                endwhile;
            } else {
                // Jika tidak ada data bundling di database, tampilkan pesan
                echo '<p class="no-data">Tidak ada menu bundling yang tersedia saat ini.</p>';
            }
            ?>
        </div>
    </div>
</section>

<!-- SCRIPT UNTUK ANIMASI TAMBAHAN -->
<script>
    // Tambahkan efek hover pada kartu bundling
    document.addEventListener('DOMContentLoaded', function() {
        const bundlingCards = document.querySelectorAll('.bundling-card');
        
        bundlingCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-12px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>

<!-- MENU BEST -->
<section class="bestseller-section" id="menu">
    <div class="container">
        <h2 class="section-title">Menu Best Seller</h2>
        <p class="bestseller-subtitle">Temukan menu favorit pelanggan kami yang paling banyak digemari</p>
        
        <div class="menu-container">
            <?php while ($row = mysqli_fetch_assoc($menu_result)) : ?>
                <div class="menu-item">
                    <?php if (in_array(strtolower($row['nama_menu']), array_map('strtolower', $bestSellerMenus))) : ?>
                        <span class="menu-badge">Best Seller</span>
                    <?php endif; ?>
                    
                    <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama_menu']); ?>">
                    
                    <h3>
                        <?= htmlspecialchars($row['nama_menu']); ?>
                        <?php if (in_array(strtolower($row['nama_menu']), array_map('strtolower', $bestSellerMenus))) : ?>
                            <span class="star-icon">★</span>
                        <?php endif; ?>
                    </h3>
                    
                    <span class="kategori"><?= htmlspecialchars($row['kategori']); ?></span>
                    <p>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- SCRIPT UNTUK ANIMASI TAMBAHAN -->
<script>
    // Tambahkan efek muncul untuk menu items
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.menu-item');
        
        // Fungsi untuk mengecek apakah elemen dalam viewport
        function isInViewport(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.bottom >= 0
            );
        }
        
        // Tambahkan class untuk animasi saat scroll
        function checkScroll() {
            menuItems.forEach((item, index) => {
                if(isInViewport(item)) {
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, index * 100); // Delay berdasarkan urutan
                }
            });
        }
        
        // Set style awal
        menuItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            item.style.transition = 'all 0.5s ease';
        });
        
        // Jalankan sekali saat load
        checkScroll();
        
        // Tambahkan event listener untuk scroll
        window.addEventListener('scroll', checkScroll);
    });
</script>

<!-- LOKASI & KONTAK CARD -->
<section class="lokasi">
    <div class="container">
        <h2 class="section-title">Lokasi Kami</h2>
        <div class="lokasi-kontak-maps">
            <div class="lokasi-kontak">
                <h3>Informasi Kontak</h3>
                <p><i class="fas fa-map-marker-alt"></i> No.27 Blok AG, Jl. Jakarta, Loa Bakung, Samarinda</p>
                <p><i class="fab fa-instagram"></i> <a href="https://www.instagram.com/erthree.coffee/" target="_blank">@erthree.coffee</a></p>
                <p><i class="fab fa-whatsapp"></i> <a href="https://api.whatsapp.com/send?phone=628170076694" target="_blank">Pesan & Reservasi</a></p>
            </div>
            <div class="lokasi-maps">
                <iframe 
                    src="https://www.google.com/maps?q=-0.5276229,117.0916482&hl=es;z=16&amp;output=embed"
                    allowfullscreen
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>

<!-- ULASAN KAMI - WIDENED -->
<section class="ulasan-kami">
  <div class="container">
    <h2 class="section-title">Ulasan Kami</h2>
    <div class="ulasan-slider">
      <!-- Card Ulasan 1 -->
      <div class="ulasan-card">
        <img src="../uploads/user1.jpg" alt="Foto User 1" class="ulasan-photo" />
        <div class="ulasan-content">
          <h4>Rina Dewi</h4>
          <div class="stars">
            &#9733;&#9733;&#9733;&#9733;&#9733;
          </div>
          <p>"Tempat yang nyaman dan kopi yang luar biasa. Pelayanan sangat ramah dan suasananya mendukung untuk bekerja maupun berkumpul bersama teman."</p>
        </div>
      </div>
      <!-- Card Ulasan 2 -->
      <div class="ulasan-card">
        <img src="../uploads/user2.jpg" alt="Foto User 2" class="ulasan-photo" />
        <div class="ulasan-content">
          <h4>Agus Santoso</h4>
          <div class="stars">
            &#9733;&#9733;&#9733;&#9733;&#9733;
          </div>
          <p>"Saya sangat menyukai suasana di sini, cocok untuk bekerja atau santai. Menu-menunya selalu fresh dan nikmat, terutama kopi signaturenya!"</p>
        </div>
      </div>
      <!-- Card Ulasan 3 -->
      <div class="ulasan-card">
        <img src="../uploads/user3.jpg" alt="Foto User 3" class="ulasan-photo" />
        <div class="ulasan-content">
          <h4>Dewi Lestari</h4>
          <div class="stars">
            &#9733;&#9733;&#9733;&#9733;&#9733;
          </div>
          <p>"Kopi dan makanan lezat, tempatnya bersih dan nyaman. Sudah menjadi tempat favorit untuk meeting dan bertemu klien. Sangat direkomendasikan!"</p>
        </div>
      </div>
      <!-- Card Ulasan 4 -->
      <div class="ulasan-card">
        <img src="../uploads/user4.jpg" alt="Foto User 4" class="ulasan-photo" />
        <div class="ulasan-content">
          <h4>Joko Prasetyo</h4>
          <div class="stars">
            &#9733;&#9733;&#9733;&#9733;&#9733;
          </div>
          <p>"Tempat favorit saya untuk ngopi dan bertemu teman-teman. Pempek dan Mix Platter di sini sangat enak dan cocok untuk dimakan sambil menikmati kopi."</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>&copy; <?= date('Y'); ?> Erthree Coffee Space. All Rights Reserved.</p>
    </div>
</footer>

<script>
const slider = document.querySelector('.ulasan-slider');
let scrollAmount = 0;
const slideWidth = 370; // Lebar card + gap kira-kira - increased from 300

function autoSlide() {
    if(scrollAmount >= slider.scrollWidth - slider.clientWidth) {
    scrollAmount = 0;
    slider.scrollTo({ left: scrollAmount, behavior: 'smooth' });
    } else {
    scrollAmount += slideWidth;
    slider.scrollTo({ left: scrollAmount, behavior: 'smooth' });
    }
}

let slideInterval = setInterval(autoSlide, 3000);

slider.addEventListener('mouseenter', () => clearInterval(slideInterval));
slider.addEventListener('mouseleave', () => slideInterval = setInterval(autoSlide, 3000));
</script>

</body>
</html>