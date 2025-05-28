<?php
require 'db.php';

function fetchArtikel($pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM informasi WHERE status = 'Terbit'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$artikels = fetchArtikel($pdo);
?>

<?php include('sideatas.php'); ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/Artikel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Artikel Beasiswa</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Include Google Sign-In library -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script> <!-- Tambahkan jsPDF -->
</head>

<body>
    <!-- Hero Section Start (Home Page) -->
    <section class="hero" id="home">
        <main class="content">
            <h1>Selamat Datang di Beasiscope</h1>
            <p>Temukan berbagai peluang beasiswa yang sesuai dengan tujuan akademis Anda.</p>
            <p>Kami menghubungkan siswa dengan beasiswa terbaik dari institusi terpercaya.</p>
        </main>
    </section>
    <!-- Hero Section End -->

    <!-- News Section Start (Newsletter) -->
    <section class="news-section" id="news">
        <div class="title-artikel">Berita terbaru</div>

        <div class="news-grid">
            <?php foreach ($artikels as $artikel): ?>
                <div class="news-item">
                    <div class="gallery-caption">
                        <img src="../admin/informasi/<?= ($artikel['gambar']) ?>" alt="<?php echo $artikel['judul']; ?>">
                        <p><?php echo date('d M Y', strtotime($artikel['tanggal'])); ?></p>
                        <h3><?php echo $artikel['judul']; ?></h3>
                        <p><?php echo substr($artikel['konten'], 0, 100); ?>...</p>
                        <a href="ArtikelDetail.php?id=<?php echo $artikel['id']; ?>" class="read-more">Baca Selengkapnya</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <!-- News Section End -->

    <!-- Popup and Overlay for News Section -->
    <div class="popup">
        <span class="popup-close">&times;</span>
        <h3>Popup Title</h3>
        <div class="popup-content">
            <p>Details about the scholarship or news item go here...</p>
        </div>
    </div>

    <div class="overlay"></div>

    <?php include('footer.php'); ?>

    <!-- JavaScript -->
    <script src="./assets/js/newsletter.js"></script>
    <script>
        feather.replace();

        // Popup functionality
        const newsItems = document.querySelectorAll('.news-item');
        const popup = document.querySelector('.popup');
        const overlay = document.querySelector('.overlay');
        const popupClose = document.querySelector('.popup-close');

        newsItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                popup.style.display = 'block';
                overlay.style.display = 'block';
            });
        });

        popupClose.addEventListener('click', () => {
            popup.style.display = 'none';
            overlay.style.display = 'none';
        });

        overlay.addEventListener('click', () => {
            popup.style.display = 'none';
            overlay.style.display = 'none';
        });
    </script>
</body>

</html>