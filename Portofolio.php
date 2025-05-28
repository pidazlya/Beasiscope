<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Anda harus login terlebih dahulu untuk mengisi portofolio!');
        window.location.href = 'index.php';
    </script>";
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_pengguna = $_SESSION['user_id'];
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
        $no_telepon = trim($_POST['no_telepon'] ?? '');
        $jenjang_pendidikan = $_POST['jenjang_pendidikan'] ?? '';
        $jurusan = trim($_POST['jurusan'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $link_portofolio = trim($_POST['link_portofolio'] ?? '');
        $upload_karya = null;

        // Validate required fields
        if (
            empty($nama_lengkap) || empty($email) || empty($tanggal_lahir) ||
            empty($no_telepon) || empty($jenjang_pendidikan) || empty($jurusan) ||
            empty($deskripsi)
        ) {
            throw new Exception("Semua field wajib diisi kecuali portofolio dan upload karya.");
        }

        // Validate jenjang_pendidikan enum values
        $valid_jenjang = ['SMA', 'Diploma', 'Sarjana', 'Magister', 'Doktor'];
        if (!in_array($jenjang_pendidikan, $valid_jenjang)) {
            throw new Exception("Jenjang pendidikan tidak valid.");
        }

        // Process file upload if exists
        if (isset($_FILES['upload_karya']['tmp_name'])) {
            $target_dir = "uploads/karya/";
            $file_name = basename($_FILES['upload_karya']['name']);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES['upload_karya']['tmp_name'], $target_file)) {
                $upload_karya = $file_name;
            }
        }

        // Prepare SQL statement based on whether there's a file upload
        if ($upload_karya === null) {
            $sql = "INSERT INTO portofolio 
                   (id_pengguna, nama_lengkap, email, tanggal_lahir, no_telepon, 
                    jenjang_pendidikan, jurusan, deskripsi, link_portofolio) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param(
                "issssssss",
                $id_pengguna,
                $nama_lengkap,
                $email,
                $tanggal_lahir,
                $no_telepon,
                $jenjang_pendidikan,
                $jurusan,
                $deskripsi,
                $link_portofolio
            );
        } else {
            $sql = "INSERT INTO portofolio 
                   (id_pengguna, nama_lengkap, email, tanggal_lahir, no_telepon, 
                    jenjang_pendidikan, jurusan, deskripsi, link_portofolio, upload_karya) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param(
                "isssssssss",
                $id_pengguna,
                $nama_lengkap,
                $email,
                $tanggal_lahir,
                $no_telepon,
                $jenjang_pendidikan,
                $jurusan,
                $deskripsi,
                $link_portofolio,
                $upload_karya
            );
        }

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $successMessage = "Data berhasil disimpan! Terima kasih telah mengisi portofolio.";

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>
<?php include('sideatas.php'); ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/Portofolio.css">
    <title>Portofolio Beasiswa</title>
</head>

<body>
    <div class="form-container">
        <div class="form-image">
            <img src="./assets/img/pexels-ron-lach-9829305.jpg" alt="Deskripsi Foto">
        </div>
        <div class="form-input">
            <div class="form-title">Buat Portofolio Beasiswa</div>
            <?php if ($successMessage): ?>
                <p class="success-message"><?php echo $successMessage; ?></p>
            <?php elseif ($errorMessage): ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <form id="portfolioForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <label for="nama_lengkap">Nama Lengkap:</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Masukkan email" required>

                <label for="tanggal_lahir">Tanggal Lahir:</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>

                <label for="no_telepon">No. Telepon:</label>
                <input type="text" id="no_telepon" name="no_telepon" placeholder="Masukkan nomor telepon" required>

                <label for="jenjang_pendidikan">Jenjang Pendidikan:</label>
                <select id="jenjang_pendidikan" name="jenjang_pendidikan" class="border rounded p-2 w-full" required>
                    <option value="">Pilih jenjang pendidikan</option>
                    <option value="SMA">SMA</option>
                    <option value="Diploma">Diploma</option>
                    <option value="Sarjana">Sarjana</option>
                    <option value="Magister">Magister</option>
                    <option value="Doktor">Doktor</option>
                </select>

                <label for="jurusan">Jurusan:</label>
                <input type="text" id="jurusan" name="jurusan" placeholder="Masukkan jurusan" required>

                <label for="deskripsi">Deskripsi Diri atau Pengalaman:</label>
                <textarea id="deskripsi" name="deskripsi" rows="5" placeholder="Tuliskan deskripsi singkat tentang diri Anda, pengalaman, atau motivasi" required></textarea>

                <label for="upload_karya">Unggah Karya (Opsional):</label>
                <input type="file" id="upload_karya" name="upload_karya">

                <label for="link_portofolio">Link Portofolio (Opsional):</label>
                <input type="text" id="link_portofolio" name="link_portofolio" placeholder="Masukkan link portofolio (contoh: LinkedIn, GitHub, dsb.)">

                <input type="submit" value="Kirim Portofolio">
            </form>
        </div>
    </div>
</body>

</html>
<?php include('footer.php'); ?>