<?php
require 'db.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data pengguna berdasarkan session user_id
$user_id = $_SESSION['user_id']; // Ambil ID pengguna dari sesi
$sql = "SELECT * FROM pengguna WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
// Cek apakah pengguna ditemukan
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $name = $user['nama'];
    $email = $user['email'];
    $db_password = $user['password'];
    $profile_picture = $user['foto'];
} else {
    echo "Pengguna tidak ditemukan.";
    exit;
}

$sql = "SELECT * FROM portofolio WHERE id_pengguna = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_portof = $stmt->get_result();


// Proses untuk mengubah profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "uploads/profile_pictures/";
        $file_name = basename($_FILES['profile_picture']['name']);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('Hanya gambar dengan format JPG, JPEG, PNG, GIF yang diperbolehkan');</script>";
        }
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $sql_update = "UPDATE pengguna SET nama=?, email=?, foto=? WHERE id=?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("sssi", $nama, $email, $file_name, $user_id);
            if ($stmt->execute()) {
                echo "<script>alert('Profil berhasil diubah!'); window.location.href='profile.php'</script>";
            } else {
                echo "<script>alert('Gagal mengubah profil " . $conn->error . "');</script>";
            }
            $conn->close();
        } else {
            echo "Terjadi kesalahan saat mengupload file.";
        }
    } else {
        $sql_update = "UPDATE pengguna SET nama = ? , email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssi", $nama, $email, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('Profil berhasil diubah!'); window.location.href='profile.php'</script>";
        } else {
            echo "<script>alert('Gagal mengubah profil " . $conn->error . "');</script>";
        }
        $conn->close();
    }
}

$conn->close();
?>

<?php include('sideatas.php'); ?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/profile.css">
    <title>Profil Pengguna</title>
</head>

<body>
    <div class="profile-container">
        <!-- Foto Profil -->
        <img src="<?= ($profile_picture != null) ? "uploads/profile_pictures/" . $profile_picture : "assets/img/no-profile.png"; ?>" alt="Foto Profil" class="profile-pic">
        <p>Nama : <?= htmlspecialchars($name); ?></p>
        <p>Email: <?= htmlspecialchars($email); ?></p>
        <button type="button" class="edit-button"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
        <div class="form-input hidden">
            <form action="" method="post" enctype="multipart/form-data">
                <label for="nama" class="label-edit">Nama:</label>
                <input type="text" id="nama" name="nama" class="input-text" value="<?= $name ?>">
                <label for="email" class="label-edit">Email:</label>
                <input type="text" id="email" name="email" class="input-text" value="<?= $email ?>">
                <label for="profile_picture" class="label-edit">Foto Profil:</label>
                <p><i>* kosongkan bagian ini jika tidak ingin mengubah foto profil</i></p>
                <input type="file" name="profile_picture" accept="image/*" class="w-100">
                <div class="btn-container">
                    <button type="button" class="cancel-button">Batal</button>
                    <button type="submit" class="profile-photo-button">Ubah Profil</button>
                </div>
            </form>
        </div>
    </div>
    <div class="profile-section">
        <div class="content-card">
            <div class="title-card">Ganti Password</div>
            <form action="ganti_password.php" method="post">
                <label for="current_password">Password Lama:</label>
                <input type="password" id="current_password" name="current_password" required>
                <label for="new_password">Password Baru:</label>
                <input type="password" id="new_password" name="new_password" required>
                <button type="submit" class="change-password-button">Ganti Password</button>
            </form>
        </div>
        <div class="content-card">
            <div class="title-card">Portofolio</div>
            <?php if ($result_portof->num_rows > 0) { ?>
                <div class="notif">Anda sudah mengisi portofolio <br /> unduh melalui tombol dibawah ini</div>
                <a href="unduh_portof.php" class="unduh-portofolio-button">
                    <i class="fa-solid fa-download"></i>
                    Unduh Portofolio
                </a>
            <?php } else { ?>
                <div class="warning">Anda belum mengisi portofolio <br /> silahkan isi portofolio terlebih dahulu</div>
                <a href="Portofolio.php" class="unduh-portofolio-button">
                    Isi Portofolio
                </a>
            <?php } ?>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editButton = document.querySelector(".edit-button");
            const cancelButton = document.querySelector(".cancel-button");
            const formInput = document.querySelector(".form-input");

            editButton.addEventListener("click", () => {
                formInput.classList.remove("hidden");
                editButton.style.display = "none";
            });

            cancelButton.addEventListener("click", () => {
                formInput.classList.add("hidden");
                editButton.style.display = "inline-block";
            });
        });
    </script>
</body>

</html>