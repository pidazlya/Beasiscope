<?php
require 'db.php'; // Koneksi ke database

// Query untuk mengambil data testimoni
$sql = "SELECT * FROM manajemen_testimoni";
$result = $conn->query($sql);

// Proses untuk menyimpan testimoni baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $universitas = $_POST['universitas'];
    $program = $_POST['program'];
    $tahun = $_POST['tahun'];
    $testimoni = $_POST['testimoni'];
    $status = $_POST['status'];

    // Mengelola file foto (upload)
    $foto = NULL;
    if ($_FILES['foto']['name']) {
        // Validasi tipe file
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['foto']['type'];

        if (in_array($fileType, $allowedFileTypes)) {
            $foto = $_FILES['foto']['name'];
            move_uploaded_file($_FILES['foto']['tmp_name'], "images/" . $foto);
        } else {
            echo "Hanya file gambar yang diperbolehkan!";
            exit; // Stop further execution if invalid file type
        }
    }

    $sql = "INSERT INTO manajemen_testimoni (nama, universitas, program, tahun, testimoni, status, foto) 
            VALUES ('$nama', '$universitas', '$program', '$tahun', '$testimoni', '$status', '$foto')";

    if ($conn->query($sql) === TRUE) {
        echo "Testimoni berhasil ditambahkan!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<?php include('sideatas.php'); // Header dari sideatas.php ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni Beasiswa</title>
    <link rel="stylesheet" href="./assets/css/Testimoni.css">
    <script defer src="./assets/js/Testimoni.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    <header class="testimonial-header">
        <h1>CERITA ALUMNI</h1>
    </header>

    <div class="testimoni-section">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Set the file path for images
                $fotoPath = "/beasiscope/admin/images/" . htmlspecialchars($row["foto"]);


                echo '
                    <div class="testimoni-box">
                        <div class="testimoni-img-container">
                            <!-- Check if the photo exists, and display it -->
                            <img src="' . $fotoPath . '" alt="Foto Penerima Beasiswa" class="testimoni-img">
                        </div>
                        <div class="testimoni-text">
                            <h3>' . htmlspecialchars($row["nama"]) . '</h3>
                            <h2 class="role">' . htmlspecialchars($row["universitas"]) . '</h2>
                            <h1 class="role">' . htmlspecialchars($row["program"]) . '</h1>
                            <p>"' . htmlspecialchars($row["testimoni"]) . '"</p>
                        </div>
                    </div>';
            }
        } else {
            echo "<p>Tidak ada testimoni yang tersedia.</p>";
        }
        ?>
    </div>

    <!-- Include Footer -->
    <?php include('footer.php'); ?>
</body>

</html>
