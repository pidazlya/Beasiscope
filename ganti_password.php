<?php 
require 'db.php';

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

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Proses untuk mengganti password
if (isset($_POST['current_password']) && isset($_POST['new_password'])) {
    $current_pass_input = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Verifikasi password lama
    if (password_verify($current_pass_input, $db_password)) {
        // Proses untuk mengubah password di database (password harus di-hash)
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_update_pass = "UPDATE pengguna SET password=? WHERE id=?";
        $stmt_update_pass = $conn->prepare($sql_update_pass);
        $stmt_update_pass->bind_param("si", $hashed_new_password, $user_id);
        $stmt_update_pass->execute();
        echo "<script>alert('Password berhasil diubah!'); window.location.href='profile.php'</script>";
    } else {
        echo "<script>alert('Password lama tidak cocok!'); window.location.href='profile.php'</script>";
    }
}