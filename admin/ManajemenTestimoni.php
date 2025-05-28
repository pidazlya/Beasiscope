<?php
require 'db.php';
// Fungsi untuk mengambil data testimoni
function getTestimonials($conn)
{
    $sql = "SELECT * FROM manajemen_testimoni";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Proses untuk menyimpan testimoni baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $universitas = $_POST['universitas'];
    $program = $_POST['program'];
    $tahun = $_POST['tahun'];
    $testimoni = $_POST['testimoni'];
    $status = $_POST['status'];

    // Mengelola file foto (upload)
    // Tentukan direktori upload
    $uploadDir = __DIR__ . "/images/";  // Menggunakan jalur penuh
    $foto = NULL;

    // Cek apakah folder images/ ada, jika tidak, buat folder
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);  // Membuat folder jika belum ada
    }

    if ($_FILES['foto']['name']) {
        $foto = $_FILES['foto']['name'];

        // Cek apakah file sudah ada, dan buat nama unik
        if (file_exists($uploadDir . $foto)) {
            $foto = uniqid() . "_" . $foto;  // Nama file unik agar tidak bentrok
        }

        // Validasi tipe file
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['foto']['type'];

        if (in_array($fileType, $allowedFileTypes)) {
            if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                // Pindahkan file yang diupload
                move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $foto);
            } else {
                echo "Gagal meng-upload gambar! Error: " . $_FILES['foto']['error'];
                exit;
            }
        } else {
            echo "Hanya file gambar yang diperbolehkan!";
            exit;
        }
    }

    // Query untuk memasukkan data testimoni ke database
    $sql = "INSERT INTO manajemen_testimoni (nama, universitas, program, tahun, testimoni, status, foto) 
            VALUES ('$nama', '$universitas', '$program', '$tahun', '$testimoni', '$status', '$foto')";

    if ($conn->query($sql) === TRUE) {
        echo "Testimoni berhasil ditambahkan!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Mengambil data testimoni dari database
$testimonials = getTestimonials($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Beasiswa - Beasiscope Admin</title>
    <link rel="stylesheet" href="ManajemenTestimoni.css">
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
                    <li><a href="Dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="ManajemenBeasiswa.php">Manajemen Beasiswa</a></li>
                    <li><a href="KategoriBeasiswa.php">Kategori Beasiswa</a></li>
                    <li><a href="ManajemenInformasi.php">Manajemen Informasi</a></li>
                    <li><a href="ManajemenTestimoni.php">Manajemen Testimoni</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="dashboard">
                    <h1>Manajemen Testimoni</h1>
                </div>
                <div class="user-info">
                    <span>Admin </span>
                    <a href="Logout.php">Logout</a>
                </div>
            </div>
            <!-- Dashboard Content -->

            <!-- Testimonial Management Content -->
            <div class="testimonial-management">
                <div class="header-actions">
                    <button class="add-btn" onclick="showAddModal()">+ Tambah Testimoni</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Universitas</th>
                            <th>Program</th>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Testimoni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $testimonial): ?>
                        <tr>
                            <td><?php echo $testimonial['nama']; ?></td>
                            <td><?php echo $testimonial['universitas']; ?></td>
                            <td><?php echo $testimonial['program']; ?></td>
                            <td><?php echo $testimonial['tahun']; ?></td>
                            <td>
                                <?php if ($testimonial['status'] == 'aktif'): ?>
                                    <span class="status-badge aktif">Aktif</span>
                                <?php else: ?>
                                    <span class="status-badge nonaktif">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $testimonial['testimoni']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal for Add Testimonial -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <h2>Tambah Testimoni</h2>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" id="nama" name="nama" required>
                    </div>
                    <div class="form-group">
                        <label for="universitas">Universitas</label>
                        <input type="text" id="universitas" name="universitas" required>
                    </div>
                    <div class="form-group">
                        <label for="program">Program</label>
                        <input type="text" id="program" name="program" required>
                    </div>
                    <div class="form-group">
                        <label for="tahun">Tahun</label>
                        <input type="text" id="tahun" name="tahun" required>
                    </div>
                    <div class="form-group">
                        <label for="testimoni">Testimoni</label>
                        <textarea id="testimoni" name="testimoni" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Non Aktif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto</label>
                        <input type="file" id="foto" name="foto">
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="submit" class="add-btn">Simpan</button>
                        <button type="button" class="add-btn" onclick="closeModal()">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addModal').style.display = 'none';
        }
    </script>
</body>

</html>
