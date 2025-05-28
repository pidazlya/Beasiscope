<?php
require 'db.php';
include('sideatas.php');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Beasiscope - Scholarship Finder</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<body>
    <section class="hero" id="home">
        <main class="content">
            <h1>Beasiscope</h1>
            <button class="cta-button"><a href="Portofolio.php">Buat Portofolio Sekarang</a></button>
        </main>
    </section>

    <section>
        <div class="container features-section">
            <div class="feature">
                <img src="./assets/img/icon/user-tie-solid.svg" alt="Portfolio Icon" class="icon">
                <h3><a href="Portofolio.php">Portofolio</a></h3>
                <p>Buat dan tampilkan portofolio akademik serta pencapaian Anda.</p>
            </div>
            <div class="feature">
                <img src="./assets/img/icon/filter-solid.svg" alt="Filter Icon" class="icon">
                <h3><a href="Pilih Beasiswa.php">Filter</a></h3>
                <p>Filter dan pilih beasiswa berdasarkan kriteria yang Anda inginkan.</p>
            </div>
            <div class="feature">
                <img src="./assets/img/icon/star-regular.svg" alt="Recommendation Icon" class="icon">
                <h3><a href="Rekomendasi.php">Rekomendasi</a></h3>
                <p>Dapatkan rekomendasi beasiswa yang sesuai dengan minat dan kualifikasi Anda.</p>
            </div>
            <div class="feature">
                <img src="./assets/img/icon/newspaper-regular.svg" alt="Newsletter Icon" class="icon">
                <h3><a href="Artikel.php">Artikel</a></h3>
                <p>Daftar untuk mendapatkan informasi terbaru tentang beasiswa melalui email.</p>
            </div>
        </div>
    </section>

    <!-- Scholarship Section -->
    <div class="scholarship-section">
        <h2>Testimoni Alumni</h2>
        <div class="scholarship-cards">
            <!-- Card 1 -->
            <div class="card">
                <div class="card-inner">
                    <div class="card-front">
                        <div class="image-wrapper">
                            <img src="./assets/img/3x4 Angel.jpg" alt="Angelita">
                        </div>
                        <h3>Angelita</h3>
                        <p>S1 Universitas Indonesia<br>Beasiswa Indonesia Maju</p>
                    </div>
                    <div class="card-back">
                        <p>Saya sangat bersyukur atas beasiswa ini!</p>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="card">
                <div class="card-inner">
                    <div class="card-front">
                        <div class="image-wrapper">
                            <img src="./assets/img/a2aaa481-cc69-42a8-a96a-40bf669fc02a.jpeg" alt="Rapli Nurhasan">
                        </div>
                        <h3>Rapli Nurhasan</h3>
                        <p>S2 University of Cambridge<br>Beasiswa LPDP</p>
                    </div>
                    <div class="card-back">
                        <p>Pengalaman belajar di Cambridge sangat berharga!</p>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <a href="Testimoni.php" class="cta-button">Baca Cerita Alumni Bersama Beasiscope</a>
    </div>

    <?php include('footer.php'); ?>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginBtn = document.getElementById('loginBtn');
            if (loginBtn) {
                loginBtn.addEventListener('click', function() {
                    window.location.href = 'login.php'; // Arahkan ke login.php
                });
            }
        });
    </script>

</body>

</html>