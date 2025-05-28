
<?php
require 'db.php';
// Ambil data statistik
$total_users_query = "SELECT COUNT(*) AS total FROM pengguna";
$active_scholarships_query = "SELECT COUNT(*) AS total FROM beasiswa WHERE status = 'aktif'";
$applications_query = "SELECT COUNT(*) AS total FROM aplikasi_beasiswa";
$new_registrations_query = "SELECT COUNT(*) AS total FROM pengguna WHERE created_at > NOW() - INTERVAL 1 DAY";

$total_users_result = $conn->query($total_users_query)->fetch_assoc();
$active_scholarships_result = $conn->query($active_scholarships_query)->fetch_assoc();
$applications_result = $conn->query($applications_query)->fetch_assoc();
$new_registrations_result = $conn->query($new_registrations_query)->fetch_assoc();

// Ambil aktivitas terbaru dari berbagai tabel
$recent_activities_query = "
    (SELECT 'pengguna' AS tipe, CONCAT('Pengguna baru ', nama) AS pesan, created_at AS waktu FROM pengguna ORDER BY created_at DESC LIMIT 1)
    UNION
    (SELECT 'beasiswa' AS tipe, 'Data beasiswa baru ditambahkan' AS pesan, created_at AS waktu FROM beasiswa WHERE status = 'aktif' ORDER BY created_at DESC LIMIT 1)
    UNION
    (SELECT 'aplikasi' AS tipe, 'Aplikasi baru ditambahkan' AS pesan, created_at AS waktu FROM aplikasi_beasiswa ORDER BY created_at DESC LIMIT 1)
    ORDER BY waktu DESC LIMIT 4
";

$recent_activities_result = $conn->query($recent_activities_query);

// Mendapatkan halaman aktif
$currentPage = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Beasiscope Admin</title>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="Dashboard.css">

</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>Beasiscope Admin</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="Index.php" class="<?= $currentPage == 'Index.php' ? 'active' : '' ?>">?Index</a></li>
                    <li><a href="ManajemenBeasiswa.php" class="<?= $currentPage == 'ManajemenBeasiswa.php' ? 'active' : '' ?>">Manajemen Beasiswa</a></li>
                    <li><a href="KategoriBeasiswa.php" class="<?= $currentPage == 'KategoriBeasiswa.php' ? 'active' : '' ?>">Kategori Beasiswa</a></li>
                    <li><a href="ManajemenInformasi.php" class="<?= $currentPage == 'ManajemenInformasi.php' ? 'active' : '' ?>">Manajemen Informasi</a></li>
                    <li><a href="ManajemenTestimoni.php" class="<?= $currentPage == 'ManajemenTestimoni.php' ? 'active' : '' ?>">Manajemen Testimoni</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="dashboard">
                    <h1>Dashboard</h1>
                </div>
                <div class="user-info">
                    <span>Admin </span>
                    <a href="Logout.php">Logout</a>
                </div>
            </div>
            <!-- Dashboard Content -->


            <!-- Statistics Cards -->
            <div class="statistics">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Pengguna</h3>
                        <p><?php echo number_format($total_users_result['total']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Beasiswa Aktif</h3>
                        <p><?php echo number_format($active_scholarships_result['total']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Aplikasi</h3>
                        <p><?php echo number_format($applications_result['total']); ?></p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Pendaftar Baru</h3>
                        <p><?php echo number_format($new_registrations_result['total']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="recent-activities">
                <h2>Aktivitas Terbaru</h2>
                <div class="activity-list">
                    <?php if ($recent_activities_result->num_rows > 0) {
                        while ($activity = $recent_activities_result->fetch_assoc()) { ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="<?= $activity['tipe'] == 'pengguna' ? 'fas fa-user' : ($activity['tipe'] == 'beasiswa' ? 'fas fa-graduation-cap' : 'fas fa-clipboard-check') ?>"></i>
                                </div>
                                <div class="activity-details">
                                    <p class="activity-message"><?= $activity['pesan']; ?></p>
                                    <span class="activity-time"><?= date('d M Y, H:i', strtotime($activity['waktu'])); ?></span>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <p>Tidak ada aktivitas terbaru</p>
                    <?php } ?>
                </div>
            </div>
            <div class="quick-actions">
                <h2>Aksi Cepat</h2>
                <div class="action-buttons">
                    <button onclick="window.location.href='ManajemenBeasiswa.php'">
                        <i class="fas fa-plus"></i> Tambah Beasiswa
                    </button>
                    <button onclick="window.location.href='ManajemenInformasi.php'">
                        <i class="fas fa-bullhorn"></i> Buat Pengumuman
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <?php $conn->close(); // Menutup koneksi 
    ?>
</body>

</html>